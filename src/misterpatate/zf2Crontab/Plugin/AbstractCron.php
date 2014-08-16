<?php
/**
 * Crontab Module on top of Zendframework 2
 *
 * @link https://github.com/misterpatate/zf2-crontab for the canonical source repository
 * @copyright Copyright (c) 2014 Cyril Rouillon
 * @license http://opensource.org/licenses/MIT MIT
 * @package misterpatate\zf2-crontab
 */
namespace misterpatate\zf2Crontab\Plugin;

use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractCron
{

    protected $ServiceLocator;
    protected $options;

    /**
     * Plugin Initialization
     *
     * @param ServiceLocatorInterface
     * @param null|array
     * @return null
     */
    public function initPlugin($ServiceLocator,$options=null){
        $this->ServiceLocator = $ServiceLocator;
        $this->options = $options;
    }

    /**
     * get the Service Manager
     *
     * @return ServiceLocatorInterface
     */
    protected  function getServiceLocator(){
        return $this->ServiceLocator;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}