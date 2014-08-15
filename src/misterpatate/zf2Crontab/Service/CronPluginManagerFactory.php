<?php
/**
 * Crontab Module on top of Zendframework 2
 *
 * @link https://github.com/misterpatate/zf2-crontab for the canonical source repository
 * @copyright Copyright (c) 2014 Cyril Rouillon
 * @license http://opensource.org/licenses/MIT MIT
 * @package misterpatate\zf2-crontab
 */
namespace misterpatate\zf2Crontab\Service;

use misterpatate\zf2Crontab\CronPluginManager;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class CronPluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'misterpatate\zf2Crontab\CronPluginManager';

    public function createService(ServiceLocatorInterface $serviceLocator){
        /** @var $plugins CronPluginManager */
        $plugins = parent::createService($serviceLocator);

        $configuration = $serviceLocator->get('Config');

        if(isset($configuration['CronManager'])){

            $plugins->setIndexPath($configuration['CronManager']['IndexPath']);
            $plugins->setTmpPath($configuration['CronManager']['tmpPath']);

            foreach($configuration['CronManager']['commands'] as $canonicalName => $command){
                if($command['is_active']){
                    $plugins->addCronCommand($canonicalName,$command);
                }
            }
            foreach($configuration['CronManager']['plugins'] as $canonicalName => $cronPlugin){
                if($cronPlugin['is_active']){
                    $plugins->addCronPlugin($canonicalName,$cronPlugin);
                }
            }

        }else{
            throw new \Exception('No CronManager entry in module.config');
        }

        return $plugins;
    }
}
?>