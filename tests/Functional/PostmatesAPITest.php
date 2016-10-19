<?php

namespace Tests\Functional;

class PostmatesAPITest extends BaseTestCase {
    
    public $customerId = "cus_L-NnPlJQVLvD2k";
    public $apiKey = "00a64237-edb3-49e4-95dc-c3008b16999c";
    
    public function testGetDeliveryQuote() {
        
        $var = '{
                    "args": {
                      "customerId": "'.$this->customerId.'",
                      "apiKey": "'.$this->apiKey.'",
                      "pickupAddress": "20 McAllister St, San Francisco, CA",
                      "dropoffAddress": "101 Market St, San Francisco, CA",
                      "runscope": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/PostmatesAPI/getDeliveryQuote', $post_data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('success', json_decode($response->getBody())->callback);

    }
    
    public function testGetDeliveryZones() {
        
        $var = '{
                    "args": {
                      "customerId": "'.$this->customerId.'",
                      "apiKey": "'.$this->apiKey.'",
                      "runscope": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/PostmatesAPI/getDeliveryZones', $post_data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('success', json_decode($response->getBody())->callback);

    }
    
    public function testGetAllDeliveries() {
        
        $var = '{
                    "args": {
                      "customerId": "'.$this->customerId.'",
                      "apiKey": "'.$this->apiKey.'",
                      "runscope": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/PostmatesAPI/getAllDeliveries', $post_data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('success', json_decode($response->getBody())->callback);

    }
    
    public function testCreateDelivery() {
        
        $var = '{
                    "args": {
                      "customerId": "'.$this->customerId.'",
                      "apiKey": "'.$this->apiKey.'",
                      "manifest": "A box of gray kittens",
                      "pickupName": "Kitten Warehouse",
                      "pickupAddress": "20 McAllister St, San Francisco, CA",
                      "pickupPhoneNumber": "415-555-4242",
                      "dropoffName": "Alice",
                      "dropoffAddress": "678 Green St, San Francisco, CA",
                      "dropoffPhoneNumber": "415-555-8484",
                      "runscope": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/PostmatesAPI/createDelivery', $post_data);
        
        $objectId = json_decode($response->getBody())->contextWrites->to->id;

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('success', json_decode($response->getBody())->callback);
        
        return $objectId;

    }
    
    /**
     * @depends testCreateDelivery
     */
    public function testGetDelivery($objectId) {
        
        $var = '{
                    "args": {
                      "customerId": "'.$this->customerId.'",
                      "apiKey": "'.$this->apiKey.'",
                      "deliveryId": "'.$objectId.'",
                      "runscope": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/PostmatesAPI/getDelivery', $post_data);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('success', json_decode($response->getBody())->callback);
    }
    
    /**
     * @depends testCreateDelivery
     */
    public function testCancelDelivery($objectId) {
        
        $var = '{
                    "args": {
                      "customerId": "'.$this->customerId.'",
                      "apiKey": "'.$this->apiKey.'",
                      "deliveryId": "'.$objectId.'",
                      "runscope": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/PostmatesAPI/cancelDelivery', $post_data);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('success', json_decode($response->getBody())->callback);
    }
    
    public function testReturnDelivery() {
        
        $var = '{
                    "args": {
                      "customerId": "'.$this->customerId.'",
                      "apiKey": "'.$this->apiKey.'",
                      "deliveryId": "del_L-S7jmQAR44mKV",
                      "runscope": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/PostmatesAPI/returnDelivery', $post_data);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('success', json_decode($response->getBody())->callback);
    }
    
    public function testAddCourierTip() {
        
        $var = '{
                    "args": {
                      "customerId": "'.$this->customerId.'",
                      "apiKey": "'.$this->apiKey.'",
                      "deliveryId": "del_L-SACTGNgU2qK-",
                      "tipByCustomer": "500",
                      "runscope": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/PostmatesAPI/addCourierTip', $post_data);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('tip_already_recorded_error', json_decode($response->getBody())->contextWrites->to->code);
        $this->assertEquals('error', json_decode($response->getBody())->callback);
    }

}
