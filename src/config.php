<?php
/**
 * config.php
 * application's base config file
 *
 * @author Thujeevan Thurairajah
 */

namespace pr1;

use Exception;
use pr1\api\providers\BTProvider;
use pr1\api\providers\PPProvider;
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

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'pr1',
        'user' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ],
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


$app['ppConf'] = function($app) use ($configs){
    return $configs['paypal'];
};

$app['btConf'] = function($app) use ($configs){
    return $configs['braintree'];
};

$app['provider.pp'] = $app->share(function($app){
    return new PPProvider($app['ppConf'], $app['db']);
});

$app['provider.bt'] = $app->share(function($app){
    return new BTProvider($app['btConf'], $app['db']);
});

# binding the routes
require 'routes.php';

return $app;
