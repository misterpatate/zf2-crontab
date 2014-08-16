<?php
/**
 * Crontab Module on top of Zendframework 2
 *
 * @link https://github.com/misterpatate/zf2-crontab for the canonical source repository
 * @copyright Copyright (c) 2014 Cyril Rouillon
 * @license http://opensource.org/licenses/MIT MIT
 * @package misterpatate\zf2-crontab
 */
namespace misterpatate\zf2Crontab\Plugin\Demo;
// Test

use misterpatate\zf2Crontab\Plugin\AbstractCron;

class Demo extends AbstractCron
{
    public function executeAction(){
        $UserTable = $this->getServiceLocator()->get('User\Model\UserTable');
        $liste_user = $UserTable->getUserList();
    }

    public function testMethAction(){
       exec("date >> /var/www/yourwebsite/data/tmp/date.txt");
    }

}
