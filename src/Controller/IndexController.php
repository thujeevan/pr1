<?php

namespace pr1\Controller;

use Exception;
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

        $messages = $app['session']->getFlashBag()->get('messages');
        return $app['twig']->render('home.html.twig', ['currencies' => $currencies, 'months' => $months, 'years' => $years, 'messages' => $messages]);
    }

    public function process(Application $app, Request $request) {
        $postParams = $request->request->all();

        $cardNo = $postParams['card-number'];
        $currency = $postParams['currency'];

        if (!(strlen($cardNo) && Util::isValidCardNo($cardNo))) {
            $app['session']->getFlashBag()->add('messages', ['danger', 'Please provide valid card number']);
            return $app->redirect($app['url_generator']->generate('index.index'));
        }

        $currencies = ['usd', 'eur', 'thb', 'hkd', 'sgd', 'aud'];
        if (!in_array($currency, $currencies)) {
            $app['session']->getFlashBag()->add('messages', ['danger', 'Please provide valid currency format']);
            return $app->redirect($app['url_generator']->generate('index.index'));
        }

        $provider = NULL;

        if (Util::isAmex($cardNo) && $currency !== 'usd') {
            $app['session']->getFlashBag()->add('messages', ['danger',  'AMEX is only possible with USD' ]);
            return $app->redirect($app['url_generator']->generate('index.index'));
        }

        if (Util::isAmex($cardNo) || in_array($currency, ['usd', 'eur', 'aud'])) {
            $provider = $app['provider.pp'];
        } else {
            $provider = $app['provider.bt'];
        }

        $processor = new Processor($provider);
        
        try {
            $processor->process($postParams);
        } catch (Exception $ex) {
            $app['session']->getFlashBag()->add('messages',['danger', 'Error in processing transaction, please check the details and retry' ]);
            return $app->redirect($app['url_generator']->generate('index.index'));
        }

        $app['session']->getFlashBag()->add('messages',['success',  'Successfully processed transaction' ]);
        return $app->redirect($app['url_generator']->generate('index.index'));
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
