<?php

namespace pr1\api\processor;

/**
 * Core payment processor class
 *
 * @author Thurairajah Thujeevan
 */
class Processor {

    protected $paymentProvider;

    public function __construct(Processable $provider) {
        $this->paymentProvider = $provider;
    }

    public function process($data) {
        $this->paymentProvider->process($data);
    }

}
