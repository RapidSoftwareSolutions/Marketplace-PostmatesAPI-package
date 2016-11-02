<?php
namespace Models;

Class NextPage {
    protected $all_data = [];
    protected $api_url = 'https://api.postmates.com';
    
    public function page($url, $auth) {
        
        if($url) {
            $client = new \GuzzleHttp\Client();

            $resp = $client->get( $url, 
            [
                'auth' => $auth,
                'verify' => false
            ]);

            $rawBody = json_decode($resp->getBody());
            $this->all_data = array_merge($this->all_data, $rawBody->data);

            if(!empty($rawBody->next_href)) {
                $this->page($this->api_url.$rawBody->next_href, $auth);
            }
        }
        return $this->all_data;
    }    
    
}

