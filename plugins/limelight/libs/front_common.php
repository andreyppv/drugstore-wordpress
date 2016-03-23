<?php

/**
* put your comment there...
* 
* @param string $template
* @param mixed $template_name
* @param mixed $template_path
* @return string
*/
function smash_locate_template( $template, $template_name, $template_path ) {
    global $woocommerce;
    $_template = $template;
    if ( ! $template_path )  
        $template_path = $woocommerce->template_url;

    $plugin_path = TEMPLATE_PATH;
     
    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            $template_path . $template_name,
            $template_name
        )
    );
    
    if( ! $template && file_exists( $plugin_path . $template_name ) )
    $template = $plugin_path . $template_name;
    
    if ( ! $template )
        $template = $_template;
    
    return $template;
}
add_filter( 'woocommerce_locate_template', 'smash_locate_template', 1, 3 );

/**
* hide gateways except limelight
* 
* @param mixed $available_gateways
*/
function smash_available_payment_gateways($available_gateways) {
    
    foreach($available_gateways as $code => $value) {
        if($code != 'limelight') unset($available_gateways[$code]);
    }
    
    return $available_gateways;
}
add_filter('woocommerce_available_payment_gateways', 'smash_available_payment_gateways');

/**
* hide shipping methods except limelight
* 
* @param mixed $available_gateways
*/
function smash_available_shipping_methods($available_methods) {
    /*foreach($available_methods as $code => $item)
    {
        if($code != 'free_shipping') unset($available_methods[$code]);
    }*/
    
    $free_shipping = new WC_Shipping_Free_Shipping;
    $shipping = new WC_Shipping_Rate('free_shipping', $free_shipping->get_title(), 0, array(), 'free_shipping');
    
    $available_methods = array();
    $available_methods['free_shipping'] = $shipping;
    
    return $available_methods;
}
add_filter('woocommerce_package_rates', 'smash_available_shipping_methods');

function smash_display_item_shippings() {
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {   
        $shipping_info = $cart_item['shipping_info'];
        $limelight_product = $cart_item['product_info'];
        /*$limelight_product_id = $cart_item['_limelight_product_id'];
        $limelight_product = get_limelight_product($limelight_product_id);*/
        
        if(is_array($shipping_info) && $shipping_info != null) {
            if($limelight_product != null) {
                echo $limelight_product['product_name'] . ' : ' . $shipping_info['name'] . ' ' . wc_price($shipping_info['initial_amount']) . "<br>";
            } else {
                echo $cart_item['data']->post->post_title . ' : ' . $shipping_info['name'] . ' ' . wc_price($shipping_info['initial_amount']) . "<br>";
            }
        }
    }
}