<?php
/*
|--------------------------------------------------------------------------
|  check admin side post type page
|--------------------------------------------------------------------------
*/
function bucw_get_post_type_page() {
	global $post, $typenow, $current_screen;
        if ( $post && $post->post_type ){
				return $post->post_type;
		}elseif( $typenow ){
				return $typenow;
		}elseif( $current_screen && $current_screen->post_type ){
				return $current_screen->post_type;
		}
		elseif( isset( $_REQUEST['post_type'] ) ){
				return sanitize_key( $_REQUEST['post_type'] );
		}
		elseif ( isset( $_REQUEST['post'] ) ) {
		return get_post_type( filter_var($_REQUEST['post'], FILTER_SANITIZE_STRING) );
		}
		return null;
}
    function bucw_fetch_currency_name(){
        $get_option_val = get_option('bucw_currency_name');
        $cache = get_transient('bucw_allcoins_data');
        if (is_array($cache)&&count($cache)==0 || $cache==false) {
            $api_url ='https://api.buyucoin.com/ticker/v1.0/liveData';
		    $request = wp_remote_get($api_url);
            if (is_wp_error($request)) {
			    return false; // Bail early
		    }
		    $body = wp_remote_retrieve_body($request);
            $arr_data = array($body);
		    $coin = json_decode($body);
            $coins = isset($coin->data)?$coin->data:'';
            $response = array();
		    $coin_data = array();
            if(is_array($coins)&&count($coins)>=1){
	        foreach($coins as $key=>$values){
              
                $coin_id  = $coins[$key];
                $coin_data['currToName'] =$coin_id->currToName;
                $coin_data['marketName'] = $coin_id->marketName;
                $coin_data['baseCurrency'] = $coin_id->baseCurrency;
                $coin_data['quoteCurrency'] = $coin_id->quoteCurrency;
                $coin_data['h24'] =$coin_id->h24;
                $coin_data['l24'] = $coin_id->l24;
                $coin_data['v24'] = $coin_id->v24;
                $coin_data['LBRate'] = $coin_id->LBRate;
                // $coin_data['l24'] = $coin_id->marketName;
                $coin_data['c24p'] = $coin_id->c24p;
                $coin_data['c24'] = $coin_id->c24;
                $response[] = $coin_data;
			}
            $db = new bucw_database();
			$db->create_table();
            $db->bucw_insert($response);
            set_transient('bucw_allcoins_data', $response,  5 * MINUTE_IN_SECONDS);
            return $response;
            }
        }else{
            return false;
           
        }
	}
		
	/*
|--------------------------------------------------------------------------
| getting all binance pair from database
|--------------------------------------------------------------------------
*/
function bucw_get_all_data()
{
    $db = new bucw_database();
    $coin_data = $db->bucw_get_all_coins_name();
    $coins = array();
     if (is_array($coin_data) && isset($coin_data)) {
        foreach ($coin_data as $key=>$coin) {
             $base_currency =   $coin_data[$key]['marketName'] ;
            $coins[$base_currency]= ucwords(strtolower($coin_data[$key]['currency_name'])). ' ('. $coin_data[$key]['market_name'] .')';
        }
    }
    return $coins;
}
function bucw_get_selected_coins_info($select_coin,$post_id){
    global $wpdb;
    $coins=array();
    $results=array();
     $name = $select_coin;
     if(is_array($select_coin)){
         $name = implode(",",$select_coin);
        
     }
     $cache_name = 'bucw_coins_name-'.$post_id;
    $cache = get_transient($cache_name);
    $table_name = $wpdb->base_prefix . 'bucw_coins';
    if (false === $cache) {
        if(is_array($select_coin)){
            $val = '"'.implode('","', $select_coin).'"';
            $values =trim($val,'"');
            $in_str_arr = array_fill( 0, count( $select_coin ), '%s' );
            $val = '"'.implode(',', $select_coin).'"';
            $values =trim($val,'"');
            $values =  explode(',',$values);
            $in_str_arr = array_fill( 0, count( $values ), '%s' ); // create a string of %s - one for each array value. This creates array( '%s', '%s', '%s' )
            $in_str = join( ',', $in_str_arr ); // now turn it into a comma separated string. This creates "%s,%s,%s"
            $coin_data = $wpdb->get_results(  $wpdb->prepare(" SELECT * FROM $table_name  WHERE marketName IN ($in_str) ORDER BY `wp_bucw_coins`.`LBRate` DESC", $values ), );
        }else{
                $query = $wpdb->prepare( 
                "SELECT * FROM {$table_name} WHERE marketName = %s", 
                $select_coin 
                );
                $coin_data = $wpdb->get_results( $query );
        }
        if (is_array($coin_data) && isset($coin_data)) {
            foreach ($coin_data as $key=>$coin) {
                $coins['currency_name'] = isset($coin_data[$key]->currToName)?$coin_data[$key]->currToName:'';
                $coins['market_name'] = isset($coin_data[$key]->marketName)?$coin_data[$key]->marketName:'';
                $coins['baseCurrency'] = isset($coin_data[$key]->baseCurrency)?$coin_data[$key]->baseCurrency:'';
                $coins['quoteCurrency'] = isset($coin_data[$key]->quoteCurrency)?$coin_data[$key]->quoteCurrency:'';
                $coins['marketName'] = isset($coin_data[$key]->marketName)?$coin_data[$key]->marketName:'';
                $coins['h24'] = isset($coin_data[$key]->h24)?$coin_data[$key]->h24:'';
                $coins['l24'] = isset($coin_data[$key]->l24)?$coin_data[$key]->l24:'';
                $coins['v24'] = isset($coin_data[$key]->v24)?$coin_data[$key]->v24:'';
                $coins['LBRate'] = isset($coin_data[$key]->LBRate)?$coin_data[$key]->LBRate:'';
                $coins['c24'] = isset($coin_data[$key]->c24)?$coin_data[$key]->c24:'';
                $coins['c24p'] = isset($coin_data[$key]->c24p)?$coin_data[$key]->c24p:'';
                // $coins['v24'] = isset($coin_data[$key]->v24)?$coin_data[$key]->v24:'';
                // $coins['LBRate'] = isset($coin_data[$key]->LBRate)?$coin_data[$key]->LBRate:'';
                $results[]=$coins;
                set_transient($cache_name, $results, 5 * MINUTE_IN_SECONDS);
               // $coins[$coin['symbol']] = $coin['name'];
            }
        	return $results;
        }else{
            return false;
        } 
    }else {
        return $cache;
    }
}
function bucw_format_number($n)
{
    $n=floatval($n);
      if ($n >= 25) {
            return $formatted = number_format($n, 2, '.', ',');
        } else if ($n >= 0.50 && $n < 25) {
            return $formatted = number_format($n, 3, '.', ',');
        } else if ($n >= 0.01 && $n < 0.50) {
            return $formatted = number_format($n, 4, '.', ',');
        } else if ($n >= 0.001 && $n < 0.01) {
            return $formatted = number_format($n, 5, '.', ',');
        } else if ($n >= 0.0001 && $n < 0.001) {
            return $formatted = number_format($n, 6, '.', ',');
        } 
        else if ($n <= -0.01 && $n > -0.50) {
            return $formatted = number_format(0 - abs($n), 3, '.', ',');
        }
        else if ($n <= -0.0001 && $n > -0.001) {
            return $formatted = number_format(0 - abs($n), 5, '.', ',');
        }       
         else {
            return $formatted = $n;
        }
}

function bucw_mb_format($value, $precision = 2)
{
    if ($value < 1000000) {
        // Anything less than a million
        $formated_str = number_format($value / 1000, $precision) . '  K';
    } else if ($value < 1000000000) {
        // Anything less than a billion
        $formated_str = number_format($value / 1000000, $precision) . '  M';

        

    } else {
        // At least a billion
        $formated_str = number_format($value / 1000000000, $precision) . '  B';

        

    }

    return $formated_str;
}

// currencies symbol
function bucw_currency_symbol($name)
{
    $cc = strtoupper($name);
    $currency = array(
        "USD" => "&#36;", //U.S. Dollar
        "CLP" => "&#36;", //CLP Dollar
        "SGD" => "S&#36;", //Singapur dollar
        "AUD" => "&#36;", //Australian Dollar
        "BRL" => "R&#36;", //Brazilian Real
        "CAD" => "C&#36;", //Canadian Dollar
        "CZK" => "K&#269;", //Czech Koruna
        "DKK" => "kr", //Danish Krone
        "EUR" => "&euro;", //Euro
        "HKD" => "&#36", //Hong Kong Dollar
        "HUF" => "Ft", //Hungarian Forint
        "ILS" => "&#x20aa;", //Israeli New Sheqel
        "INR" => "&#8377;", //Indian Rupee
        "IDR" => "Rp", //Indian Rupee
        "KRW" => "&#8361;", //WON
        "CNY" => "&#165;", //CNY
        "JPY" => "&yen;", //Japanese Yen
        "MYR" => "RM", //Malaysian Ringgit
        "MXN" => "&#36;", //Mexican Peso
        "NOK" => "kr", //Norwegian Krone
        "NZD" => "&#36;", //New Zealand Dollar
        "PHP" => "&#x20b1;", //Philippine Peso
        "PLN" => "&#122;&#322;", //Polish Zloty
        "GBP" => "&pound;", //Pound Sterling
        "SEK" => "kr", //Swedish Krona
        "CHF" => "Fr", //Swiss Franc
        "TWD" => "NT&#36;", //Taiwan New Dollar
        "PKR" => "Rs", //Rs
        "THB" => "&#3647;", //Thai Baht
        "TRY" => "&#8378;", //Turkish Lira
        "ZAR" => "R", //zar
        "RUB" => "&#8381;", //rub
    );
    if (array_key_exists($cc, $currency)) {
        return $currency[$cc];
    }
}