<?php

namespace pr1\api\gateway;

/**
 * Gateway
 *
 * @author Thurairajah Thujeevan 
 */
class Gateway {

    protected $messages;
    protected $provider;

    public function __construct(Processable $provider) {
        $this->provider = $provider;
    }

    public function useProvider(Processable $provider) {
        $this->provider = $provider;
        return $this;
    }

    public function process($data) {
        $result = $this->provider->process($data);
        $this->message = $this->provider->getMessage();
        return $result;
    }

    public function getMessage() {
        return $this->message;
    }

}
