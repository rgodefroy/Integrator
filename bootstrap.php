<?php
namespace ThalosGears\Integrator;

// Load libraries
$loader = require_once( __DIR__ .'/vendor/autoload.php');
$loader->add('ThalosGears', __DIR__ . '/src/');

use Monolog\Handler\StreamHandler;

use Silex\Provider\TwigServiceProvider;

use Nicl\Silex\MarkdownServiceProvider;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

$app = new \Silex\Application();

// Debug mode
$app['debug'] = true;

// Error logger before loading config
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler(__DIR__ . '/log/error.log', Logger::ERROR));

// Load config
try {
    $yml = __DIR__ . '/config/config.yml';
    $logger->debug('loading config file', array('path' => $yml));
    $config = Yaml::parse($yml);
    $logger->debug('config', $config);
} catch (ParseException $e) {
    $logger->err('config', array($e->getMessage()));
    throw $e;
}

// Load config paths, server and process
$app['paths']= $config['paths'];
$app['server']= $config['server'];
$app['process']= $config['process'];

// Logger
if (!isset($config['loggers']['default']['file']['path']))
    $config['loggers']['default']['file']['path'] = __DIR__ . '/log/integrator.log';
    $logger->loadHandlers($config['loggers']['default']);
    $app['logger'] = $app->share(function() use ($logger) {
        return $logger;
    });

// Twig
$app->register(new TwigServiceProvider(), array(
    'twig.path' => array(__DIR__ . '/views'),
));

// Markdown parser
$app->register(new MarkdownServiceProvider());

// Load projects
$projects = array();
foreach (glob($app['paths']['projects'].'/*.yml') as $yml) {
    try {
        $logger->debug('loading project file', array('path' => $yml));
        $project = Yaml::parse($yml);
        $logger->debug('project', $project);
        $projects[basename($yml, '.yml')] = $project;
    } catch (ParseException $e) {
        $logger->err('project', array($e->getMessage()));
        throw $e;
    }
}
// Create projects container
$app['projects'] = $projects;

// Connect nodes
foreach ($app['projects'] as $project => $options) {        
    foreach ($options['nodes'] as $node => $options) {
        $app["ssh.$project.$node"] = $app->share(function () use ($node, $options) {
            return new Node($node, $options['host'], $options['port'], $options['user'], $options['password']);
        });
    }
}

// Mailer
foreach ($app['projects'] as $project => $options) {
	$app["notifier.$project"]= new swiftMailNotifier($options['notifications']['email']['recipients'], $app);
}
