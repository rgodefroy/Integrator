<?php
namespace ThalosGears\Integrator;

class Controller
{
    public static function run($app, $id, $action, $revision = 'current')
    {
        // Send a SIGTERM if there is an other execution and not running with PHP built in test server
        $pid = file_get_contents('/tmp/integrator_run_'.$id.'_'.$action.'.pid');
        if (false !== strpos(`ps uh --pid $pid`, $app['process']) && php_sapi_name() != 'cli-server') {
            posix_kill($pid, SIGTERM);
        }
        // Save the current PID in the temp file
        file_put_contents('/tmp/integrator_run_'.$id.'_'.$action.'.pid', getmypid());
        
        // Load the project
        $project = $app['projects'][$id];
        //Create all project's tasks
        $tasks = array();
        
        foreach ($project['actions'][$action] as $name => $task) {
            $tasks[$action][$name] = new Task($name, $app['ssh.'.$id.'.'.$task['node']], $task['commands']);
        }
        
        // Create a new Integrator with the project
        $integrator = new Integrator($id, $project['name'], $project['description'], $project['nodes'], $tasks);
        
        // Run the action
        $integrator->run($action, $app['paths']['data'], $revision);
        $app["notifier.$id"]->notify($integrator);
        
        // Return results
        return $integrator;
    }
}
