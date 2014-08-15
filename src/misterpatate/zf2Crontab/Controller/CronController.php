<?php
/**
 * Crontab Module on top of Zendframework 2
 *
 * @link https://github.com/misterpatate/zf2-crontab for the canonical source repository
 * @copyright Copyright (c) 2014 Cyril Rouillon
 * @license http://opensource.org/licenses/MIT MIT
 * @package misterpatate\zf2-crontab
 */
namespace misterpatate\zf2Crontab\Controller;

use misterpatate\zf2Crontab\CronPluginManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use RuntimeException;

class CronController extends AbstractActionController {

    /** @var  $CronManager CronPluginManager*/
    private $CronManager;
    private function getCronManager(){
        if(!$this->CronManager){
            $this->CronManager = $this->getServiceLocator()->get('CronManager');
        }
        return $this->CronManager;
    }

    public function crontabAction(){

        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }

        $liste = $request->getParam('l');
        $remove = $request->getParam('r');

        $output = array();
        $CronManager = $this->getCronManager();

        if($liste){
            $output = $CronManager->getListeCrontab();
        }

        if($remove){
            $output = $CronManager->removeCrontab();
        }

        foreach($output as $line){
            echo $line."\n";
        }
    }

    public function cronInitAction(){
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }

        $CronManager = $this->getCronManager();

        $CronManager->cronInit();

    }

    public function cronExecuteAction(){
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }

        $pluginName = $request->getParam('pluginName');
        $methodName = $request->getParam('methodName');
        $CronManager = $this->getCronManager();
        $CronManager->cronExecute($pluginName,$methodName);
    }
}