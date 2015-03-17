<?php

namespace pr1\api\providers;

use pr1\api\processor\Processable;

/**
 * Braintree payment process provider
 *
 * @author Thurairajah Thujeevan
 */
class BTProvider implements Processable {

    protected $apiContext;

    public function __construct($conf) {
        $this->apiContext = $apiContext;
    }

    public function process($data) {
        
    }

}
