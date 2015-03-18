<?php
/**
 * config.php
 * application's base config file
 *
 * @author Thujeevan Thurairajah
 */

namespace pr1;

use Exception;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\Yaml\Yaml;

define('ROOT',  dirname(__DIR__));

require ROOT . '/vendor/autoload.php';

# bootstrap
$app = new Application();

$app['debug'] = TRUE;

# twig provider
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Views',
));

# url generator
$app->register(new UrlGeneratorServiceProvider());

# session
$app->register(new SessionServiceProvider(), array(
    'session.storage.options' => array(
        'name' => 'pr1_session',
    )
));

try {
    $file = __DIR__ . '/confs.yml';
    if (!is_readable($file)) {
        throw new Exception('Failed to load configuration file');
    }
    $conf = file_get_contents($file);
    $configs = Yaml::parse($conf);
} catch (Exception $exc) {
    die('Unable to load configuration file, please verify');
}

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => $configs['db']
));

$app['ppConf'] = function() use ($configs){
    return $configs['paypal'];
};

$app['btConf'] = function() use ($configs){
    return $configs['braintree'];
};

# binding the routes
require 'routes.php';

return $app;
