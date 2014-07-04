<?php
namespace ThalosGears\Integrator;

//Verify php config
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require_once __DIR__ . '/../bootstrap.php';

//Home
$app->get('/', function(\Silex\Application $app) 
{
    $projects = $app['projects'];
    $integrators = array();
    foreach ($projects as $id => $project) {
        $executions = glob($app['paths']['data'].'/'.$id.'_*');
        if (count($executions)) {
            $integrators[$id] = Integrator::load(end($executions));
        }
    }
    return $app['twig']->render('list.twig', array(
        'integrators' => $integrators,
    ));
});

//History
$app->get('/history', function(\Silex\Application $app) 
{
    $integrators = array();
        //Get all results
        $executions = glob($app['paths']['data'].'/*');
        //Sort by date
        array_multisort(
        array_map( 'filemtime', $executions ),
            SORT_NUMERIC,
            SORT_DESC,
        $executions
        );
        //Just diplays the last 100
        if (count($executions)) {
            for ($i = 0; $i <= count($executions)-1 && $i <= 100; $i++) {
            $integrators[] = Integrator::load($executions[$i]);
        }
    }
    return $app['twig']->render('history.twig', array(
        'integrators' => $integrators,
    ));
});

//Action history
$app->get('/history/{id}/{action}', function(\Silex\Application $app, $id, $action) 
{
    $integrators = array();
    $executions = glob($app['paths']['data'].'/'.$id.'_*_'.$action.'_*.dat');
    //sort all results by date 
    array_multisort(
        array_map('filemtime', $executions),
            SORT_NUMERIC,
            SORT_DESC,
        $executions
    );
    if (count($executions)) {
        for ($i = 0; $i <= count($executions)-1 && $i <= 100; $i++) {
            $integrators[] = Integrator::load($executions[$i]);
        }
    }
    return $app['twig']->render('historyaction.twig', array(
        'id' => $id,
        'action' => $action,
        'integrators' => $integrators,
    ));
});

//View
$app->get('/view/{id}', function(\Silex\Application $app, $id) 
{
    // Load the project with its id
    $project = $app['projects'][$id];
    //Load its tasks
    $tasks = array();
    foreach ($project['actions'] as $action => $actionTasks) {
        foreach ($actionTasks as $name => $task) {
            $tasks[$action][$name] = new Task($name, $app['ssh.'.$id.'.'.$task['node']], $task['commands']);
        }
    }
    //Create integrator for the project ans return it in twig
    $integrator = new Integrator($id, $project['name'], $project['description'], $project['nodes'], $tasks);
    return $app['twig']->render('view.twig', array(
        'integrator' => $integrator,
    ));
});

//Run action
$app->get('/run/{id}/{action}', function(\Silex\Application $app, $id, $action) 
{   //Call run function
    $integrator = Controller::run($app, $id, $action);
    // Return results
    return $app['twig']->render('run.twig', array(
        'integrator' => $integrator,
    ));
});

//Loader
$app->get('/load/{file}', function(\Silex\Application $app, $file) 
{
    $integrator = Integrator::load($app['paths']['data'].'/'.$file);
    //return the result
    return $app['twig']->render('run.twig', array(
        'integrator' => $integrator,
    ));
});

//Readme
$app->get('/readme', function(\Silex\Application $app) 
{
    return $app['twig']->render('readme.twig', array(
        'readme' => file_get_contents(__DIR__ .'/../README.md'),
    ));
});

$app['flash'] = false;

$app->run();
