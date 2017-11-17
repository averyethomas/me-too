<?php

    function buildBaseString($baseURI, $method, $params) {
            $r = array();
            ksort($params);
            foreach($params as $key=>$value){
                $r[] = "$key=" . rawurlencode($value);
            }
            return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
        }

        function buildAuthorizationHeader($oauth) {
            $r = 'Authorization: OAuth ';
            $values = array();
            foreach($oauth as $key=>$value)
                $values[] = "$key=\"" . rawurlencode($value) . "\"";
            $r .= implode(', ', $values);
            return $r;
        }

        $search = 'MeToo';
        $url = "https://api.twitter.com/1.1/search/tweets.json";

        if($search != "")
                $search = "#".$search;
        $query = array( 'count' => 100, 'q' => urlencode($search), "result_type" => "recent");
        $oauth_access_token = "257278598-K6HvknF9b4Nb61TnCDcAd8YNEqNEzaGsBawWf0Jv";
        $oauth_access_token_secret = "TkwJIkcdR3EZ4jX1bGDnIgMQyeoorNPTTN8C8WHy60tFe";
        $consumer_key = "ioMjOQKdQT7L7kISvoxgqJRvy";
        $consumer_secret = "rOxoxuJUuokN9V5PVQU23ZZMFnHAEsA0JcPDw6kiU2RvDpI3eR";

        $oauth = array(
            'oauth_consumer_key'     => $consumer_key,
            'oauth_nonce'            => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token'            => $oauth_access_token,
            'oauth_timestamp'        => time(),
             'oauth_version'         => '1.0'
        );

        $base_params = empty($query) ? $oauth : array_merge($query,$oauth);
        $base_info = buildBaseString($url, 'GET', $base_params);
        $url = empty($query) ? $url : $url . "?" . http_build_query($query);

        $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;

        $header = array(buildAuthorizationHeader($oauth), 'Expect:');
        $options = array( CURLOPT_HTTPHEADER     => $header,
                          CURLOPT_HEADER         => false,
                          CURLOPT_URL            => $url,
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_SSL_VERIFYPEER => false
        );

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);
        $twitter_data =  json_decode($json);

    print_r ($twitter_data);
?>