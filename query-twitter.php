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


    $screen_name = strtoupper($_GET['screen_name']);
    //echo "======SCREEN NAME======" . $screen_name;

    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

    $oauth_access_token = "143031335-dXK26iihNWe3UpvTFXAZ6ZKno48tJi8zHD8mN92C";
    $oauth_access_token_secret = "jzrKi7s5mtpNagSkwBFljL3de5ZvlJLfMBXlJfSrn9FW7";
    $consumer_key = "KdsHyQGPcKqub2mI2ntUuqDXf";
    $consumer_secret = "XHnPROx0L2NvZY5OTLzr6MPV2bOSmhINdmL4NPmxvBrzHNo9Vw";


    $oauth = array(
           'screen_name' => $screen_name,
           'count' => 10,
           'oauth_consumer_key' => $consumer_key,
           'oauth_nonce' => time(),
           'oauth_signature_method' => 'HMAC-SHA1',
           'oauth_token' => $oauth_access_token,
           'oauth_timestamp' => time(),
           'oauth_version' => '1.0'
         );

    $base_info = buildBaseString($url, 'GET', $oauth);
    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;

    // Make requests
    $header = array(buildAuthorizationHeader($oauth), 'Expect:');
    $options = array(
                    CURLOPT_HTTPHEADER => $header,
                    //CURLOPT_POSTFIELDS => $postfields,
                    CURLOPT_HEADER => false,
                    CURLOPT_URL => $url . '?screen_name=' . $screen_name . '&count=10',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false
                );

    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);

    $twitter_data = json_decode($json);


    //print_r ($twitter_data);

    echo "<hr><h3>Twitter - Recent Tweets</h3>";
    foreach($twitter_data as $key=>$value){
        //print_r($value);
        $tweetId = $value->id;

        $time = explode(" ", $value->created_at);
        echo $time[0] . ' ' . $time[1] . ' ' . $time[2] . ' ' . $time[3];;

        echo "<br>";
        echo $value->text;
        echo "<br>";
        echo "<a href='https://twitter.com/" . $screen_name . "/status/" . $tweetId . "' target='_blank' >[View Tweet]</a>";
        echo "<br><br>";
    }

?>
