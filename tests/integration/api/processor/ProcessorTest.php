<?php
namespace pr1\Test\api\providers;

use Doctrine\DBAL\DriverManager;
use PHPUnit_Framework_TestCase;
use pr1\api\processor\Processor;
use pr1\api\providers\BTProvider;
use pr1\api\providers\PPProvider;
use Symfony\Component\Yaml\Yaml;

/**
 * ProcessorTest
 *
 * @author Thurairajah Thujeevan 
 */
class ProcessorTest extends PHPUnit_Framework_TestCase {
    protected $db;
    protected $processor;

    public function setUp() {
        $confs = Yaml::parse(file_get_contents(dirname(dirname(dirname(dirname(__DIR__))))."/src/confs.yml"));
        $this->db = $db = DriverManager::getConnection($confs['db']);
        
        $this->processor = new Processor($this->db, $confs);
    }
    
    public function testProcessInvalidCardFailure() {
        $data = [
            'fullname' => '',
            'price' => '',
            'currency' => 'usd',
            'card-holder-name' => '',
            'card-number' => '',
            'expiry-month' => '',
            'expiry-year' => '',
            'cvv' => '',
        ];
        
        $result = $this->processor->preProcess($data);
        $this->assertFalse($result);
        $this->assertEquals("Please provide valid card number", $this->processor->getMessage());
    }
    
    public function testProcessInvalidCurrencySuccess() {
        $data = [
            'fullname' => '',
            'price' => '',
            'currency' => '',
            'card-holder-name' => '',
            'card-number' => '4032031924794992',
            'expiry-month' => '',
            'expiry-year' => '',
            'cvv' => '',
        ];
        
        $result = $this->processor->preProcess($data);
        $this->assertFalse($result);
        $this->assertEquals("Please provide valid currency format", $this->processor->getMessage());
    }

    public function testProcessAmexNonUsd(){
        $data = [
            'fullname' => '',
            'price' => '',
            'currency' => 'aud',
            'card-holder-name' => '',
            'card-number' => '3782 822463 10005',
            'expiry-month' => '',
            'expiry-year' => '',
            'cvv' => '',
        ];
        
        $result = $this->processor->preProcess($data);
        $this->assertFalse($result);
        $this->assertEquals("AMEX is only possible with USD", $this->processor->getMessage());
    }
    
    public function testProcessPPProvider(){
        $data = [
            'fullname' => '',
            'price' => '',
            'currency' => 'usd',
            'card-holder-name' => '',
            'card-number' => '4032031924794992',
            'expiry-month' => '',
            'expiry-year' => '',
            'cvv' => '',
        ];
        
        $result = $this->processor->preProcess($data);
        $this->assertTrue($result instanceof PPProvider);
    }
    
    public function testProcessBTProvider(){
        $data = [
            'fullname' => '',
            'price' => '',
            'currency' => 'thb',
            'card-holder-name' => '',
            'card-number' => '4032031924794992',
            'expiry-month' => '',
            'expiry-year' => '',
            'cvv' => '',
        ];
        
        $result = $this->processor->preProcess($data);
        $this->assertTrue($result instanceof BTProvider);
    }
    
    public function testPostProcess(){
        $fields = [
                'payment_id' => 'sample',
                'payment_provider' => 'braintree',
                'intent' => 'sale',
                'payment_method' => 'credit_card',
                'state' => 'status',
                'amount' => 124,
                'currency' => 'AUD',
                'description' => 'direct payment with credit card',
                'created_time' => date('Y-m-d H:i:s'),
            ];
        
        $initial = count($this->db->fetchAll('select * from pr1orders'));
        $result = $this->processor->postProcess($fields);
        $after = count($this->db->fetchAll('select * from pr1orders'));
        
        $this->assertEquals($initial + 1, $after);
        $this->assertEquals("Payment completed for Id: sample \n Payment status: status", $result);
    }
}
