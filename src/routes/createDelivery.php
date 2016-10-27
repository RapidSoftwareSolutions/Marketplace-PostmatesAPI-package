<?php

$app->post('/api/PostmatesAPI/createDelivery', function ($request, $response, $args) {
    $settings =  $this->settings;
    
    $data = $request->getBody();
    $post_data = json_decode($data, true);
    if(!isset($post_data['args'])) {
        $data = $request->getParsedBody();
        $post_data = $data;
    }
    
    $error = [];
    if(empty($post_data['args']['customerId'])) {
        $error[] = 'customerId cannot be empty';
    }
    if(empty($post_data['args']['apiKey'])) {
        $error[] = 'apiKey cannot be empty';
    }
    if(empty($post_data['args']['manifest'])) {
        $error[] = 'manifest cannot be empty';
    }
    if(empty($post_data['args']['pickupName'])) {
        $error[] = 'pickupName cannot be empty';
    }
    if(empty($post_data['args']['pickupAddress'])) {
        $error[] = 'pickupAddress cannot be empty';
    }
    if(empty($post_data['args']['pickupPhoneNumber'])) {
        $error[] = 'pickupPhoneNumber cannot be empty';
    }
    if(empty($post_data['args']['dropoffName'])) {
        $error[] = 'dropoffName cannot be empty';
    }
    if(empty($post_data['args']['dropoffAddress'])) {
        $error[] = 'dropoffAddress cannot be empty';
    }
    if(empty($post_data['args']['dropoffPhoneNumber'])) {
        $error[] = 'dropoffPhoneNumber cannot be empty';
    }
    
    if(!empty($error)) {
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = implode(',', $error);
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }
    
    $body['manifest'] = $post_data['args']['manifest']; 
    $body['pickup_name'] = $post_data['args']['pickupName'];
    $body['pickup_address'] = $post_data['args']['pickupAddress'];
    $body['pickup_phone_number'] = $post_data['args']['pickupPhoneNumber'];
    $body['dropoff_name'] = $post_data['args']['dropoffName'];
    $body['dropoff_address'] = $post_data['args']['dropoffAddress'];
    $body['dropoff_phone_number'] = $post_data['args']['dropoffPhoneNumber'];
    if(!empty($post_data['args']['quoteId'])) {
        $body['quote_id'] = $post_data['args']['quoteId'];
    }
    if(!empty($post_data['args']['manifestReference'])) {
        $body['manifest_reference'] = $post_data['args']['manifestReference'];
    }
    if(!empty($post_data['args']['pickupBusinessName'])) {
        $body['pickup_business_name'] = $post_data['args']['pickupBusinessName'];
    }
    if(!empty($post_data['args']['pickupNotes'])) {
        $body['pickup_notes'] = $post_data['args']['pickupNotes'];
    }
    if(!empty($post_data['args']['dropoffBusinessName'])) {
        $body['dropoff_business_name'] = $post_data['args']['dropoffBusinessName'];
    }
    if(!empty($post_data['args']['dropoffNotes'])) {
        $body['dropoff_notes'] = $post_data['args']['dropoffNotes'];
    }
    
    $auth = [$post_data['args']['apiKey'], ''];

    $query_str = $settings['api_url'] . 'v1/customers/'.$post_data['args']['customerId'].'/deliveries';
    
    $client = $this->httpClient;

    try {

        $resp = $client->post( $query_str, 
            [
                'auth' => $auth,
                'form_params' => $body,
                'verify' => false
            ]);
        $responseBody = $resp->getBody()->getContents();
        
        if($resp->getStatusCode() == '200') {
            $result['callback'] = 'success';
            $result['contextWrites']['to'] = is_array($responseBody) ? $responseBody : json_decode($responseBody);
        } else {
            $result['callback'] = 'error';
            $result['contextWrites']['to'] = is_array($responseBody) ? $responseBody : json_decode($responseBody);
        }

    } catch (\GuzzleHttp\Exception\ClientException $exception) {

        $responseBody = $exception->getResponse()->getBody();
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = json_decode($responseBody);

    } catch (GuzzleHttp\Exception\ServerException $exception) {

        $responseBody = $exception->getResponse()->getBody(true);
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = json_decode($responseBody);

    } catch (GuzzleHttp\Exception\BadResponseException $exception) {

        $responseBody = $exception->getResponse()->getBody(true);
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = json_decode($responseBody);

    }

    return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
});
