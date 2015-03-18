<?php

namespace pr1\api\processor;

use pr1\api\gateway\Gateway;
use pr1\api\providers\BTProvider;
use pr1\api\providers\PPProvider;
use pr1\api\Util;

/**
 * payment processor class
 *
 * @author Thurairajah Thujeevan
 */
class Processor {

    const TABLE_NAME = 'pr1orders';
    
    protected $message;
    protected $db;
    protected $configs;
    protected $provider;

    public function __construct($db, $configs) {
        $this->db = $db;
        $this->configs = $configs;
    }

    public function preProcess($data) {
        $cardNo = $data['card-number'];
        $currency = $data['currency'];

        if (!(strlen($cardNo) && Util::isValidCardNo($cardNo))) {
            $this->message = 'Please provide valid card number';
            return FALSE;
        }

        $currencies = ['usd', 'eur', 'thb', 'hkd', 'sgd', 'aud'];
        if (!in_array($currency, $currencies)) {
            $this->message = 'Please provide valid currency format';
            return FALSE;
        }

        if (Util::isAmex($cardNo) && $currency !== 'usd') {
            $this->message = 'AMEX is only possible with USD';
            return FALSE;
        }

        if (Util::isAmex($cardNo) || in_array($currency, ['usd', 'eur', 'aud'])) {
            $this->provider = new PPProvider($this->configs['paypal']);
        } else {
            $this->provider = new BTProvider($this->configs['braintree']);
        }

        return $this->provider;
    }
    
    public function postProcess($result) {
        $this->writeToDb($result);
        return "Payment completed for Id: {$result['payment_id']} \n Payment status: {$result['state']}";
    }

    public static function getRecentOrders($db, $limit = 30) {
        $recentOrders = $db->fetchAll('select * from pr1orders order by created_time desc limit ' . $limit);
        return $recentOrders;
    }

    public function getMessage() {
        return $this->message;
    }        
    
    public function writeToDb($fields) {
        try {
            $rows = $this->db->insert(self::TABLE_NAME, $fields);
        } catch (Exception $ex) {
            $this->message = 'Writing to DB failed';
        }
    }

}
