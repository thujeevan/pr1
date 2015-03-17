<?php

namespace pr1\Controller;

use pr1\Controller\Base\AbstractController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController {

    public function index(Application $app) {
        $currencies = ['usd', 'eur', 'thb', 'hkd', 'sgd', 'aud'];
        $months = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'];
        $years = range(15, 25);

        return $app['twig']->render('home.html.twig', ['currencies' => $currencies, 'months' => $months, 'years' => $years]);
    }

    public function process(Application $app, Request $request) {
        $postParams = $request->request->all();
    }

    public function connect(Application $app) {
        $index = $app['controllers_factory'];
        $index->match("/", 'pr1\Controller\IndexController::index')->bind("index.index");
        $index->match("/process", 'pr1\Controller\IndexController::process')->bind("index.process");

        $index->before(function (Request $request) use ($app) {
            $post = $request->request->all();
            self::sanitize($post);
            $request->request->replace($post);
        });

        return $index;
    }

    public static function sanitize(array &$arr, $filter = FILTER_SANITIZE_STRING, $options = FILTER_FLAG_NO_ENCODE_QUOTES) {
        array_walk_recursive($arr, function(&$value, $key) use ($filter, $options) {
            $value = filter_var(trim($value), $filter, $options);
        });
    }

}
