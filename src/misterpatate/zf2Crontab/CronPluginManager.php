<?php
/**
 * Crontab Module on top of Zendframework 2
 *
 * @link https://github.com/misterpatate/zf2-crontab for the canonical source repository
 * @copyright Copyright (c) 2014 Cyril Rouillon
 * @license http://opensource.org/licenses/MIT MIT
 * @package misterpatate\zf2-crontab
 */
namespace misterpatate\zf2Crontab;

use Zend\I18n\Validator\Alnum;
use Zend\ServiceManager\AbstractPluginManager;
use RuntimeException;

class CronPluginManager extends AbstractPluginManager
{
    public $CronCommands;
    public $CronPlugins;

    protected  $invokableClasses;

    protected $IndexPath;
    protected $tmpPath;

    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Plugin\AbstractCron) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Plugin\CronInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }

    /**
     * Add a new cron job command
     *
     * @param  string $canonicalName
     * @param  string $command
     * @return null
     */
    public function addCronCommand($canonicalName,$command){
        $this->CronCommands[$canonicalName] = $command;
    }


    /**
     * Add a new cron job plugin
     *
     * @param  string $canonicalName
     * @param  string $cronPlugin
     * @return null
     */
    public function addCronPlugin($canonicalName,$cronPlugin){
        $this->CronPlugins[$canonicalName] = $cronPlugin;
        $this->invokableClasses[$canonicalName] = $cronPlugin['invokableClasse'];
    }


    /**
     * @param mixed $IndexPath
     */
    public function setIndexPath($IndexPath)
    {
        $this->IndexPath = $IndexPath;
    }

    /**
     * @return mixed
     */
    public function getIndexPath()
    {
        return $this->IndexPath;
    }
    /**
     * @param mixed $tmpPath
     */
    public function setTmpPath($tmpPath)
    {
        $this->tmpPath = $tmpPath;
    }

    /**
     * @return mixed
     */
    public function getTmpPath()
    {
        return $this->tmpPath;
    }

    /**
     * Get the cron table
     *
     * @return array
     */
    public function getListeCrontab(){
        exec("crontab -l",$output);
        return $output;
    }

    /**
     * Remove all the entry of the cron table
     *
     * @return array
     */
    public function removeCrontab(){
        exec("crontab -r",$output);
        return $output;
    }

    /**
     * Initialization of the cron table
     *
     * @return array
     * @throws RuntimeException
     */
    public function cronInit(){
        $this->removeCrontab();

        $tmpFile = $this->getTmpPath()."cronTmp";

        if (!$fp = fopen($tmpFile,"w")) {
            throw new RuntimeException('Error writing to file '.$tmpFile);
        }

        if($this->CronCommands){
            foreach($this->CronCommands as $canonicalName => $command){
                fputs($fp, "# ".$canonicalName."\n");
                fputs($fp, $command['time']." ".$command['command']."\n");
            }
        }

        if($this->CronPlugins){
            foreach($this->CronPlugins as $canonicalName => $plugin){
                fputs($fp, "\n## ".$canonicalName."\n");
                fputs($fp, "################################\n");
                if(isset($plugin['methods'])){
                    foreach($plugin['methods'] as $methodeName => $methodConfig){
                        if($methodConfig['is_active']){
                            fputs($fp, $methodConfig['time']." php ".$this->getIndexPath()." cronExecute ".$canonicalName." ".$methodeName."\n");
                        }
                    }
                }
            }
        }

        fclose($fp);

        exec("crontab ".$tmpFile,$output);

        unlink($tmpFile);

        return $output;
    }

    /**
     * Lunch the method of the given Plugin
     *
     * @param string $pluginName
     * @param string $methodName
     * @return null
     * @throws \Exception
     */
    public function cronExecute($pluginName,$methodName){

        $validator = new Alnum();

        if($validator->isValid($pluginName)){
            $Plugin = $this->get($pluginName);

            $methodName .= "Action";

            $options=null;
            $configuration = $this->getServiceLocator()->get('Config');

            if(isset($configuration['CronManager']['plugins'][$pluginName]['options'])){
                $options = $configuration['CronManager']['plugins'][$pluginName]['options'];
            }

            $Plugin->initPlugin($this->getServiceLocator(),$options);

            if(!method_exists($Plugin,$methodName)){
                throw new \Exception("$methodName doesn't exist in $pluginName class");
            }

            try{
                call_user_func( array($Plugin,$methodName) );
            } catch(\Exception $e){
                throw $e;
            }
        }else{
            throw new \Exception("Plugin name is not valid, must be AlphaNumeric Value");
        }

    }
}
?>
