<?php
/**
 * Crontab Module on top of Zendframework 2
 *
 * @link https://github.com/misterpatate/zf2-crontab for the canonical source repository
 * @copyright Copyright (c) 2014 Cyril Rouillon
 * @license http://opensource.org/licenses/MIT MIT
 * @package misterpatate\zf2-crontab
 */
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'crontab' => array(
                    'options' => array(
                        'route'    => 'crontab [-l|-r]',
                        'defaults' => array(
                            '__NAMESPACE__' => 'misterpatate\zf2Crontab\Controller',
                            'controller'    => 'Cron',
                            'action'        => 'crontab',
                        )
                    )
                ),
                'cronInit' => array(
                    'options' => array(
                        'route'    => 'cronInit',
                        'defaults' => array(
                            '__NAMESPACE__' => 'misterpatate\zf2Crontab\Controller',
                            'controller'    => 'Cron',
                            'action'        => 'cronInit',
                        )
                    )
                ),
                'cronExecute' => array(
                    'options' => array(
                        'route'    => 'cronExecute <pluginName> <methodName>',
                        'defaults' => array(
                            '__NAMESPACE__' => 'misterpatate\zf2Crontab\Controller',
                            'controller'    => 'Cron',
                            'action'        => 'cronExecute',
                        )
                    )
                ),
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'CronManager' => 'misterpatate\zf2Crontab\Service\CronPluginManagerFactory'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'misterpatate\zf2Crontab\Controller\Cron' => 'misterpatate\zf2Crontab\Controller\CronController',
        ),
    ),
);
