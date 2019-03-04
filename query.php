
<?php


    // API constants, you shouldn't have to change these.
    $API_HOST = "https://min-api.cryptocompare.com/";
    $SEARCH_PATH = "data/pricemulti";



/*
    $BUSINESS_PATH = "/v3/businesses/";  // Business ID will come after slash.
    $TOKEN_PATH = "/oauth2/token";
    $GRANT_TYPE = "client_credentials";
    // Defaults for our simple example.
    $DEFAULT_TERM = "japanese";
    $DEFAULT_LOCATION = "San Francisco, CA";
    $SEARCH_LIMIT = 8;
*/
    /**
     * Makes a request to the Yelp API and returns the response
     *
     * @param    $bearer_token   API bearer token from obtain_bearer_token
     * @param    $host    The domain host of the API
     * @param    $path    The path of the API after the domain.
     * @param    $url_params    Array of query-string parameters.
     * @return   The JSON response from the request
     */


    function coinrequest( $host, $path, $url_params = array()) {
        // Send Yelp API Call
        try {
            $curl = curl_init();
            if (FALSE === $ch)
                throw new Exception('Failed to initialize');
            $url = $host . $path . "?" . http_build_query($url_params);

            //echo "=====".$url."=====";

            curl_setopt_array($curl, array(
                                           CURLOPT_URL => $url,
                                           CURLOPT_RETURNTRANSFER => true,  // Capture response.
                                           CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
                                           CURLOPT_MAXREDIRS => 10,
                                           CURLOPT_TIMEOUT => 30,
                                           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                           CURLOPT_CUSTOMREQUEST => "GET",
                                           CURLOPT_HTTPHEADER => array(
                                                                       "authorization: Bearer " . $bearer_token,
                                                                       "cache-control: no-cache",
                                                                       ),
                                           ));
            $response = curl_exec($curl);
            if (FALSE === $response)
                throw new Exception(curl_error($curl), curl_errno($curl));
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (200 != $http_status)
                throw new Exception($response, $http_status);
            curl_close($curl);
        } catch(Exception $e) {
            trigger_error(sprintf(
                                  'Curl failed with error #%d: %s',
                                  $e->getCode(), $e->getMessage()),
                          E_USER_ERROR);
        }
        return $response;
    }



    /**
     * Query the Search API by a search term and location
     *
     * @param    $bearer_token   API bearer token from obtain_bearer_token
     * @param    $term        The search term passed to the API
     * @param    $location    The search location passed to the API
     * @return   The JSON response from the request
     */
    function search($bearer_token, $term, $location) {
        $url_params = array();

        $url_params['term'] = $term;
        $url_params['location'] = $location;
        $url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];

        return request($bearer_token, $GLOBALS['API_HOST'], $GLOBALS['SEARCH_PATH'], $url_params);
    }

    function coinsearch($coins,$currencies) {
        $url_params = array();

        $url_params['fsyms'] = $coins;
        $url_params['tsyms'] = $currencies;


        return coinrequest($GLOBALS['API_HOST'], $GLOBALS['SEARCH_PATH'], $url_params);
    }




        function crypto_api($coins, $currencies) {
            //$bearer_token = obtain_bearer_token();
            $query_coins = $coins;
            $query_currencies = $currencies;


            $response = json_decode(coinsearch($query_coins,$query_currencies));
//$response = coinsearch($query_coins,$query_currencies);


//echo "<pre>";
//print_r($response);
//echo "</pre>";


            foreach ($response as $key0 => $value0) {

            // below foreach works to show the key names of primary object decoded from Yelp response (REGION, BUSINESSES, and TOTAL)

            echo "== " . $key0 . " == <br>";

            foreach($value0 as $key => $value)
            {
            echo ' '.$key.': '.$value;
            echo "<br>";
            }

echo "<br>";
/*
            foreach ($response as $key => $value) {
                print "<pre>";
                print "***** KEY: " . $key . " ******* \n";
                print_r($value);
                print "</pre>";
            }
*/


            // BELOW only works when using with json_decode for search function above
/*
            foreach ($response->businesses as $value) {
                $biz_id=$value->id;
                print $biz_id . "<br> ******************************* <br>";

                print "<pre>";
                print_r($value);
                print "</pre>";
*/

            }

        }



    /**
     * User input is handled here
     */
    $longopts  = array(
                       "term::",
                       "location::",
                       );

    $options = getopt("", $longopts);


    $coins = strtoupper($_GET['symbol']);
//    $coins = "BTC,ETH,LTC,POWR,PAY,OMG,GNT,RDN*,ZEN,XMR,QSP,LINK,";
    $currencies = "USD,BTC";

    //query_api($term, $location);
    //query_api($term, $location);

    crypto_api($coins,$currencies);
?>
