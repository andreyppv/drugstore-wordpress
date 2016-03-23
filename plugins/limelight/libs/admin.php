<?php

/**
* hide shipping methods except limelight
* 
* @param mixed $available_gateways
*/
function smash_shipping_methods($available_methods) {
    foreach($available_methods as $code => $item)
    {
        if($item != 'WC_Shipping_Free_Shipping') unset($available_methods[$code]);
    }
    
    return $available_methods;
}
add_filter('woocommerce_shipping_methods', 'smash_shipping_methods');

/**
* hide gateways except limelight
* 
* @param mixed $available_gateways
*/
function smash_payment_gateways($available_gateways) {
    foreach($available_gateways as $code => $value) {
        if($value != 'WC_Smash_Limelight_Gateway') unset($available_gateways[$code]);
    }
    
    return $available_gateways;
}
//add_filter('woocommerce_payment_gateways', 'smash_payment_gateways');

function smash_admin_notice() {
    echo '<div id="smash-admin-notice" class="update-nag notice"><p>';
    _e( 'Please ensure all products have a Limelight Camp-ID, Product-ID and shipping-ID  because LimelightCRM is enabled as your payment gateway; otherwise, there will be no payment for it.', 'limelight' );
    echo '</p></div>';
    
    //if products updating is fail
    if(isset($_SESSION[SK_UPDATE_FAIL])) { 
        unset($_SESSION[SK_UPDATE_FAIL]);
        
        echo '<div class="update-nag error" style="display:block;"><p>';
        _e( 'Update failed. Can\'t connect to limelight right now. Please check your limelight info and try again later.', 'limelight' );
        echo '</p></div>';    
    }
    
    //if products updating is success
    if(isset($_SESSION[SK_UPDATE_SUCCESS])) { 
        //unset($_SESSION[SK_UPDATE_SUCCESS]);
        
        echo '<div class="update-nag updated" style="display:block;"><p>';
        _e( 'Limelight products are updated successfully.', 'limelight' );
        echo '</p></div>'; 
    }
}
add_action('admin_notices', 'smash_admin_notice');

/**
* add notice to product edit page in admin
* 
* @param mixed $post
*/
function product_page_notice($post) {
    echo '<style>#smash-admin-notice {display:none;}</style>';
        
    if($post->post_type == 'product') { 
        echo '<div class="update-nag notice" style="display:block;"><p>';
        _e( 'You need to enter Camp-ID, Product-ID or shipping-ID for this product before you can save, because LimelightCRM is enabled as your payment gateway', 'limelight' );
        echo '</p></div>';
        
        //if getting product info is fail
        if(isset($_SESSION[SK_PRODUCT_FAIL])) {
            unset($_SESSION[SK_PRODUCT_FAIL]);
            
            echo '<div class="update-nag error" style="display:block;"><p>';
            _e( 'We are sorry. We can\'t get product info from limelight by given product id. Please try again later.', 'limelight' );
            echo '</p></div>';
        }
        
        //if getting shipping info is fail
        if(isset($_SESSION[SK_SHIPPING_FAIL])) {
            unset($_SESSION[SK_SHIPPING_FAIL]);
            
            echo '<div class="update-nag error" style="display:block;"><p>';
            _e( 'We are sorry. We can\'t get shipping info from limelight by given shipping id. Please try again later.', 'limelight' );
            echo '</p></div>';
        }
    }
}
add_action('edit_form_top', 'product_page_notice', 10, 2);

/**
* add limelight product price automatically when save product in admin
* 
* @param mixed $post_id
*/
function smash_save_product_info( $post_id, $post ) {  
}
//add_action( 'save_post_product', 'smash_save_product_info', 10, 2 );

/**
* Add limelight tab to product detail page of admin
* 
* @param mixed $methods
*/
function smash_add_limelight_tab($tabs) {
    $tabs['limelight'] = array(
        'label'  => __( 'LimeLight Fields', 'woocommerce' ),
        'target' => 'limelight_fields',
        'class'  => array( 'hide_if_grouped' ),
    );
                    
    return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'smash_add_limelight_tab' );

/**
* Add Limelight custom field tab panel
* 
*/
function smash_add_limelight_tab_panel() { 
    global $post;
    
    include_once(HTML_PATH . 'admin/product_lielight_tab.php');
}
add_action( 'woocommerce_product_data_panels', 'smash_add_limelight_tab_panel' );

/**
* Save Post Data
*/
function smash_save_post_data($post_id, $post)
{
    //get old product and shippin info
    $old_product_info = get_post_meta($post_id, '_product_info', true);
    $old_shipping_info = get_post_meta($post_id, '_shipping_info', true);
    
    //get limelight product info    
    if ( (isset( $_POST['_limelight_product_id_changed'] ) && $_POST['_limelight_product_id_changed'] === '1') || $old_product_info == null) {
        if ( isset( $_POST['_limelight_product_id'] ) ) {
            $limelight_product_id = ( $_POST['_limelight_product_id'] === '' ) ? 0 : intval( $_POST['_limelight_product_id'] );
            $limelight_product = null;
            $limelight_rebill_product = null;

            if($limelight_product_id > 0) {
                $limelight_product = get_limelight_product($limelight_product_id, true);
                //$_SESSION[SK_PRODUCT_FAIL] = true;
                if($limelight_product != null) {
                    $_POST['_regular_price'] = $limelight_product['product_price'];
                    $_POST['_sku'] = $limelight_product['product_sku'];
                    //update_post_meta( $post_id, '_regular_price', 1 );
                    //update_post_meta( $post_id, '_sku', 1 );
                    
                    //update product title
                    $post->post_title = $limelight_product['product_name'];
                    wp_update_post((array)$post);
                    
                    if($limelight_product['product_rebill_product'] > 0) {
                        $limelight_rebill_product = get_limelight_product($limelight_product['product_rebill_product'], true);
                    }
                } else {
                    $_SESSION[SK_PRODUCT_FAIL] = true;
                }
            }
            
            update_post_meta( $post_id, '_limelight_product_id', $limelight_product_id );
            update_post_meta( $post_id, '_product_info', $limelight_product );
            update_post_meta( $post_id, '_rebill_product_info', $limelight_rebill_product );
        } else {
            //update_post_meta( $post_id, '_limelight_product_id', 0 );
            update_post_meta( $post_id, '_product_info', null );
            update_post_meta( $post_id, '_rebill_product_info', null );
        }
    }
    
    //capaign info
    if ( isset( $_POST['_limelight_campaign_id'] ) ) {
        update_post_meta( $post_id, '_limelight_campaign_id', ( '' === $_POST['_limelight_campaign_id'] ) ? '' : intval( $_POST['_limelight_campaign_id'] ) );
    } else {
        update_post_meta( $post_id, '_limelight_campaign_id', 0 );
    }
    
    //shipping info
    if ( (isset( $_POST['_limelight_shipping_id_changed'] ) && $_POST['_limelight_shipping_id_changed'] === '1') || $old_shipping_info == null) {
        if ( isset( $_POST['_limelight_shipping_id'] ) ) {
            $limelight_shipping_id = ( $_POST['_limelight_shipping_id'] === '' ) ? 0 : intval( $_POST['_limelight_shipping_id'] );
            $limelight_shipping = null;
            if($limelight_shipping_id > 0) {
                $limelight_shipping = get_limelight_shipping($limelight_shipping_id, true);
                
                //$_SESSION[SK_SHIPPING_FAIL] = true;
                if($limelight_shipping != null) {
                } else {
                    $_SESSION[SK_SHIPPING_FAIL] = true;
                }
            }
            
            update_post_meta( $post_id, '_limelight_shipping_id', $limelight_shipping_id );
            update_post_meta( $post_id, '_shipping_info', $limelight_shipping );
        } else {
            update_post_meta( $post_id, '_limelight_shipping_id', 0 );
            update_post_meta( $post_id, '_shipping_info', null );
        }
    }
    
    //gateway 
    if ( isset( $_POST['_limelight_gateway_id'] ) ) {
        update_post_meta( $post_id, '_limelight_gateway_id', ( '' === $_POST['_limelight_gateway_id'] ) ? '' : intval( $_POST['_limelight_gateway_id'] ) );
    } else {
        update_post_meta( $post_id, '_limelight_gateway_id', '' );
    }
    
    //terms
    if ( isset( $_POST['_limelight_terms'] ) ) {
        update_post_meta( $post_id, '_limelight_terms', $_POST['_limelight_terms'] );
    }
    
    //terms checkbox
    $terms_checkbox_value = '';
    if ( isset( $_POST['_limelight_terms_checkbox'] ) ) {
        $terms_checkbox_value = 'yes';
    }
    update_post_meta( $post_id, '_limelight_terms_checkbox', $terms_checkbox_value );
}
add_action('woocommerce_process_product_meta', 'smash_save_post_data', 10, 2);

/**
* update limelight products from limelight crm
* 
*/
function smash_limelight_product_update() {
    if ( ! empty( $_GET['do_update_limelight_products'] ) ) {
        set_time_limit(0);
        
        $total_products = 0;
        $total_limelight_products = 0;
        $total_success = 0;
        $fail_products = array();
        $success = true;
        
        //loop all woocommerce products   
        $args = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1);
        $loop = new WP_Query( $args );     
        if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) {
                $loop->the_post();
                global $product;
                global $post;
                
                //check if connection is disabled
                if(limelight_connection_disabled() == true) {
                    $success = false;
                    break;
                }
                
                $limelight_product_id = get_post_meta($product->id, '_limelight_product_id', true);
                $limelight_shipping_id = get_post_meta($product->id, '_limelight_shipping_id', true);
                $fail_product = false;
                $fail_shipping = false;
                
                //get product info
                if($limelight_product_id > 0) {
                    $limelight_product = get_limelight_product($limelight_product_id, true);
                    $limelight_rebill_product = null;
                    if($limelight_product != null) {
                        update_post_meta( $product->id, '_regular_price', $limelight_product['product_price'] );
                        update_post_meta( $product->id, '_sku', $limelight_product['product_sku'] );
                        
                        $post->post_title = $limelight_product['product_name'];
                        wp_update_post((array)$post);        
                        
                        if($limelight_product['product_rebill_product'] > 0) {
                            $limelight_rebill_product = get_limelight_product($limelight_product['product_rebill_product']);
                        }
                    } else {
                        $fail_product = true;
                    }
                    
                    update_post_meta($product->id, '_product_info', $limelight_product);
                    update_post_meta($product->id, '_rebill_product_info', $limelight_rebill_product);
                    $total_limelight_products++;
                }
                
                //get shipping info
                if($limelight_shipping_id > 0) {
                    $limelight_shipping = get_limelight_shipping($limelight_shipping_id, true);
                    if($limelight_shipping != null) {
                    } else {
                        $fail_shipping = true;                        
                    }
                }
                update_post_meta($product->id, '_shipping_info', $limelight_shipping);
                
                //increase success products
                if($fail_product === true || $fail_shipping === true) {
                    $fail_products[] = array(
                        'product_id' => $product->id,
                        'product_name' => $post->post_title,
                        'limelight_product_id' => $fail_product ? $limelight_product_id : 0,  
                        'limelight_shipping_id' => $fail_shipping ? $limelight_shipping_id : 0
                    );    
                }
                
                //increase total products
                $total_products++;
                
                //0.01 seconds
                usleep(10000);
                //1 seconds
                //sleep(1);  
                //break;
            }
        }  
        
        //
        if($success) {
            $_SESSION[SK_UPDATE_SUCCESS] = true;
            
            add_update_log($total_products, $total_limelight_products, $total_limelight_products - count($fail_products), $fail_products);
        } else {
            $_SESSION[SK_UPDATE_FAIL] = true;                    
            
            add_update_log($total_products, $total_limelight_products, 0, array(), 'Connection Problem');
        }
        wp_redirect(admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_smash_limelight_gateway' ));
    }
}
add_action('admin_init', 'smash_limelight_product_update'); 