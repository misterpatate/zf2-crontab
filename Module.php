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

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        $moduleConfig = include __DIR__ . '/config/module.config.php';
        $cronManagerConfig = include __DIR__ . '/config/cronManager.config.php';
        return array_merge($moduleConfig,$cronManagerConfig);
    }

}
?>