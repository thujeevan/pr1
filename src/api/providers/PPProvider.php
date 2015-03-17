<?php

namespace pr1\api\providers;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use pr1\api\processor\Processable;

/**
 * Paypal payment process provider
 *
 * @author Thurairajah Thujeevan 
 */
class PPProvider implements Processable {

    protected $apiContext;

    public function __construct($conf) {
        $this->setupContext($conf);
    }

    protected function setupContext($conf) {
        $creds = $conf['credentials'];
        $context = new ApiContext(
            new OAuthTokenCredential($creds[0], $creds[0])
        );
        $context->setConfig($conf['configs']);

        $this->apiContext = $context;
    }

    public function process($data) {
        
    }

}
