<?php
/**
 * Plugin Name: WooCommerce Checkout Manager
 * Plugin URI: https://wordpress.org/plugins/woocommerce-checkout-manager/
 * Description: Manages WooCommerce Checkout, the advance way.
 * Author: Ephrain Marchan
 * Version: 4.0
 * Text Domain: woocommerce-checkout-manager
 * Domain Path: /languages/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include(plugin_dir_path( __FILE__ ).'includes/classes/main.php');	
include(plugin_dir_path( __FILE__ ).'includes/classes/export.php');
include(plugin_dir_path( __FILE__ ).'includes/classes/field_filters.php');
include(plugin_dir_path( __FILE__ ).'includes/classes/file_upload/main_upload.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/add_functions.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/billing_functions.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/shipping_functions.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/add_wooccmupload.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/billing_wooccmupload.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/shipping_wooccmupload.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/required/add_required.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/required/billing_required.php');
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/required/shipping_required.php'); 
include(plugin_dir_path( __FILE__ ).'includes/templates/functions/woocm_editing_wrapper.php');

add_action( 'wp_enqueue_scripts', 'jquery_wccs_emit' );
add_action( 'admin_enqueue_scripts', 'wooccm_pro_backend_scripts' );
add_action( 'plugins_loaded', 'wooccm_load_textdomain' );
add_action( 'woocommerce_before_checkout_form' , 'wccm_autocreate_account' );
add_action( 'woocommerce_email_after_order_table', 'add_payment_method_to_new_order', 10, 3);
add_action(	'woocommerce_checkout_update_order_meta', 'wccs_custom_checkout_field_pro_update_order_meta');
add_action(	'woocommerce_checkout_process', 'wccs_custom_checkout_field_pro_process');
add_action(	'woocommerce_order_details_after_customer_details', 'wccs_custom_checkout_details_pro');
add_action(	'woocommerce_checkout_after_customer_details','wccm_checkout_text_after');
add_action(	'woocommerce_checkout_before_customer_details','wccm_checkout_text_before');
add_action( 'woocommerce_admin_order_data_after_billing_address', 'delta_wccs_custom_checkout_details_pro_billing', 10, 1 );
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'delta_wccs_custom_checkout_details_pro_shipping', 10, 1 );
add_filter(	'woocommerce_checkout_fields','remove_fields_filter',15);
add_filter(	'woocommerce_checkout_fields','remove_fields_filter3',1);
add_action(	'wp_head','display_front_wccs_pro');
add_action(	'wp_head','billing_hide_required_wooccm');
add_action(	'wp_head','shipping_hide_required_wooccm');
// add_action(	'run_color_innerpicker','run_color_inner'); run color inside options page (proto)
add_action(	'woocommerce_before_checkout_form', 'override_this_wccs');
add_filter( 'woocommerce_billing_fields', 'wooccm_billing_fields', 1000 );
add_filter( 'woocommerce_shipping_fields', 'wooccm_shipping_fields', 1000 );
add_filter( 'wcdn_order_info_fields', 'wccm_woocommerce_delivery_notes_compat_pro', 10, 2 );
add_filter( 'wc_customer_order_csv_export_order_row', 'wooccm_pro_csv_export_modify_row_data', 10, 3 );
add_filter( 'wc_customer_order_csv_export_order_headers', 'wooccm_pro_csv_export_modify_column_headers' );
add_action( 'admin_init', 'wooccm_deactivate_plugin_conditional' );
add_filter( 'default_checkout_state', 'state_defaultSwitchWooccm' );
add_action(	'woocommerce_checkout_process', 'wccm_ccfcustomcheckoutprocessnow');
add_action(	'woocommerce_checkout_process', 'billing_wccm_ccfcustomcheckoutprocessnow');
add_action(	'woocommerce_checkout_process', 'shipping_wccm_ccfcustomcheckoutprocessnow');

add_action( 'woocommerce_before_checkout_form', 'upload_billing_scripts_enhanced');
add_action( 'woocommerce_before_checkout_form', 'upload_shipping_scripts_enhanced');
add_action(	'woocommerce_before_checkout_form', 'billing_scripts_enhanced');
add_action(	'woocommerce_before_checkout_form', 'shipping_scripts_enhanced');
add_action(	'woocommerce_before_checkout_form', 'billing_override_this_wccs');
add_action(	'woocommerce_before_checkout_form', 'shipping_override_this_wccs');
add_action( 'woocommerce_before_checkout_form', 'scripts_enhanced');
add_action( 'woocommerce_before_checkout_form', 'upload_scripts_enhanced');

add_action('woocommerce_checkout_fields', 'wooccm_order_notes');
add_filter( 'parse_query', 'wooccm_query_list' );
add_action( 'restrict_manage_posts', 'woooccm_restrict_manage_posts' );
register_activation_hook( __FILE__, 'wccs_install_pro' );


if ( wccs_positioning() == 'after_shipping_form' ) {
    add_action('woocommerce_before_checkout_shipping_form', 'wccs_custom_checkout_field_pro');
}elseif ( wccs_positioning() == 'after_shipping_form' ) {
    add_action('woocommerce_after_checkout_shipping_form', 'wccs_custom_checkout_field_pro');
}elseif ( wccs_positioning() == 'before_billing_form' ) {
    add_action('woocommerce_before_checkout_billing_form', 'wccs_custom_checkout_field_pro');
}elseif ( wccs_positioning() == 'after_billing_form' ) {
    add_action('woocommerce_after_checkout_billing_form', 'wccs_custom_checkout_field_pro');
}elseif ( wccs_positioning() == 'after_order_notes' ) {
    add_action('woocommerce_after_order_notes', 'wccs_custom_checkout_field_pro');
}

if ( validator_changename() ) {
    add_action('woocommerce_before_cart', 'wccm_before_checkout');
    add_action('woocommerce_admin_order_data_after_order_details', 'wccm_before_checkout');
    add_action('woocommerce_before_my_account', 'wccm_before_checkout');
    add_action('woocommerce_email_header', 'wccm_before_checkout');
    add_action('woocommerce_before_checkout_form', 'wccm_before_checkout');
    
    add_action('woocommerce_after_cart', 'wccm_after_checkout');
    add_action('woocommerce_admin_order_data_after_shipping_address', 'wccm_after_checkout');
    add_action('woocommerce_after_my_account', 'wccm_after_checkout');
    add_action('woocommerce_email_footer', 'wccm_after_checkout');
    add_action('woocommerce_after_checkout_form', 'wccm_after_checkout');

}

add_action('admin_menu', 'wccs_admin_menu');
function wccs_admin_menu() {
        add_menu_page( 'WooCheckout', 'WooCheckout', 'manage_options', 'woocommerce-checkout-manager' , 'wccs__options_page', 'dashicons-businessman', 57);
	add_submenu_page( 'woocommerce-checkout-manager' , '', '', 'manage_options', '', ''); 

		global $submenu;
		$submenu['woocommerce-checkout-manager'][0][0] = 'Settings';
}

if ( enable_auto_complete_wccs()) {
    add_action( 'woocommerce_before_checkout_form', 'retain_field_values_wccm' );
}


function wooccm_load_textdomain() {
    $options = get_option( 'wccs_settings' );
        if( !empty($options['checkness']['admin_translation']) ) {
            load_plugin_textdomain('woocommerce-checkout-manager', false, dirname(plugin_basename(__FILE__)) . '/languages/'); 
        }
}


function wccs_install_pro() {
    $options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
		$options3 = get_option( 'wccs_settings3' );
		
		update_option('wooccm_update_notice', 'no');
		
		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string('WooCommerce Checkout Manager', 'is a required field.', 'is a required field.');
		}
		
		if (empty($options['checkness']['position'])) {
            $options['checkness']['position'] = 'after_order_notes';
        }
	
        if (empty($options['checkness']['wooccm_notification_email'])) {
            $options['checkness']['wooccm_notification_email'] = get_option('admin_email');
        }
        
        if ( empty($options['checkness']['payment_method_d']) ) {
            $options['checkness']['payment_method_d'] = 'Payment Method';
        }

        if ( empty($options['checkness']['time_stamp_title']) ) {
            $options['checkness']['time_stamp_title'] = 'Order Time';
        }

        if ( empty($options['checkness']['payment_method_t']) ) {
            $options['checkness']['payment_method_t'] = '1';
        }

        if ( empty($options['checkness']['shipping_method_d']) ) {
            $options['checkness']['shipping_method_d'] = 'Shipping Method';
        }

        if ( empty($options['checkness']['shipping_method_t']) ) {
            $options['checkness']['shipping_method_t'] = '1';
        }


	    if ( empty($options2['shipping_buttons']) ) {
		    $shipping = array( 'country' => 'Country', 'first_name' => 'First Name', 'last_name' => 'Last Name', 'company' => 'Company Name', 'address_1' => 'Address', 'address_2' => '', 'city' => 'Town/ City', 'state' => 'State', 'postcode' => 'Zip');

		    $ship = 0;
    	    foreach( $shipping as $name => $value ) :
			
			    $options2['shipping_buttons'][$ship]['label'] = __(''.$value.'', 'woocommerce');
			    $options2['shipping_buttons'][$ship]['cow'] = $name;
		        $options2['shipping_buttons'][$ship]['checkbox']  = 'true';
			    $options2['shipping_buttons'][$ship]['order'] = $ship + 1;
			    $options2['shipping_buttons'][$ship]['type'] = 'wooccmtext';
	
				if ( $name == 'country') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
		        }	
                
		        if ( $name == 'first_name') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-first';
		        }	
                
		        if ( $name == 'last_name') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-last';
				    $options2['shipping_buttons'][$ship]['clear_row'] = true;
		        }
                
		        if ( $name == 'company') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
		        }
                
		        if ( $name == 'address_1') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
			       $options2['shipping_buttons'][$ship]['placeholder'] = __('Street address', 'woocommerce');
		        }
                
		        if ( $name == 'address_2') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
			        $options2['shipping_buttons'][$ship]['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'woocommerce');
		        }			
        
		        if ( $name == 'city') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
			        $options2['shipping_buttons'][$ship]['placeholder'] = __('Town / City', 'woocommerce');
		        }
                
		        if ( $name == 'state') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-first';
		        }
                
		        if ( $name == 'postcode') {
		            $options2['shipping_buttons'][$ship]['position'] = 'form-row-last';
			        $options2['shipping_buttons'][$ship]['placeholder'] = __('Postcode / Zip', 'woocommerce');
			        $options2['shipping_buttons'][$ship]['clear_row'] = true;
		        }
				 

		    $ship++;
    		endforeach;
	    }


	    if ( empty($options3['billing_buttons']) ) {
		    $billing = array( 'country' => 'Country', 'first_name' => 'First Name', 'last_name' => 'Last Name', 'company' => 'Company Name', 'address_1' => 'Address', 'address_2' => '', 'city' => 'Town/ City', 'state' => 'State', 'postcode' => 'Zip', 'email' => 'Email Address', 'phone' => 'Phone' );
		
		    $bill = 0;
    		foreach( $billing as $name => $value ) :
                
                $options3['billing_buttons'][$bill]['label'] = __(''.$value.'', 'woocommerce');
                $options3['billing_buttons'][$bill]['cow'] = $name;
                $options3['billing_buttons'][$bill]['checkbox']  = 'true';
                $options3['billing_buttons'][$bill]['order'] = $bill + 1;
				$options3['billing_buttons'][$bill]['type'] = 'wooccmtext';

				if ( $name == 'country') {
                    $options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
                }	
                
        		if ( $name == 'first_name') {
                    $options3['billing_buttons'][$bill]['position'] = 'form-row-first';
        		}		
                
        		if ( $name == 'last_name') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-last';
        			$options3['billing_buttons'][$bill]['clear_row'] = true;
        		}		
                
        		if ( $name == 'company') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        		}		
                
        		if ( $name == 'address_1') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        			$options3['billing_buttons'][$bill]['placeholder'] = __('Street address', 'woocommerce');
        		}		
                
        		if ( $name == 'address_2') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        			$options3['billing_buttons'][$bill]['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'woocommerce');
        		}		
                
        		if ( $name == 'city') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        			$options3['billing_buttons'][$bill]['placeholder'] = __('Town / City', 'woocommerce');
        		}			
                
        		if ( $name == 'state') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-first';
        		}		
                
        		if ( $name == 'postcode') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-last';
        			$options3['billing_buttons'][$bill]['placeholder'] = __('Postcode / Zip', 'woocommerce');
        				 $options3['billing_buttons'][$bill]['clear_row'] = true;
        		}				
                
        		if ( $name == 'email') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-first';
        		}		
                
        		if ( $name == 'phone') {
        		    $options3['billing_buttons'][$bill]['position'] = 'form-row-last';
        		    $options3['billing_buttons'][$bill]['clear_row'] = true;
        		}
		

		    $bill++;
    		endforeach;
	    }


        if ( !empty($options['buttons']) ) {
            foreach( $options['buttons'] as $i => $btn ) :
                
				if( !empty($btn['check_1']) || !empty($btn['check_2']) ) {
				 $options['buttons'][$i]['option_array'] = implode( '||', array(''.wpml_string_wccm_pro(''.$btn['check_1'].'').'',''.wpml_string_wccm_pro(''.$btn['check_2'].'').'') );
				 $options['buttons'][$i]['check_1'] = '';
				 $options['buttons'][$i]['check_2'] = '';
				} 
				
				$options['buttons'][$i]['type'] = ( $btn['type'] == 'checkbox' ) ? 'checkbox_wccm' : $btn['type'];
				$options['buttons'][$i]['type'] = ( $btn['type'] == 'text' ) ? 'wooccmtext' : $btn['type'];
				$options['buttons'][$i]['type'] = ( $btn['type'] == 'select' ) ? 'wooccmselect' : $btn['type'];
				$options['buttons'][$i]['type'] = ( $btn['type'] == 'date' ) ? 'datepicker' : $btn['type'];
				
                if (empty($btn['option_array'])) {
                    $btn['option_array'] = '';
                }
                
                $mysecureop = explode( '||', $btn['option_array']);
                
                if ( !empty($btn['option_a']) ) {
                    array_push($mysecureop, $btn['option_a'] );
                }
                
                if ( !empty($btn['option_b']) ) {
                    array_push($mysecureop, $btn['option_b'] );
                }
                
                $uniqueThevalues = array_unique($mysecureop);
        
                $options['buttons'][$i]['option_array'] = implode( '||', $uniqueThevalues);
        
            endforeach;
        }
		
			foreach( $options3['billing_buttons'] as $i => $btn ) :
                
				if( !empty($btn['check_1']) || !empty($btn['check_2']) ) {
				 $options3['billing_buttons'][$i]['option_array'] = implode( '||', array(''.wpml_string_wccm_pro(''.$btn['check_1'].'').'',''.wpml_string_wccm_pro(''.$btn['check_2'].'').'') );
				 $options3['billing_buttons'][$i]['check_1'] = '';
				 $options3['billing_buttons'][$i]['check_2'] = '';
				} 
				
				$options3['billing_buttons'][$i]['type'] = ( $btn['type'] == 'checkbox' ) ? 'checkbox_wccm' : $btn['type'];
				$options3['billing_buttons'][$i]['type'] = ( $btn['type'] == 'text' ) ? 'wooccmtext' : $btn['type'];
				$options3['billing_buttons'][$i]['type'] = ( $btn['type'] == 'select' ) ? 'wooccmselect' : $btn['type'];
				$options3['billing_buttons'][$i]['type'] = ( $btn['type'] == 'date' ) ? 'datepicker' : $btn['type'];
        
            endforeach;
			
			foreach( $options2['shipping_buttons'] as $i => $btn ) :
                
				if( !empty($btn['check_1']) || !empty($btn['check_2']) ) {
				 $options2['shipping_buttons'][$i]['option_array'] = implode( '||', array(''.wpml_string_wccm_pro(''.$btn['check_1'].'').'',''.wpml_string_wccm_pro(''.$btn['check_2'].'').'') );
				 $options2['shipping_buttons'][$i]['check_1'] = '';
				 $options2['shipping_buttons'][$i]['check_2'] = '';
				}

				$options2['shipping_buttons'][$i]['type'] = ( $btn['type'] == 'checkbox' ) ? 'checkbox_wccm' : $btn['type'];
				$options2['shipping_buttons'][$i]['type'] = ( $btn['type'] == 'text' ) ? 'wooccmtext' : $btn['type'];
				$options2['shipping_buttons'][$i]['type'] = ( $btn['type'] == 'select' ) ? 'wooccmselect' : $btn['type'];
				$options2['shipping_buttons'][$i]['type'] = ( $btn['type'] == 'date' ) ? 'datepicker' : $btn['type'];
        
            endforeach;
        
        update_option( 'wccs_settings', $options );
		update_option( 'wccs_settings2', $options2 );
			update_option( 'wccs_settings3', $options3 );
}





function jquery_wccs_emit() {
    global $woocommerce;
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	
		if( is_account_page() ) {
			wp_enqueue_style('dashicons');
			wp_enqueue_style('wooccm-button-style', plugins_url( '/woocommerce-checkout-manager/includes/classes/file_upload/button_style.css'), false, '1.0', 'all');
		}
		
        if ( is_checkout() ) {
            $current_language = ICL_LANGUAGE_CODE;
			
            if ( function_exists( 'icl_register_string' ) && ICL_LANGUAGE_CODE == $current_language && ICL_LANGUAGE_CODE !== 'en') {
                wp_register_script('jquery.ui.datepicker-'.$current_language.'', plugins_url('woocommerce-checkout-manager/includes/pickers/di18n/jquery.ui.datepicker-'.$current_language.'.js', dirname(__FILE__)));
                wp_enqueue_script('jquery.ui.datepicker-'.$current_language.'');
            }
            
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-style', plugins_url('woocommerce-checkout-manager/includes/pickers/jquery.ui.css') );


            // http://fgelinas.com/code/timepicker/
            wp_enqueue_script('jquery-ui-timepicker', plugins_url('woocommerce-checkout-manager/includes/pickers/jquery.ui.timepicker.js') );
            wp_enqueue_style('jquery-ui-timepicker', plugins_url('woocommerce-checkout-manager/includes/pickers/jquery.ui.timepicker.css') );
            wp_enqueue_style('jquery-ui-timepicker-min', plugins_url('woocommerce-checkout-manager/includes/pickers/include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css') );
            
			wp_enqueue_script( 'jquery-lib', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js' );
			
            wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'iris', plugins_url('woocommerce-checkout-manager/includes/pickers/iris/dist/iris.js'), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
			wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, 1 );
			
			 // load the style and script for farbtastic
            wp_enqueue_style( 'farbtastic' );
            wp_enqueue_script( 'farbtastic', site_url('/wp-admin/js/farbtastic.js') );
			

			wp_enqueue_style('dashicons');
			
			wp_enqueue_style( 'magnific-popup', plugins_url('woocommerce-checkout-manager/includes/pickers/magnificpopup/dist/magnific-popup.css')  );
			wp_enqueue_script( 'magnific-popup', plugins_url('woocommerce-checkout-manager/includes/pickers/magnificpopup/dist/jquery.magnific-popup.js') );
			
			wp_enqueue_script( 'caman', plugins_url('woocommerce-checkout-manager/includes/pickers/caman/dist/caman.js') );
			wp_enqueue_style( 'caman', plugins_url('woocommerce-checkout-manager/includes/pickers/caman/dist/caman.css') );
			
			wp_enqueue_script( 'jcrop-color', plugins_url('woocommerce-checkout-manager/includes/pickers/jcrop/js/jquery.color.js') );
			wp_enqueue_script( 'jcrop', plugins_url('woocommerce-checkout-manager/includes/pickers/jcrop/js/jquery.Jcrop.js') );

        }
}


// backend scripts
function wooccm_pro_backend_scripts( $hook_suffix ) {
	
	if ( $hook_suffix == 'toplevel_page_woocommerce-checkout-manager' ) {
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic', site_url('/wp-admin/js/farbtastic.js') );   
		wp_enqueue_style('wooccm-backend-css', plugins_url('woocommerce-checkout-manager/includes/pickers/css/backend_css.css') );      
		wp_enqueue_script( 'script_wccs', plugins_url( 'includes/templates/js/script_wccs.js', __FILE__ ), array( 'jquery' ), '1.2' );
		wp_enqueue_script( 'billing_script_wccs', plugins_url( 'includes/templates/js/billing_script_wccs.js', __FILE__ ), array( 'jquery' ), '1.2' );
		wp_enqueue_script( 'shipping_script_wccs', plugins_url( 'includes/templates/js/shipping_script_wccs.js', __FILE__ ), array( 'jquery' ), '1.2' );
			
        	if( !wp_script_is('jquery-ui-sortable', 'queue') ){
               		wp_enqueue_script('jquery-ui-sortable');
         	}
	}
	
	if( $hook_suffix === 'woocheckout_page_wooccm-advance-export') {
		wp_enqueue_style( 'export', plugins_url('woocommerce-checkout-manager/includes/classes/sc/export.css') );
	}
	
}


if ( is_admin() ){ 
	add_action('admin_menu', 'wccs_register_export');
    add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'wccs_admin_plugin_actions_pro', -10);
    add_action( 'admin_init', 'wccs_register_setting_pro' );
}

function wccs_register_setting_pro() {
    register_setting( 'wccs_options', 'wccs_settings', 'wccs_options_validate_pro' );
	register_setting( 'wccs_options2', 'wccs_settings2', 'wccs_options_validate_pro2' );
	register_setting( 'wccs_options3', 'wccs_settings3', 'wccs_options_validate_pro3' );
}

function wccs_register_export() {
		add_submenu_page( 'woocommerce-checkout-manager', 'Export', 'Export', 'manage_options', 'wooccm-advance-export', 'wooccm_advance_export' );
}

function wccs__options_page() {
        if ( !current_user_can('manage_options') ) { 
            wp_die( __('You do not have sufficient permissions to access this page.', 'woocommerce-checkout-manager') ); 
        }

$htmlshippingabbr = array( 'country', 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode' );
$htmlbillingabbr = array( 'country', 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'email', 'phone' );
$upload_dir = wp_upload_dir();
$hidden_field_name = 'mccs_submit_hidden';
$hidden_wccs_reset = "my_new_field_reset";
$options = get_option( 'wccs_settings' );
$options2 = get_option( 'wccs_settings2' );
$options3 = get_option( 'wccs_settings3' );

        if( isset($_POST[ $hidden_wccs_reset ]) && $_POST[ $hidden_wccs_reset ] == 'Y' ) {
            delete_option('wccs_settings');
			 delete_option('wccs_settings2');
				 delete_option('wccs_settings3');

            $defaults = array(
                'checkness' => array(
                                    'position' => 'after_billing_form',
                                    'wooccm_notification_email' => ''.get_option('admin_email').'',
                                    'payment_method_t' => true,
                                    'shipping_method_t' => true,
                                    'payment_method_d' => __('Payment Method','woocommerce-checkout-manager'),
                                    'shipping_method_d' => __('Shipping Method','woocommerce-checkout-manager'),
                                    'time_stamp_title' => __('Order Time','woocommerce-checkout-manager'),
                                ),
            
                );

		    $shipping = array( 'country' => 'Country', 'first_name' => 'First Name', 'last_name' => 'Last Name', 'company' => 'Company Name', 'address_1' => 'Address', 'address_2' => '', 'city' => 'Town/ City', 'state' => 'State', 'postcode' => 'Zip' );

		    $ship = 0;
    		foreach( $shipping as $name => $value ) :
			
    			$defaults2['shipping_buttons'][$ship]['label'] = __(''.$value.'', 'woocommerce');
    			$defaults2['shipping_buttons'][$ship]['cow'] = $name;
    			$defaults2['shipping_buttons'][$ship]['checkbox']  = 'true';
    			$defaults2['shipping_buttons'][$ship]['order'] = $ship + 1;
			$defaults2['shipping_buttons'][$ship]['type'] = 'wooccmtext';

    			if ( $name == 'country') {
    		        $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
    		    }	
                
    		    if ( $name == 'first_name') {
    		        $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-first';
    		    }
                
    		    if ( $name == 'last_name') {
    		        $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-last';
    			    $defaults2['shipping_buttons'][$ship]['clear_row'] = true;
    		    }
                    
        		if ( $name == 'company') {
        		    $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
        		}				
                
        		if ( $name == 'address_1') {
        		    $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
        			$defaults2['shipping_buttons'][$ship]['placeholder'] = __('Street address', 'woocommerce');
        		}			
                
        		if ( $name == 'address_2') {
        		    $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
        			$defaults2['shipping_buttons'][$ship]['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'woocommerce');
        		}	
                
        		if ( $name == 'city') {
        		    $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-wide';
        			$defaults2['shipping_buttons'][$ship]['placeholder'] = __('Town / City', 'woocommerce');
        		}	
                
        		if ( $name == 'state') {
        		    $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-first';
        		}		
                
        		if ( $name == 'postcode') {
        		    $defaults2['shipping_buttons'][$ship]['position'] = 'form-row-last';
        			$defaults2['shipping_buttons'][$ship]['placeholder'] = __('Postcode / Zip', 'woocommerce');
        		    $defaults2['shipping_buttons'][$ship]['clear_row'] = true;
        		}							

            $ship++;
            endforeach;


		$billing = array( 'country' => 'Country', 'first_name' => 'First Name', 'last_name' => 'Last Name', 'company' => 'Company Name', 'address_1' => 'Address', 'address_2' => '', 'city' => 'Town/ City', 'state' => 'State', 'postcode' => 'Zip', 'email' => 'Email Address', 'phone' => 'Phone' );
		
		$bill = 0;
    		foreach( $billing as $name => $value ) :

    			$defaults3['billing_buttons'][$bill]['label'] = __(''.$value.'', 'woocommerce');
    			$defaults3['billing_buttons'][$bill]['cow'] = $name;
			$defaults3['billing_buttons'][$bill]['checkbox']  = 'true';
    			$defaults3['billing_buttons'][$bill]['order'] = $bill + 1;	
			$defaults3['billing_buttons'][$bill]['type'] = 'wooccmtext';
			
				if ( $name == 'country') {
		            $defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        		}	
                
        		if ( $name == 'first_name') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-first';
        		}		
                
        		if ( $name == 'last_name') {
                    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-last';
                    $defaults3['billing_buttons'][$bill]['clear_row'] = true;
        		}			
                
        		if ( $name == 'company') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        		}				
                
        		if ( $name == 'address_1') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        			$defaults3['billing_buttons'][$bill]['placeholder'] = __('Street address', 'woocommerce');
        		}			
                
        		if ( $name == 'address_2') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        			$defaults3['billing_buttons'][$bill]['placeholder'] = __('Apartment, suite, unit etc. (optional)', 'woocommerce');
        		}				
                
        		if ( $name == 'city') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-wide';
        			$defaults3['billing_buttons'][$bill]['placeholder'] = __('Town / City', 'woocommerce');
        		}				
                
        		if ( $name == 'state') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-first';
        		}				
                
        		if ( $name == 'postcode') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-last';
        			$defaults3['billing_buttons'][$bill]['placeholder'] = __('Postcode / Zip', 'woocommerce');
        		    $defaults3['billing_buttons'][$bill]['clear_row'] = true;
        		}				
                
        		if ( $name == 'email') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-first';
        		}		
                
        		if ( $name == 'phone') {
        		    $defaults3['billing_buttons'][$bill]['position'] = 'form-row-last';
        		    $defaults3['billing_buttons'][$bill]['clear_row'] = true;
        		}

		    $bill++;
    		endforeach;

            add_option( 'wccs_settings' , $defaults );
		 add_option( 'wccs_settings2' , $defaults2 );
			 add_option( 'wccs_settings3' , $defaults3 );


	        echo '<script type="text/javascript">window.location.href="'.$_SERVER['PHP_SELF'].'?page=woocommerce-checkout-manager";</script><noscript><meta http-equiv="refresh" content="0;url='.$_SERVER['PHP_SELF'].'?page=woocommerce-checkout-manager" /></noscript>';exit; 
        }

echo '<script type="text/javascript" src="'.plugins_url('/woocommerce/assets/js/jquery-blockui/jquery.blockUI.js').'"></script>';
echo '<div class="refreshwooccm">';

	if ( get_option('wooccm_update_notice') != 'yep' ) {
		display_notices_wooccm();
	}
	
	    // display error
	    settings_errors();

            // Now display the settings editing screen
            echo '<div class="wrap"></div>';

            // header
            echo '<div style="clear:both;">
                    <h2 class="nav-tab-wrapper add_tip_wrap">
                        <span class="wooccm_name_heading">' . __( 'WooCommerce Checkout Manager', 'woocommerce-checkout-manager' ) . '</span>
                            <a class="nav-tab general-tab nav-tab-active">' . __( 'General', 'woocommerce-checkout-manager' ) . '</a>
                            <a class="nav-tab billing-tab">' . __( 'Billing', 'woocommerce-checkout-manager' ) . '</a>
                            <a class="nav-tab shipping-tab">' . __( 'Shipping', 'woocommerce-checkout-manager' ) . '</a>
                            <a class="nav-tab additional-tab">' . __( 'Additional', 'woocommerce-checkout-manager' ) . '</a>
		 	    <a class="nav-tab star" href="https://wordpress.org/support/view/plugin-reviews/woocommerce-checkout-manager?filter=5"><div id="star-five" title="'.__('Like the plugin? Rate it! On WordPress.org', 'woocommerce-checkout-manager' ) . '"><div class="star-rating"><div class="star star-full"></div><div class="star star-full"></div><div class="star star-full"></div><div class="star star-full"></div><div class="star star-full"></div></div></div></a>
                      </h2>
                    </div>';
?>

			<?php do_action('run_color_innerpicker'); ?>


            <form name="reset_form" class="reset_form" method="post" action="">
                <input type="hidden" name="<?php echo $hidden_wccs_reset; ?>" value="Y">
                <input type="submit" name="submit" id="wccs_reset_submit" class="button button-hero" value="Reset">
            </form>


		
            <?php require(plugin_dir_path( __FILE__ ).'includes/classes/import.php'); ?>




<form name="wooccmform2" method="post" action="options.php" id="frm2">

		<?php settings_fields( 'wccs_options2' ); ?>

                <input type="submit" style="display:none;" name="Submit" class="save-shipping wccs_submit_button button button-primary button-hero" value="<?php _e( 'Save Changes', 'woocommerce-checkout-manager' ); ?>" />

		<!-- SHIPPING SECTION -->

	<table class="widefat shipping-wccs-table shipping-semi" style="display:none;" border="1" name="shipping_table">
                    
                <thead>
                    <tr>
                        <th style="width:3%;" class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlheadship.php'); ?>
                        
                        <th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
                    </tr>
                </thead>
                    
                    
                    
                <tbody>
                    
                    <?php
                    if ( isset ( $options2['shipping_buttons'] ) ) :
                    
	$shipping = array( 'country', 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode' );

                        for ( $ix = 0; $ix < count( $options2['shipping_buttons'] ); $ix++ ) :
                    
                            if ( ! isset( $options2['shipping_buttons'][$ix] ) )
                            break;
                    
                    ?>
                    
                    <tr valign="top" class="shipping-wccs-row">

<td style="display:none;" class="shipping-wccs-order-hidden" >
<input type="hidden" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][order]" value="<?php echo (empty( $options2['shipping_buttons'][$ix]['order'])) ? $ix :  $options2['shipping_buttons'][$ix]['order']; ?>" />
</td>

                        <td class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $ix+1; ?></td>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlbodyship.php'); ?>
                        
                        <?php if( in_array( $options2['shipping_buttons'][$ix]['cow'],$shipping) ) { ?>
                        <td style="text-align:center;"><input name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][disabled]" type="checkbox" value="true" <?php if (  !empty ($options2['shipping_buttons'][$ix]['disabled'])) echo "checked='checked'"; ?> /></td>
			<?php } else { 
			echo '<td class="shipping-wccs-remove"><a class="shipping-wccs-remove-button" href="javascript:;" >&times;</a></td>';
			} ?>
                        
                    </tr>
                    
                    <?php endfor; endif; ?>
                    <!-- Empty -->
                    
                    <?php $ix = 999; ?>
                    
                    <tr valign="top" class="shipping-wccs-clone" >

<td style="display:none;" class="shipping-wccs-order-hidden" >
<input type="hidden" name="wccs_settings2[shipping_buttons][<?php echo $ix; ?>][order]" value="<?php echo $ix; ?>" />
</td>

                        <td class="shipping-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $ix; ?></td>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlbodycloneship.php'); ?>

                        <td class="shipping-wccs-remove"><a class="shipping-wccs-remove-button" href="javascript:;">&times;</a></td>
                        
                    </tr>
                </tbody>      
            </table>
                    
                    
            <div class="shipping-wccs-table-footer shipping-semi" style="display:none;">
                <a href="javascript:;" id="shipping-wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
            </div>
		<!-- END SHIPPING SECTION -->

</form>


<form name="wooccmform3" method="post" action="options.php" id="frm3">

		<?php settings_fields( 'wccs_options3' ); ?>

                <input type="submit" name="Submit" style="display:none;" class="save-billing wccs_submit_button button button-primary button-hero" value="<?php _e( 'Save Changes', 'woocommerce-checkout-manager' ); ?>" />

      
               <!-- BILLING SECTION -->

	<table class="widefat billing-wccs-table billing-semi" style="display:none;" border="1" name="billing_table">
                    
                <thead>
                    <tr>
                        <th style="width:3%;" class="billing-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlheadbill.php'); ?>
                        
                        <th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
                    </tr>
                </thead>
                    
                    
                    
                <tbody>
                    
                    <?php
                    if ( isset ( $options3['billing_buttons'] ) ) :

$billing = array( 'country', 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'email', 'phone' );
                    
                        for ( $i = 0; $i < count( $options3['billing_buttons'] ); $i++ ) :
                    
                            if ( ! isset( $options3['billing_buttons'][$i] ) )
                            break;
                    
                    ?>
                    
                    <tr valign="top" class="billing-wccs-row">
<td style="display:none;" class="billing-wccs-order-hidden" >
<input type="hidden" name="wccs_settings3[billing_buttons][<?php echo $i; ?>][order]" value="<?php echo (empty( $options3['billing_buttons'][$i]['order'])) ? $i :  $options3['billing_buttons'][$i]['order']; ?>" />
</td>
                         <td class="billing-wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $i+1; ?></td>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlbodybill.php'); ?>
                        
			<?php if( in_array($options3['billing_buttons'][$i]['cow'], $billing) ) { ?>
                        <td style="text-align:center;"><input name="wccs_settings3[billing_buttons][<?php echo $i; ?>][disabled]" type="checkbox" value="true" <?php if (  !empty ($options3['billing_buttons'][$i]['disabled'])) echo "checked='checked'"; ?> /></td>
			<?php } else { 
			echo '<td class="billing-wccs-remove"><a class="billing-wccs-remove-button" href="javascript:;">&times;</a></td>';
			} ?>

                    </tr>
                    
                    <?php endfor; endif; ?>
                    <!-- Empty -->
                    
                    <?php $i = 999; ?>
                    
                    <tr valign="top" class="billing-wccs-clone" >

                    <td style="display:none;" class="billing-wccs-order-hidden"><input type="hidden" name="wccs_settings3[billing_buttons][<?php echo $i; ?>][order]" value="<?php echo $i; ?>" /></td>

                        <td class="billing-wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>"><?php echo $i; ?></td>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlbodyclonebill.php'); ?>

                        <td class="billing-wccs-remove"><a class="billing-wccs-remove-button" href="javascript:;" >&times;</a></td>
                        
                    </tr>
                </tbody>      
            </table>
                    
                    
            <div class="billing-wccs-table-footer billing-semi" style="display:none;">
                <a href="javascript:;" id="billing-wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
            </div>

		<!-- END BILLING SECTION -->                         

</form>


            <form name="wooccmform" method="post" action="options.php" id="frm1">


		<?php settings_fields( 'wccs_options' ); ?>

                <input type="submit" name="Submit" class="save-additional wccs_submit_button button button-primary button-hero" value="<?php _e( 'Save Changes', 'woocommerce-checkout-manager' ); ?>" />
			  
                
                <div id="general-semi-nav">
                
                <div id="main-nav-left">
                	    <ul>
                    	  <li class="upload_class current"><a title="Upload">Upload</a></li>
                    	  <li class="address_fields_class"><a title="Address Fields">Hide Address Fields</a></li>
                    	  <li class="checkout_notice_class"><a title="Checkout Notice">Checkout Notice</a></li>
                    	  <li class="switches_class"><a title="Switches">Switches</a></li>
			  <li class="order_notes_class"><a title="Order Notes">Handlers</a></li>
                    	  <li class="custom_css_class"><a title="Custom CSS">Custom CSS</a></li>
                	    </ul>
                </div>
                

                <div id="content-nav-right" class="general-vibe">
                
                <?php
                    // file upload options section
                    require(plugin_dir_path( __FILE__ ).'includes/classes/file_upload/upload_settings.php'); 
                ?>



<!-- ADDITIONAL SECTION -->

                    
            <table class="widefat wccs-table additional-semi" style="display:none;" border="1" name="additional_table">
                    
                <thead>
                    <tr>
                        <th style="width:3%;" class="wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>">#</th>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlheadadd.php'); ?>
                        
                        <th width="1%" scope="col" title="<?php esc_attr_e( 'Remove button', 'woocommerce-checkout-manager' ); ?>"><strong>X</strong><!-- remove --></th>
                    </tr>
                </thead>
                    
                    
                    
                <tbody>
                    
                    <?php
                    if ( isset ( $options['buttons'] ) ) :
                    
                        for ( $iz = 0; $iz < count( $options['buttons'] ); $iz++ ) :
                    
                            if ( ! isset( $options['buttons'][$iz] ) )
                            break;
                    
                    ?>
                    
                    <tr valign="top" class="wccs-row">
                        <td style="display:none;" class="wccs-order-hidden" >
<input type="hidden" name="wccs_settings[buttons][<?php echo $iz; ?>][order]" value="<?php echo (empty( $options['buttons'][$iz]['order'])) ? $iz :  $options['buttons'][$iz]['order']; ?>" />
</td>

<td class="wccs-order" title="<?php esc_attr_e( 'Change order', 'woocommerce-checkout-manager' ); ?>"><?php echo $iz+1; ?></td>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlbodyadd.php'); ?>
                        
                        <td class="wccs-remove"><a class="wccs-remove-button" href="javascript:;" title="<?php esc_attr_e( 'Remove Field' , 'woocommerce-checkout-manager' ); ?>">&times;</a></td>
                        
                    </tr>
                    
                    <?php endfor; endif; ?>
                    <!-- Empty -->
                    
                    <?php $iz = 999; ?>
                    
<tr valign="top" class="wccs-clone" >

                    <td style="display:none;" class="wccs-order-hidden"><input type="hidden" name="wccs_settings[buttons][<?php echo $iz; ?>][order]" value="<?php echo $iz; ?>" /></td>

                        <td class="wccs-order" title="<?php esc_attr_e( 'Change order' , 'woocommerce-checkout-manager' ); ?>"><?php echo $iz; ?></td>
                        
                        <?php require(plugin_dir_path( __FILE__ ).'includes/templates/htmlbodycloneadd.php'); ?>

                        <td class="wccs-remove"><a class="wccs-remove-button" href="javascript:;" title="<?php esc_attr_e( 'Remove Field' , 'woocommerce-checkout-manager' ); ?>">&times;</a></td>
                        
                    </tr>
                </tbody>      
            </table>
                    
                    
            <div class="wccs-table-footer additional-semi" style="display:none;">
                <a href="javascript:;" id="wccs-add-button" class="button-secondary"><?php _e( '+ Add New Field' , 'woocommerce-checkout-manager' ); ?></a>
            </div>

		<!-- END ADDITIONAL SECTION -->


                    
                    
            <div class="widefat general-semi address_fields" border="1" style="display:none;">
                    
                    <div class="section">
                        <h3 class="heading"><?php _e('Disable Billing Address fields for certain products', 'woocommerce-checkout-manager');  ?></h3>
                    
                        <div class="option">
                            <input type="text" name="wccs_settings[checkness][productssave]" style="width: 100%;" value="<?php echo (empty($options['checkness']['productssave'])) ? '' : $options['checkness']['productssave']; ?>" />
                            <h3 class="heading address"><div class="info-of"><?php _e('To get product number, goto the listing of WooCoommerce Products then hover over each product and you will see ID. Example', 'woocommerce-checkout-manager'); ?> "ID: 3651"</div></h3>
                        </div>
                    </div>
                    
            </div>
                    

	<div class="widefat general-semi order_notes" border="1" style="display:none;">
                    
                    <div class="section">
                        <h3 class="heading">
				<?php _e('Order Notes','woocommerce-checkout-manager'); ?>
			</h3>
			
                
			<div style="float:left;width: 46%;" class="option">
                            <input style="width: 100%;clear:both;" name="wccs_settings[checkness][noteslabel]" type="text" value="<?php echo $options['checkness']['noteslabel']; ?>" />
                            <div class="info-of" style="font-weight:700;margin-top:5px;text-align:center;"><?php _e('Order Notes Label', 'woocommerce-checkout-manager');  ?></div>
                        </div>
                        
                        <div style="float:left;width: 47%;" class="option">
                            <input style="width: 100%;clear:both;" name="wccs_settings[checkness][notesplaceholder]" type="text" value="<?php echo (empty($options['checkness']['notesplaceholder'])) ? '' : $options['checkness']['notesplaceholder']; ?>" />
                            <div class="info-of" style="font-weight:700;margin-top:5px;text-align:center;"><?php _e('Order Notes Placeholder', 'woocommerce-checkout-manager');  ?></div>
                        </div>

			<h3 class="heading checkbox" style="clear:both;">
                            <div class="option">
                                <input name="wccs_settings[checkness][notesenable]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['notesenable'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Disable Order Notes.', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>

			</div>


		<div class="section">
                        <h3 class="heading"><?php _e('Time order was purchased', 'woocommerce-checkout-manager');  ?></h3>
                        
                        <div style="float:left;width: 46%;" class="option">
                            <input style="width: 100%;clear:both;" name="wccs_settings[checkness][time_stamp_title]" type="text" value="<?php echo $options['checkness']['time_stamp_title']; ?>" />
                            <div class="info-of" style="font-weight:700;margin-top:5px;text-align:center;"><?php _e('Order time title', 'woocommerce-checkout-manager');  ?></div>
                        </div>
                        
                        <div style="float:left;width: 47%;" class="option">
                            <input style="width: 100%;clear:both;" name="wccs_settings[checkness][set_timezone]" type="text" value="<?php echo (empty($options['checkness']['set_timezone'])) ? '' : $options['checkness']['set_timezone']; ?>" />
                            <div class="info-of" style="font-weight:700;margin-top:5px;text-align:center;"><?php _e('Set TimeZone', 'woocommerce-checkout-manager');  ?></div>
                        </div>
                        
                        <h3 class="heading checkbox" style="clear:both;">
                            <div class="option">
                                <input name="wccs_settings[checkness][time_stamp]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['time_stamp'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Enable display of order time.', 'woocommerce-checkout-manager');  ?></div>
                            </div>
			   <div class="option">
                                <input name="wccs_settings[checkness][twenty_hour]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['twenty_hour]'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Enable 24 hour time.', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    
                    
                    <div class="section">
                        <h3 class="heading"><?php _e('Payment method used by customer', 'woocommerce-checkout-manager');  ?></h3>
                        
			<div class="option">
                        <input style="width: 50%;" name="wccs_settings[checkness][payment_method_d]" type="text" value="<?php echo $options['checkness']['payment_method_d']; ?>" />
			</div>

                        <h3 class="heading checkbox">
                            <div class="option">
                                <input name="wccs_settings[checkness][payment_method_t]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['payment_method_t'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Enable display of payment method.', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    
                    
                    <div class="section">
                        <h3 class="heading"><?php _e('Shipping method used by customer', 'woocommerce-checkout-manager');  ?></h3>
                        
			<div class="option">
                        <input style="width: 50%;" name="wccs_settings[checkness][shipping_method_d]" type="text" value="<?php echo $options['checkness']['shipping_method_d']; ?>" />
			</div>

                        <h3 class="heading checkbox">
                            <div class="option">
                                <input name="wccs_settings[checkness][shipping_method_t]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['shipping_method_t'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Enable display of shipping method.', 'woocommerce-checkout-manager');  ?></div>
                             </div>
                        </h3>
                    </div>
                    
                    
                    
                    <div class="section">
                        <h3 class="heading"><?php _e('Default sate code for checkout.', 'woocommerce-checkout-manager');  ?></h3>
                        
			<div class="option">
                        <input placeholder="ND" style="width: 50%;" name="wccs_settings[checkness][per_state]" type="text" value="<?php echo (empty($options['checkness']['per_state'])) ? '' : $options['checkness']['per_state']; ?>" />
                        </div>

                        <h3 class="heading checkbox">
                            <div class="option">
                                <input name="wccs_settings[checkness][per_state_check]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['per_state_check'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Enable default sate code.', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>


		<br />
		

            </div>


                    
            <div class="widefat general-semi custom_css" border="1" style="display:none;">
                    
                    <div class="section">
                        <h3 class="heading"><?php _e('Custom CSS','woocommerce-checkout-manager'); ?></strong></h3>
                    
                        <h3 class="heading checkbox">
                        
                            <div class="option">
                                <div class="info-of">
                                    <?php _e('CSS language stands for Cascading Style Sheets which is used to style html content. You can change the fonts size, colours, margins of content, which lines to show or input, adjust height, width, background images etc.','woocommerce-checkout-manager'); ?>
                                    <?php _e('Get help in our', 'woocommerce-checkout-manager');  ?> <a href="http://www.trottyzone.com/forums/forum/wordpress-plugins/"><?php _e('Support Forum', 'woocommerce-checkout-manager');  ?></a>.
                                </div>
                            </div>
                        </h3>
                    
                        <textarea type="text" name="wccs_settings[checkness][custom_css_w]" style="height:200px;width: 100%;"><?php echo (empty($options['checkness']['custom_css_w'])) ? '' : $options['checkness']['custom_css_w']; ?></textarea>
                    </div>
            </div>
                    
                    
                    
                   
                   
            <div class="widefat general-semi checkout_notices" border="1" style="display:none;" >
                    
                    <div class="section">
                        <h3 class="heading"><?php _e('Position for notification one', 'woocommerce-checkout-manager');  ?></h3>
                    
                        <h3 class="heading checkbox">
                            <div class="option">
                                <input style="float:left;" name="wccs_settings[checkness][checkbox1]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['checkbox1'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Before Customer Address Fields', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    
                    
                        <h3 class="heading checkbox">
                            <div class="option">
                                 <input style="float:left;" name="wccs_settings[checkness][checkbox2]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['checkbox2'])) echo "checked='checked'"; ?> /><span></span>
                                 <div class="info-of"><?php _e('Before Order Summary', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    
                        <div class="option">
                            <div class="info-of"><?php _e('Notification text area: You can use class', 'woocommerce-checkout-manager');  ?> "woocommerce-info" <?php _e('for the same design as WooCommerce Coupon.', 'woocommerce-checkout-manager');  ?></div>
                            <textarea style="width:100%;height:150px;" name="wccs_settings[checkness][text1]" type="textarea"><?php echo (empty($options['checkness']['text1'] )) ? '' : esc_attr( $options['checkness']['text1'] ); ?></textarea>
                        </div>
                        
                    </div>
                    
                    
                    
                    <div class="section">
                        <h3 class="heading"><?php _e('Position for notification two', 'woocommerce-checkout-manager');  ?></h3>
                        
                        <h3 class="heading checkbox">
                            <div class="option">
                                <input style="float:left;" name="wccs_settings[checkness][checkbox3]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['checkbox3'])) echo "checked='checked'"; ?> /></th><span></span>
                                <div class="info-of"><?php _e('Before Customer Address Fields', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                        
                        <h3 class="heading checkbox">
                            <div class="option">
                                <input style="float:left;" name="wccs_settings[checkness][checkbox4]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['checkbox4'])) echo "checked='checked'"; ?> /></th><span></span>
                                <div class="info-of"><?php _e('Before Order Summary', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                        
                        <div class="option">
                            <div class="info-of"><?php _e('Notification text area: You can use class', 'woocommerce-checkout-manager');  ?> "woocommerce-info" <?php _e('for the same design as WooCommerce Coupon.', 'woocommerce-checkout-manager');  ?></div>
                            <textarea style="width:100%;height:150px;" name="wccs_settings[checkness][text2]" type="textarea"><?php echo (empty( $options['checkness']['text2'] )) ? '' : esc_attr( $options['checkness']['text2'] ); ?></textarea>
                        </div>
                    </div>    
            </div>
                    
                    
                    
                    
                    
                    
            <div class="widefat general-semi switches" border="1" style="display:none;">
                    
                    <div class="section"><h3 class="heading"><?php _e('General Switches', 'woocommerce-checkout-manager'); ?></h3></div>
                    <div class="section">
                        <h3 class="heading checkbox">  
                            <div class="option">
                                <input name="wccs_settings[checkness][additional_info]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['additional_info'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Remove Additional Information title', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    
                    <div class="section">
                        <h3 class="heading checkbox">
                            <div class="option">
                                <input name="wccs_settings[checkness][admin_translation]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['admin_translation'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Translate WooCCM Options Panel', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    
                    <div class="section">
                        <h3 class="heading checkbox">
                            <div class="option">
                                <input name="wccs_settings[checkness][auto_create_wccm_account]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['auto_create_wccm_account'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Hide registration checkbox', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    <div class="section">
                        <h3 class="heading checkbox">
                            <div class="option">
                                <input name="wccs_settings[checkness][retainval]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['retainval'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Retain Fields Information', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
					
					<div class="section">
                        <h3 class="heading checkbox">
                            <div class="option">
                                <input name="wccs_settings[checkness][abbreviation]" type="checkbox" value="true" <?php if (  !empty ($options['checkness']['abbreviation'])) echo "checked='checked'"; ?> /><span></span>
                                <div class="info-of"><?php _e('Editing Of Abbreviation Fields', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    <div class="section"><h3 class="heading"><?php _e('Additional Fields Positions', 'woocommerce-checkout-manager'); ?></h3></div>

                    <div class="section">
                        <h3 class="heading checkbox radio">
                            <div class="option">
                                <input name="wccs_settings[checkness][position]" type="radio" value="before_shipping_form" <?php checked( $options['checkness']['position'], 'before_shipping_form' ); ?> />
                                <div class="info-of"><?php _e('Before Shipping Form', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    <div class="section">
                        <h3 class="heading checkbox radio">
                            <div class="option">
                                <input name="wccs_settings[checkness][position]" type="radio" value="after_shipping_form" <?php checked( $options['checkness']['position'], 'after_shipping_form' ); ?> />
                                <div class="info-of"><?php _e('After Shipping Form', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    <div class="section">
                        <h3 class="heading checkbox radio">
                            <div class="option">
                                <input name="wccs_settings[checkness][position]" type="radio" value="before_billing_form" <?php checked( $options['checkness']['position'], 'before_billing_form' ); ?> />
                                <div class="info-of"><?php _e('Before Billing Form', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    <div class="section">
                        <h3 class="heading checkbox radio">
                            <div class="option">
                                <input name="wccs_settings[checkness][position]" type="radio" value="after_billing_form" <?php checked( $options['checkness']['position'], 'after_billing_form' ); ?> />
                                <div class="info-of"><?php _e('After Billing Form', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    <div class="section">
                        <h3 class="heading checkbox radio">
                            <div class="option">
                                <input name="wccs_settings[checkness][position]" type="radio" value="after_order_notes" <?php checked( $options['checkness']['position'], 'after_order_notes' ); ?> />
                                <div class="info-of"><?php _e('After Order Notes', 'woocommerce-checkout-manager');  ?></div>
                            </div>
                        </h3>
                    </div>
                    
                    
                    
                    
                    
            </div>
                                        
                    
            </div>
            </div>       
            </form>
</div>



<?php 
}