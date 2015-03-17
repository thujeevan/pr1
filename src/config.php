<?php
/**
 * config.php
 * application's base config file
 *
 * @author Thujeevan Thurairajah
 */

namespace pr1;

use SebastianBergmann\RecursionContext\Exception;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

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

$app->before(function(Request $request) use ($app){
});

$app->error(function (Exception $e, $code) use ($app) {
    
});

# binding the routes
require 'routes.php';

return $app;
