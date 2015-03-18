<?php

namespace pr1\api\providers;

use Exception;
use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use pr1\api\Util;

/**
 * Paypal payment process provider
 *
 * @author Thurairajah Thujeevan 
 */
class PPProvider extends Base {

    protected $message;
    protected $apiContext;

    public function __construct($conf) {
        $this->setupContext($conf);
    }

    protected function setupContext($conf) {
        $creds = $conf['credentials'];
        $context = new ApiContext(
            new OAuthTokenCredential($creds[0], $creds[1])
        );
        $context->setConfig($conf['configs']);

        $this->apiContext = $context;
    }

    /**
     * Function which process the payment 
     * 
     * @param mixed $data data to be provided for the transaction
     * @return string String representation of the result
     */
    public function process($data) {
        $type = Util::getCardType($data['card-number']);
        
        $card = new CreditCard();
        $card->setType($type)
                ->setNumber(preg_replace("/\D|\s/", "", $data['card-number']))
                ->setExpireMonth($data['expiry-month'])
                ->setExpireYear("20".$data['expiry-year'])
                ->setCvv2($data['cvv'])
                ->setFirstName($data['card-holder-name'])
                ->setLastName($data['card-holder-name']);
        
        $fundingInstrument = new FundingInstrument();
        $fundingInstrument->setCreditCard($card);

        $payer = new Payer();
        $payer->setPaymentMethod("credit_card");
        $payer->setFundingInstruments(array($fundingInstrument));

        $amount = new Amount();
        $amount->setCurrency(strtoupper($data['currency']));
        $amount->setTotal($data['price']);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("creating a direct payment with credit card");

        $payment = new Payment();
        $payment->setIntent("sale");
        $payment->setPayer($payer);
        $payment->setTransactions(array($transaction));
        
        try {
            $payment->create($this->apiContext);

            $fields = [
                'payment_id' => $payment->getId(),
                'payment_provider' => 'paypal',
                'intent' => 'sale',
                'payment_method' => 'credit_card',
                'state' => $payment->getState(),
                'amount' => $data['price'],
                'currency' => strtoupper($data['currency']),
                'description' => 'direct payment with credit card',
                'created_time' => date('Y-m-d H:i:s', strtotime($payment->getCreateTime())),
            ];
            return $fields;
        } catch (Exception $exc) {
            $this->message = 'Error in processing transaction, please check the details and retry';
            return FALSE;
        }
    }

    public function getMessage(){
        return $this->message;
    }
}
