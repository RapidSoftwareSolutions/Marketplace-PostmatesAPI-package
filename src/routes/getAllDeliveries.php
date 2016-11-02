<?php

$app->post('/api/PostmatesAPI/getAllDeliveries', function ($request, $response, $args) {
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
    
    if(!empty($error)) {
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = implode(',', $error);
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }
    
    $body = []; 
    if(!empty($post_data['args']['filter'])) {
        $body['filter'] = $post_data['args']['filter'];
    }
    
    
    $auth = [$post_data['args']['apiKey'], ''];

    $query_str = $settings['api_url'] . 'v1/customers/'.$post_data['args']['customerId'].'/deliveries';
    
    $client = $this->httpClient;

    try {

        $resp = $client->get( $query_str, 
            [
                'auth' => $auth,
                'query' => $body,
                'verify' => false
            ]);
        $responseBody = $resp->getBody()->getContents();
        $rawBody = json_decode($resp->getBody());
      
        $all_data[] = $rawBody;
        
        if(isset($rawBody->next_href)) {
            $pagin = $this->pager;
            $ret = $pagin->page(substr($settings['api_url'],0,-1).$rawBody->next_href, $auth, $query);
         
            $merge = array_merge($all_data[0]->data, $ret);
        
            $all_data[0]->data = $merge;
        }
        
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
