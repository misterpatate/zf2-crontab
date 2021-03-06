zf2-crontab
===========

Task Manager for Zend 2 Project with cron

                | `misterpatate\zf2-crontab`
----------------|----------
**Version**     | 1.0-alpha1
**Authors**     | Cyril Rouillon <cyril@misterpatate.fr>
**License**     | [MIT](http://opensource.org/licenses/MIT)


## Overview

In most application you need to execute task at a given time. This module allow you to manage cron jobs 
directly inside your Zend 2 application.
Cron Jobs could be shell commands or class method

## Features

- Shell command execution
- PHP method execution (with Zend ServiceManager and configuration file)
- Console API to manage and execute jobs

## Installation

1 - clone the module with "Download ZIP" button into your modules directory

2 - Load the zf2 module, edit your `config/application.config.php` file:

```php
'modules' => array(
	'misterpatate\zf2Crontab',
)
```

## Requirement
- This Module use the shell command crontab you need to have this command in your system (Linux,BSD...)
- The Module erase the cron table of the user, if you need to schedule other tasks you need to use the /etc/cron/... files
- Every commands are executed with the PHP user rights
- be careful ;-)

## Configuration

The config is located in the config/cronManager.config.php.
For the first configuration, use the *.sample file for a good starting point.

```php
    'CronManager' => array(
        'IndexPath' => '/var/www/yourwebsite/public/index.php',
        'tmpPath' => '/var/www/yourwebsite/data/tmp/',
        'commands' => array(
            'vide_tmp' => array(
                'is_active' => true,
                'command' => 'rm -Rf /var/www/yourwebsite/data/tmp/*',
                'time' => '@hourly'
            )
        ),
        'plugins' => array(
            'demo' => array(
                'is_active' => true,
                'invokableClasse' => 'misterpatate\zf2Crontab\Plugin\Demo\Demo',
                'options' => array(
                    'param' => true
                ),
                'methods' => array(
                    'testMeth' => array(
                        'is_active' => true,
                        'time' => '*/3 * * * *'
                    )
                )
            )
        )
    )
```

- **IndexPath** : the path of your index.php application file
    Used to interact with your application form command line. ( php /var/www/yourwebsite/public/index.php consoleRoute ).

- **tmpPath** : The path of the temporary file to create crontable file ( PHP user need to have write right).

- **commands** : See Commands section.

- **plugins** : See Plugins section.

# Jobs

## Commands
The commands jobs is like shell cron jobs, you simply set the time and shell command to execute.

```php
'commands' => array(
    'emptyTmp' => array(
        'is_active' => true,
        'command' => 'rm -Rf /var/www/yourwebsite/data/tmp/*',
        'time' => '@hourly'
    )
    'otherCommand' => array(.....
),
```

- **emptyTmp** is the name of your custom command.

- **is_active** : if set to true the command will be include inside the cron table.
- **command** : shell command to execute.
- **time** : cron style schedule time (see cron man for more).

## Plugins
A plugin job is used to schedule execution of a PHP method.

```php
'plugins' => array(
    'demo' => array(
        'is_active' => true,
        'invokableClasse' => 'misterpatate\zf2Crontab\Plugin\Demo\Demo',
        'options' => array(
            'param' => true
        ),
        'methods' => array(
            'testMeth' => array(
                'is_active' => true,
                'time' => '*/3 * * * *'
            )
        )
    )
)
```
The plugin system use the ZF2 PluginManager system but all you have to do is to add class and configure it inside this config file.

- **demo** is the canonical name of your job, you must put an Alpha/Numeric value here.

- **is_active** : if set to true the command will be include inside the cron table.
- **invokableClasse** : the NAMESPACE+Name of your plugin Class.
- **options** : an array of custom options accessible from your Class during execution.
- **methods** : this section contain the method of the class will be executed by cron job
    - **testMeth** : its the name of you method WITHOUT the "Action" suffix
        - **is_active** : if set to true the command will be include inside the cron table.
        - **time** : cron style schedule time (see cron man for more).

## Plugin Class

To create your plugin class you just need to create a directory inside Plugin Dir. and create a class inside like in the example.

```php
<?php
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

```

You must extend the AbstractCron class.
For security reason you must use the suffix "Action" at the end of every method executed by cron job
In this class you have access to the ZF2 service manager with *$this->getServiceLocator()* and option array with *$this->getOptions()*.

## Lunch the scheduler

To commit any change inside of the configuration file you need to execute this command inside of the console :

```shell
$ php /var/www/website/public/index.php cronInit
```

## Crontab API

You can use the CronManager Service to perform some little task with crontab

```php
$CronManager = $this->getServiceLocator()->get('CronManager');

// list actual user cron table
$output = $CronManager->getListeCrontab();

if($output){
    foreach($output as $line){
        echo $line;
    }
}

// Erase user cron table
$CronManager->removeCrontab();

// Initialize user cron table with the CronManager config file
$CronManager->cronInit();
```

With the CronManager service you can access to a plugin and execute task directly.

In this example we use a plugin referenced in the CronManager config file under the 'UserManagment' name and we lunch the EmptyUselessProfileAction() method.
```php
$CronManager = $this->getServiceLocator()->get('CronManager');

$UserManagmentCronPlugin = $CronManager->get('UserManagment');

$UserManagmentCronPlugin->EmptyUselessProfileAction();

$PluginOption = $UserManagmentCronPlugin->getOptions();

```