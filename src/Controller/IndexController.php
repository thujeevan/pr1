<?php

namespace pr1\Controller;

use pr1\api\gateway\Gateway;
use pr1\api\processor\Processor;
use pr1\api\Util;
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
        
        $recentOrders = Processor::getRecentOrders($app['db']);

        $messages = $app['session']->getFlashBag()->get('messages');
        return $app['twig']->render('home.html.twig', [
                    'currencies' => $currencies,
                    'months' => $months,
                    'years' => $years,
                    'messages' => $messages,
                    'recentOrders' => $recentOrders,
        ]);
    }

    public function process(Application $app, Request $request) {
        if(!$request->isMethod('post')){
            return $app->redirect($app['url_generator']->generate('index.index'));
        }
        
        $postParams = $request->request->all();

        $processor = new Processor($app['db'], [ 'paypal' => $app['ppConf'], 'braintree' => $app['btConf']]);
        $provider = $processor->preProcess($postParams);        

        if (FALSE === $provider) {
            $app['session']->getFlashBag()->add('messages', ['danger', $processor->getMessage()]);
            return $app->redirect($app['url_generator']->generate('index.index'));
        }

        $gateway = new Gateway($provider);
        $result = $gateway->process($postParams);

        if (FALSE === $result) {
            $app['session']->getFlashBag()->add('messages', ['danger', $gateway->getMessage()]);
            return $app->redirect($app['url_generator']->generate('index.index'));
        }

        $formatted = $processor->postProcess($result);

        $app['session']->getFlashBag()->add('messages', ['info', $formatted]);
        return $app->redirect($app['url_generator']->generate('index.index'));
    }

    public function connect(Application $app) {
        $index = $app['controllers_factory'];
        $index->match("/", 'pr1\Controller\IndexController::index')->bind("index.index");
        $index->match("/process", 'pr1\Controller\IndexController::process')->bind("index.process");

        $index->before(function (Request $request) use ($app) {
            $post = $request->request->all();
            Util::sanitize($post);
            $request->request->replace($post);
        });

        return $index;
    }
}
