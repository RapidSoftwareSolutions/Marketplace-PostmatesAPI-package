<?php
namespace Models;

Class NextPage {
    protected $all_data = [];
    protected $page = 1;
    protected $api_url = 'https://api.postmates.com';
    
    public function page($url, $auth, $old_query) {
      
        if($url) {
            $client = new \GuzzleHttp\Client();
            
            $req_url = parse_url($url);
            $args = explode('&', $req_url['query']);

            foreach($args as $item) {
                $arg = explode('=', $item);
                if($arg[0]=='limit' || $arg[0]=='offset') {
                    $old_query[$arg[0]] = $arg[1];
                }
            }

            $resp = $client->get( $this->api_url.$req_url['path'], 
            [
                'auth' => $auth,
                'query' => $old_query,
                'verify' => false
            ]);

            $rawBody = json_decode($resp->getBody());
            if(!empty($rawBody->data)) {
                $this->all_data = array_merge($this->all_data, $rawBody->data);
                $this->page++;
                
                if($this->page <= round($rawBody->total_count/32)) {
                    if(!empty($rawBody->next_href)) {
                        $this->page($this->api_url.$rawBody->next_href, $auth, $old_query);
                    } else {
                        $old_query['offset'] = $old_query['offset'] + 32;
                        $this->page($this->api_url.$req_url['path'], $auth, $old_query);
                    }
                }
            }
        }
        
        return $this->all_data;
    }    
    
}

