<?php

$ins = new WC_Smash_Limelight_Gateway;

/**
* product pages
*/
{ 
    /**
    * remove price from category page
    */
    //remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
    
    /**
    * change product title on product list
    * 
    * @param mixed $woocommerce_show_product_loop_sale_flash
    */
    function smash_before_shop_loop_item_title( $woocommerce_show_product_loop_sale_flash ) { 
        global $post;   
        
        $limelight_product_id = get_post_meta( $post->ID, '_limelight_product_id', true );
        if($limelight_product_id > 0) {
            $limelight_product = get_limelight_product($limelight_product_id);
            if($limelight_product != null) {
                $post->post_title = $limelight_product['product_name'];
            }
        }
    }
    //add_action( 'woocommerce_before_shop_loop_item_title', 'smash_before_shop_loop_item_title', 10 ); 
    //add_action( 'woocommerce_before_main_content', 'smash_before_shop_loop_item_title', 10, 2 ); 
    
    /**
    * change product price when show product page
    * 
    * @param mixed $price
    * @param mixed $product
    */
    function smash_get_price($price, $product) {
        $limelight_product_id = get_post_meta( $product->id, '_limelight_product_id', true );
        if($limelight_product_id > 0) {  
            $limelight_product = get_limelight_product($limelight_product_id);
            if($limelight_product != null) {
                return $limelight_product['product_price'];
            }
        }
        
        return $price;
    }
    //add_filter('woocommerce_get_price', 'smash_get_price', 10, 2);
}

/**
* cart pages
*/
{
    /**
    * change price when add to cart
    * 
    * @param mixed $cart_item
    */
    function smash_add_cart_item( $cart_item ) {
        return $cart_item;
    }
    //add_filter( 'woocommerce_add_cart_item', 'smash_add_cart_item', 20, 1 );
    
    /**
    * change product title with ll product title when add to cart 
    */
    function smash_product_title( $title, $post ) {
        $limelight_product_id = get_post_meta( $post->id, '_limelight_product_id', true );
        $limelight_product = get_limelight_product($limelight_product_id);
        
        if($limelight_product != null) return $limelight_product['product_name'];
        
        return $title;
    }
    //add_action( 'woocommerce_product_title', 'smash_product_title', 10, 2 );
    
    function smash_before_add_to_cart_button() { 
        $ins = new WC_Smash_Limelight_Gateway;
        if($ins->show_terms_checkbox != 'yes') return;
        
        global $post;
        $show_product_terms = get_post_meta($post->ID, '_limelight_terms_checkbox', true);
        if($show_product_terms != 'yes') return;
        
        include_once(HTML_PATH . 'front/terms_checkbox.php');
    }
    add_action('woocommerce_before_add_to_cart_button', 'smash_before_add_to_cart_button', 10, 2);    
    
    function smash_add_to_cart($cart_item_key, $product_id, $quantity) {
        $ins = new WC_Smash_Limelight_Gateway;
        if($ins->show_terms_checkbox != 'yes') return;
        
        $show_product_terms = get_post_meta($product_id, '_limelight_terms_checkbox', true);
        if($show_product_terms != 'yes') return;
        
        if ( !isset($_POST['terms'] ) ) {
            throw new Exception( sprintf(__( 'You must accept our Terms & Conditions.' ) ) );
        }
    }
    add_action('woocommerce_add_to_cart', 'smash_add_to_cart', 10, 6);
    
    /**
    * add shipping info when add to cart
    * 
    * @param mixed $cart_item_meta
    * @param mixed $product_id
    */
    function smash_add_cart_item_data( $cart_item_meta, $product_id ) {
        global $woocommerce;
                                                                                             
        $limelight_shipping_id = get_post_meta($product_id, '_limelight_shipping_id', true);
        /*$limelight_shipping = get_limelight_shipping($limelight_shipping_id);
        $cart_item_meta['shipping_info'] = $limelight_shipping;*/
        
        $limelight_product_id = get_post_meta($product_id, '_limelight_product_id', true);
        /*$limelight_product = get_limelight_product($limelight_product_id);
        $cart_item_meta['product_info'] = $limelight_product;*/
        
        $limelight_shipping = get_post_meta($product_id, '_shipping_info', true);
        $cart_item_meta['shipping_info'] = $limelight_shipping;
        
        $limelight_product = get_post_meta($product_id, '_product_info', true);
        $cart_item_meta['product_info'] = $limelight_product;
        
        $rebill_limelight_product = get_post_meta($product_id, '_rebill_product_info', true);
        $cart_item_meta['rebill_product_info'] = $rebill_limelight_product;
        
        $cart_item_meta['_limelight_product_id']  = $limelight_product_id;
        $cart_item_meta['_limelight_shipping_id'] = $limelight_shipping_id;
        $cart_item_meta['_limelight_campaign_id'] = get_post_meta($product_id, '_limelight_campaign_id', true);
        $cart_item_meta['_limelight_gateway_id']  = get_post_meta($product_id, '_limelight_gateway_id', true);
        
        return $cart_item_meta; 
    }
    add_filter( 'woocommerce_add_cart_item_data', 'smash_add_cart_item_data', 10, 2 );
    
    /**
    * Get it from the session and add it to the cart variable
    * 
    * @param mixed $item
    * @param mixed $values
    * @param mixed $key
    */
    function smash_get_cart_items_from_session( $item, $values, $key ) {
        if ( array_key_exists( 'shipping_info', $values ) ) {
            $item['shipping_info'] = $values['shipping_info'];
        } else {
            $item['shipping_info'] = null;
        }
        
        if ( array_key_exists( 'product_info', $values ) ) {
            $item['product_info'] = $values['product_info'];
        } else {
            $item['product_info'] = null;
        }
        
        if ( array_key_exists( 'rebill_product_info', $values ) ) {
            $item['rebill_product_info'] = $values['rebill_product_info'];
        } else {
            $item['rebill_product_info'] = null;
        }
        
        return $item;
    }
    add_filter( 'woocommerce_get_cart_item_from_session', 'smash_get_cart_items_from_session', 1, 3 );
    
    
    /**
    * recalculate total price
    * 
    * @param mixed $cart_object
    */
    function smash_before_calculate_totals( $cart_object ) {
        foreach ( $cart_object->cart_contents as $key => $value ) {
            $value['data']->price = 100;
        }
    }
    //add_action( 'woocommerce_before_calculate_totals', 'smash_before_calculate_totals' );
    
    /**
    * calculate total price with shipping price
    * 
    * @param mixed $cart_object
    */
    function smash_after_calculate_totals( $cart_object ) {
        $total = WC()->cart->total;
        
        foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $shipping_info = $cart_item['shipping_info'];
            if(is_array($shipping_info) && $shipping_info != null) {
                $total = $total + floatVal($shipping_info['initial_amount']); 
            }
        }
        
        WC()->cart->total = $total;
    }
    add_action( 'woocommerce_after_calculate_totals', 'smash_after_calculate_totals' );
    
    function smash_cart_totals_order_total_html() {
        $total = WC()->cart->total;
        foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $shipping_info = $cart_item['shipping_info'];
            if(is_array($shipping_info) && $shipping_info != null) {
                $total = $total + floatVal($shipping_info['initial_amount']); 
            }
        }
        
        $value = '<strong>' . wc_price($total) . '</strong> ';
        return $value;
    }
    //add_filter('woocommerce_cart_totals_order_total_html','smash_cart_totals_order_total_html');
    
    function smash_add_order_item_meta( $item_id, $values, $cart_item_key ) {
        $cartItems = WC()->cart->get_cart();
        $cartItem = $cartItems[$cart_item_key];
        
        wc_add_order_item_meta( $item_id, 'shipping_info', $cartItem['shipping_info'] , true );
        //wc_add_order_item_meta( $item_id, 'product_info', $cartItem['product_info'] , true );
        //wc_add_order_item_meta( $item_id, 'rebill_product_info', $cartItem['rebill_product_info'] , true );
        wc_add_order_item_meta( $item_id, '_limelight_product_id', $cartItem['_limelight_product_id'] , true );
        wc_add_order_item_meta( $item_id, '_limelight_campaign_id', $cartItem['_limelight_campaign_id'] , true );
        wc_add_order_item_meta( $item_id, '_limelight_shipping_id', $cartItem['_limelight_shipping_id'] , true );
        wc_add_order_item_meta( $item_id, '_limelight_gateway_id', $cartItem['_limelight_gateway_id'] , true );
    }
    add_action( 'woocommerce_add_order_item_meta', 'smash_add_order_item_meta', 10, 3 );
    
    /**
    * replace freeshipping with Limelight Shippings
    * 
    * @param mixed $totals
    * @param mixed $order
    */
    function smash_get_order_item_totals( $totals, $order ) { 
        $shipping = '';
        
        $items = $order->get_items();
        foreach($items as $item) {
            if(isset($item['shipping_info'])) {
                $info = unserialize($item['shipping_info']);
                if(isset($info['name'])) {
                    $shipping .= '<b>' . $item['name'] . ' :</b> ' . $info['name'] . ' - ' . $info['initial_amount'] . '<br>';
                }
            }
        }
        
        $result = array();
        foreach($totals as $item) {
            if($item['label'] == 'Shipping:') {
                $item['value'] = $shipping;    
            }
            $result[] = $item;
        }

        return $result; 
    }; 
    add_filter( 'woocommerce_get_order_item_totals', 'smash_get_order_item_totals', 10, 3 ); 
    
    function smash_review_order_before_submit() { 
        if(!apply_filters( 'woocommerce_checkout_show_terms', true )) return;
        
        echo get_term_contents();
        
        include_once(HTML_PATH . 'front/terms_checkbox.php');
    }
    add_action('woocommerce_review_order_before_submit', 'smash_review_order_before_submit', 10, 2);
}