#!/usr/bin/php
<?php
namespace ThalosGears\Integrator;

    /**
     * integrator.php allows to execute Integrator in command line
     *
     * @param  array   $argv        command arguments
     * @return string  $status      done on success, or error on failure
     *
     * 
     */

if (!is_callable('ThalosGears\\Integrator\\ConsoleController::'.$argv[1])) {

echo"    
This script allows to execute Integrator application in command line

    Usage :
        
        Run an action:
        $argv[0] run <project_id> <action> <revision>
        @return:
        Integrator: <file>
        Integrator: <status>
        
        Load an execution:
        $argv[0] load <file>
        @return:
        print <file> 
  ";    
exit;
}

require_once __DIR__ . '/../bootstrap.php';

// Short the array by one element 
array_shift($argv);
$cmd = array_shift($argv);
call_user_func('ThalosGears\\Integrator\\ConsoleController::'.$cmd, $app, $argv);

class ConsoleController
{
    static function run($app, $parameters)
    {
        list($id, $action, $revision) = $parameters;
        $integrator = Controller::run($app, basename($id), $action, $revision);
        echo 'integrator: '.basename($integrator->getFileName($app['paths']['data']))."\n";
        echo 'status: '.$integrator->status."\n";
    }

    static function load($app, $parameters)
    {
        list($file) = $parameters;
        $integrator = Integrator::load($app['paths']['data'].'/'.$file);
        var_dump($integrator);
    }
}
