<?php

/*
Plugin Name: Limelight
Plugin URI: In­house sizzle
Description: Integrate Limelight as a new payment gateway for woocommerce
Version: 1.0
Author: Smashtech
Author URI: http://automattic.com/wordpress-plugins/
*/

/**
 * Check if WooCommerce is active
 **/
 
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action('plugins_loaded', 'smash_wc_smash_init', 0);

function smash_wc_smash_init() {
    if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
        
    DEFINE ('PLUGIN_DIR', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) . '/' );
    DEFINE ('HTML_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/htmls/' );
    DEFINE ('TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/woocommerce/templates/');
    
    DEFINE ('SK_PRODUCT_FAIL', 'limelight_product_fail');
    DEFINE ('SK_SHIPPING_FAIL', 'limelight_shipping_fail');
    DEFINE ('SK_CONNECT_FAIL', 'limelight_connection_fail');
    DEFINE ('SK_UPDATE_FAIL', 'limelight_update_fail');
    DEFINE ('SK_UPDATE_SUCCESS', 'limelight_update_success');
    
    {
        add_action('init', 'smash_start_session', 1);
        function smash_start_session() {
            if(!session_id()) {
                session_start();
            }
        }
        
        /**
        * Add Limelight Gateway to WC
        **/
        function smash_wc_add_limelight_gateway($methods) {
            $methods[] = 'WC_Smash_Limelight_Gateway';
            return $methods;
        }
        add_filter( 'woocommerce_payment_gateways', 'smash_wc_add_limelight_gateway' );

        /**
        * hide limelight fields in admin order details
        * 
        * @param mixed $fields
        */
        function smash_woocommerce_hidden_order_itemmeta($fields) {
            $fields[] = '_limelight_product_id';
            $fields[] = '_limelight_campaign_id';
            $fields[] = '_limelight_shipping_id';
            $fields[] = '_limelight_gateway_id';
            $fields[] = 'shipping_info';
            $fields[] = 'product_info';
            $fields[] = 'rebill_product_info';
            
            return $fields;
        } 
        add_filter('woocommerce_hidden_order_itemmeta', 'smash_woocommerce_hidden_order_itemmeta');

        /**
        * iterate limelight shippings in admin order detail page
        * 
        * @param mixed $order_id
        */
        function smash_woocommerce_admin_order_items_after_shipping($order_id) {
            $order = new WC_Order( $order_id ); 
            $count = 0;
            
            foreach($order->get_items() as $item) {
                $shipping_info = unserialize($item['shipping_info']);
                if($shipping_info != null) { 
                    $count++;
                ?>
                    <tr data-order_item_id="205" class="shipping Zero Rate">
                        <td class="check-column"></td>
                        <td class="thumb"><div></div></td>
                        <td class="name"><?php echo $shipping_info['name']; ?></td>
                        <td width="1%" class="item_cost">&nbsp;</td>
                        <td width="1%" class="quantity">&nbsp;</td>
                        <td width="1%" class="line_cost"><?php echo wc_price($shipping_info['initial_amount']); ?> </td>
                        <td class="wc-order-edit-line-item"></td>
                    </tr>
                <?php 
                } 
            }
            
            if($count > 0) {
                echo '<style>#order_shipping_line_items tr:first-child {display:none;}</style>';
            }
        }
        add_action('woocommerce_admin_order_items_after_shipping', 'smash_woocommerce_admin_order_items_after_shipping', 10, 2);

        /**
        * calculate total shipping price again
        * 
        * @param mixed $order_id
        */
        function smash_woocommerce_admin_order_totals_after_shipping($order_id) { 
            $order = new WC_Order( $order_id ); 
            $shipping_price = 0;
            $count = 0;
            
            foreach($order->get_items() as $item) {
                $shipping_info = unserialize($item['shipping_info']);
                if($shipping_info != null) {
                    $shipping_price += $shipping_info['initial_amount'];
                    $count++;
                }
            } 
            ?>
            <tr id="limelight_shipping_price">
                <td class="label"><?php echo wc_help_tip( __( 'This is the shipping and handling total costs for the order.', 'woocommerce' ) ); ?> <?php _e( 'Shipping', 'woocommerce' ); ?>:</td>
                <td class="total"><?php echo wc_price($shipping_price); ?></td>
                <td width="1%"></td>
            </tr>
            <?php 
            
            //hide original shipping price
            if($count > 0) { 
                echo "<script>jQuery('#limelight_shipping_price').prev().hide();</script>";
            }
        }
        add_action('woocommerce_admin_order_totals_after_shipping', 'smash_woocommerce_admin_order_totals_after_shipping', 10, 2); 
    }
    
    include_once('libs/gateway.php');
    
    $ins = new WC_Smash_Limelight_Gateway;
    if($ins->is_available()) {
        include_once('libs/functions.php');
        include_once('libs/shortcodes.php');
        include_once('libs/admin.php');
        include_once('libs/front_common.php');
        include_once('libs/front_product.php');
    }
}

{
    /**
    * create table for updating logs
    * 
    */
    function smash_limelight_install() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'limelight_updates_log';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `time` datetime NOT NULL,
            `total` mediumint(8) unsigned NOT NULL DEFAULT '0',
            `total_limelight` mediumint(8) unsigned NOT NULL DEFAULT '0',
            `total_success` mediumint(8) unsigned NOT NULL DEFAULT '0',
            `fails_data` text,
            `note` text,
            PRIMARY KEY (`id`)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    register_activation_hook( __FILE__, 'smash_limelight_install' );
    
    function add_action_links ( $links ) {
        $mylinks = array(
            '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_smash_limelight_gateway' ) . '">Settings</a>',
        );
        return array_merge( $links, $mylinks );
    }
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );
}