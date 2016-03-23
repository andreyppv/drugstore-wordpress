<?php

/**
* common functions
*/

include("limelight/Membership.php");

/**
* check limelight connection is disabled
* 
*/
function limelight_connection_disabled() {
    //$key = "limelight_connection";   
    $key = SK_CONNECT_FAIL;
    
    if(isset($_SESSION[$key]) && $_SESSION[$key] == false) {
        return true; 
    } else {
        return false;
    }                
}

/**
* if api info is wrong, then set connection flag to disabled status
* so that next time, it doesn't try to connect again
* 
*/
function set_limelight_connection_disabled() {
    //$key = "limelight_connection";
    $key = SK_CONNECT_FAIL;
    $_SESSION[$key] = false;
}

/**
* get limelight product info
* if info is in session, then get it from session
* or get info from limelight
* 
* @param mixed $limelight_product_id
*/
function get_limelight_product($limelight_product_id, $ignore_session=false) {
    //$ignore_session = false; //for test
    $key = "limelight_product_{$limelight_product_id}";   
    
    if($ignore_session == false && isset($_SESSION[$key])) {
        return $_SESSION[$key];
    } else {
        if(limelight_connection_disabled() == true) return null;

        $options = get_option('woocommerce_limelight_settings');
        $instance= new Membership($options['username'], $options['password'], $options['url']);
        $product = $instance->GetResponse('getproductinfo', $limelight_product_id); 
        
        if($product === false || $product['response_code'] == 200) {
            set_limelight_connection_disabled();
            
            $product = null;
        } else {
            if(!isset($product['product_name']) || $product['product_name'] == '') {
                $product = null;
            }
        }
        
        $_SESSION[$key] = $product;     
        return $product;
    }                
}

/**
* get limelight shipping info
* 
* @param mixed $limelight_shipping_id
*/
function get_limelight_shipping($limelight_shipping_id, $ignore_session=false) {
    //$ignore_session = false; //for test
    $key = "limelight_shipping_{$limelight_shipping_id}";
    
    if($ignore_session == false && isset($_SESSION[$key])) {
        return $_SESSION[$key];
    } else {
        if(limelight_connection_disabled() == true) return null;
        
        $options  = get_option('woocommerce_limelight_settings');
        $instance = new Membership($options['username'], $options['password'], $options['url']);                
        $shipping = $instance->GetResponse('shippingmethodview', $limelight_shipping_id);     

        if($shipping === false || $shipping['response_code'] === 200) {
            set_limelight_connection_disabled();
            
            $shipping = null;    
        } else {
            if(!isset($shipping['name']) || $shipping['name'] == '') {
                $shipping = null;
            }
        }
        
        $_SESSION[$key] = $shipping;     
        return $shipping;
    }
}   

/**
* get terms text from product, 
* if it's empty, then get it from limelight setting default terms
* 
* @param mixed $cart_item
*/
function get_term_template($cart_item) {
    $product_terms = get_post_meta($cart_item['data']->id, '_limelight_terms', true);
    if($product_terms != '') return $product_terms; 
        
    $ins = new WC_Smash_Limelight_Gateway;
    $default_terms = $ins->terms;
    
    return $default_terms; 
}

function get_term_contents() {
    $cart_items = WC()->cart->get_cart();
    
    $result = '';
    
    foreach($cart_items as $item) {
        $product_info = $item['product_info'];
        $shipping_info = $item['shipping_info'];
        $rebill_product = $item['rebill_product_info'];
        $terms = get_term_template($item);
        
        if($terms != '') {
            $product_name = '---';
            $product_price = 0;
            $product_rebill_price = 0;
            $shipping_name = '---';
            $shipping_price = 0;            
            
            if($product_info != null) {
                if($product_info['product_rebill_product'] > 0) {
                    //$rebill_product = get_limelight_product($product_info['product_rebill_product']);
                    if($rebill_product != null) {
                        $product_rebill_price = $rebill_product['product_price'];      
                    }
                }
                
                $product_name = $product_info['product_name'];
                $product_price = $product_info['product_price'];
                $rebill_price = $product_rebill_price;
                
            }
            
            if($shipping_info != null) {
                $shipping_name = $shipping_info['name'];
                $shipping_price = $shipping_info['initial_amount']; 
            }
            
            $terms = str_replace(
                array('[limelight_product_name]', '[limelight_product_price]', '[limelight_rebill_price]', '[limelight_shipping_name]', '[limelight_shipping_price]'),
                array($product_name, wc_price($product_price), wc_price($product_rebill_price), $shipping_name, wc_price($shipping_price)),
                $terms);
            
            $result .= '<div style="margin-bottom:10px;">';                                
            $result .= do_shortcode($terms); 
            $result .= '</div>';
        }
    }    
    
    if($result != '') {
        $result = '<div id="term-lists" style="background-color: #efefef; border-radius: 5px; margin-bottom: 10px; padding: 10px; font-size:23px;">' . $result . '</div>'; 
    }
    
    return $result;
}

function add_update_log($total_products=0, $total_limelight_products=0, $total_success=0, $fails_data=array(), $note='') {
    global $wpdb;
    $table_name = $wpdb->prefix . 'limelight_updates_log';
    
    $wpdb->insert( 
        $table_name, 
        array( 
            'time'  => current_time( 'mysql' ), 
            'total' => $total_products, 
            'total_limelight'   => $total_limelight_products, 
            'total_success'     => $total_success,
            'fails_data'        => serialize($fails_data),
            'note'              => $note
        ) 
    );
}

function get_latest_update_result() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'limelight_updates_log';
    
    return $wpdb->get_row("SELECT * FROM $table_name ORDER BY time DESC LIMIT 1", ARRAY_A );   
}