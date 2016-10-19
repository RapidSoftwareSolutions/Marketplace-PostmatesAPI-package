<?php

$app->post('/api/PostmatesAPI/addCourierTip', function ($request, $response, $args) {
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
    if(empty($post_data['args']['deliveryId'])) {
        $error[] = 'deliveryId cannot be empty';
    }
    if(empty($post_data['args']['tipByCustomer'])) {
        $error[] = 'tipByCustomer cannot be empty';
    }
    
    if(!empty($error)) {
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = implode(',', $error);
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }
    
    
    $auth = [$post_data['args']['apiKey'], ''];
    
    $body['tip_by_customer'] = $post_data['args']['tipByCustomer'];

    $query_str = $settings['api_url'] . 'v1/customers/'.$post_data['args']['customerId'].'/deliveries/'.$post_data['args']['deliveryId'];
    
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
            if(!empty($post_data['args']['runscope'])) {
                $result['contextWrites']['to'] = json_decode($responseBody);
            } else {
                $result['contextWrites']['to'] = json_encode($responseBody);
            }
        } else {
            $result['callback'] = 'error';
            $result['contextWrites']['to'] = $responseBody;
        }

    } catch (\GuzzleHttp\Exception\ClientException $exception) {

        $responseBody = $exception->getResponse()->getBody(true);

        $result['callback'] = 'error';
        $result['contextWrites']['to'] = json_decode($responseBody);

    }
    
    

    return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
});
