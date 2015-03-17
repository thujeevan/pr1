<?php

namespace pr1\api\providers;

use Braintree_Configuration;
use Braintree_Transaction;

/**
 * Braintree payment process provider
 *
 * @author Thurairajah Thujeevan
 */
class BTProvider extends Base {
    
    protected $conf;
    protected $db;

    public function __construct($conf, $db) {
        $this->conf = $conf;
        $this->db = $db;
        $this->setupContext($conf);
    }
    
    protected function setupContext($conf) {
        Braintree_Configuration::environment($conf['credentials']['environment']);
        Braintree_Configuration::merchantId($conf['credentials']['merchantId']);
        Braintree_Configuration::publicKey($conf['credentials']['publicKey']);
        Braintree_Configuration::privateKey($conf['credentials']['privateKey']);
    }

    public function process($data) {
        $result = Braintree_Transaction::sale(
                        array(
                            'amount' => $data['price'],
                            'merchantAccountId' => $this->conf['merchant_accounts'][$data['currency']],
                            'creditCard' => array(
                                'number' => preg_replace("/\D|\s/", "", $data['card-number']),
                                'expirationMonth' => $data['expiry-month'],
                                'expirationYear' => $data['expiry-year'],
                                'cvv' => $data['cvv']
                            ),
                            'customer' => array(
                                'firstName' => $data['card-holder-name']
                            ),
                            "options" => array(
                                "submitForSettlement" => true
                            )
                        )
        );

        if ($result->success) {
            // TODO : persist data 
            return "Transaction success! \n Transaction Id: " . $result->transaction->id;
        } else if ($result->transaction) {
            return FALSE;
        } else {
            return FALSE;
        }
    }

}
