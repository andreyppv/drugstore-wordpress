<?php

/**
  * Gateway class
  */
class WC_Smash_Limelight_Gateway extends WC_Payment_Gateway {    
    public function __construct() {
        $this->id                   = 'limelight';
        //$this->icon                 = apply_filters('woocommerce_limelight_icon', plugins_url( 'assets/limelight.png' , __FILE__ ) );
        $this->method_title         = 'Limelight Gateway';
        $this->method_description   = 'LimelightCRM Gateway';

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();
        
        // Get setting values
        foreach ( $this->settings as $key => $val ) $this->$key = $val;
        
        // Load plugin checkout icon
        $this->icon = PLUGIN_DIR . 'images/cards.png';
         
        //Actions
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

        // Payment listener/API hook
        add_action( 'woocommerce_api_wc_smash_limelight_gateway', array( $this, 'check_limelight_response' ) );
        
        add_action( 'wp_enqueue_scripts', array( $this, 'add_smash_limelight_scripts' ) );
        
        /*if($this->enabled == 'yes') {
            add_action( 'admin_notices', 'smash_admin_notice' );
        }*/
    } 
    
    /**
     * Admin Panel Options
     **/
    public function admin_options(){
        include_once(HTML_PATH . 'admin/plugin_options.php');
    }   
    
    /**
     * Initialise Gateway Settings Form Fields
    **/
    public function init_form_fields(){
        $this->form_fields = array(
            'enabled' => array(
                'title'         => 'Enable/Disable',
                'type'          => 'checkbox',
                'label'         => 'Enable Limelight Gateway',
                'description'   => 'Enable or disable the gateway.',
                'desc_tip'      => true,
                'default'       => 'yes'
            ),
            'title' =>      array(
                'title'         => 'Title',
                'type'          => 'text',
                'description'   => 'This controls the title which the user sees during checkout.',
                'desc_tip'      => false,
                'default'       => 'Limelight Gateway'
            ),
            'description'   => array(
                'title'         => 'Description',
                'type'          => 'textarea',
                'description'   => 'This controls the description which the user sees during checkout.',
                'default'       => 'If this is selected, Only Limelight shipping option is only available, others will be ignored automatically.'
            ),
            'url'           => array(
                'title'         => 'Url',
                'type'          => 'text',
                'description'   => 'Your LimeLight Site URL</br> Ex: https://www.domain.com/admin/' ,
                'default'       => '',
                'desc_tip'      => false
            ),
            'username'      => array(
                'title'         => 'Username',
                'type'          => 'text',
                'description'   => 'Your LimeLight API Username' ,
                'default'       => '',
                'desc_tip'      => false
            ),
            'password'      => array(
                'title'         => 'Password',
                'type'          => 'password',
                'description'   => 'Your LimeLight API Password' ,
                'default'       => '',
                'desc_tip'      => false
            ),
            
            'gateway'       => array(
                'title'         => 'Default Gateway ID',
                'type'          => 'text',
                'description'   => 'Your LimeLight Default Gateway ID<br/>If this value is set, the product\'s gateway id will be ignored.' ,
                'default'       => '',
                'desc_tip'      => false
            ),
            
            /*'cardtypes'   => array(
                'title'       => __( 'Accepted Cards', 'woothemes' ),
                'type'        => 'multiselect',
                'description' => __( 'Select which card types to accept.', 'woothemes' ),
                'default'     => '',
                'options'     => array(
                    'MasterCard'        => 'MasterCard',
                    'Visa'                => 'Visa',
                    'Discover'            => 'Discover',
                    'American Express'  => 'American Express'
                ),
            ),
            'cvv'         => array(
                'title'       => __( 'CVV', 'woothemes' ),
                'type'        => 'checkbox',
                'label'       => __( 'Require customer to enter credit card CVV code', 'woothemes' ),
                'description' => __( '', 'woothemes' ),
                'default'     => 'yes'
            ), */
            /*'saveinfo'    => array(
                'title'       => __( 'Billing Information Storage', 'woothemes' ),
                'type'        => 'checkbox',
                'label'       => __( 'Allow customers to save billing information for future use (requires Inspire Commerce Customer Vault)', 'woothemes' ),
                'description' => __( '', 'woothemes' ),
                'default'     => 'no'
            ),  */
            
            'show_terms_checkbox' => array(
                'title'         => 'Terms Checkbox',
                'type'          => 'checkbox',
                'label'         => 'Show Term Checkbox',
                'description'   => 'Show terms checkbox on product page',
                'desc_tip'      => true,
                'default'       => 'yes'
            ),
            'terms' => array(
                'title'         => 'Terms and Conditions',
                'type'          => 'textarea',
                'description'   => 'Available shortcodes: </br>
                                    Product Name        -> [limelight_product_name], </br>
                                    Product Price       -> [limelight_product_price], </br>
                                    Product Rebill Price-> [limelight_rebill_price], </br>
                                    Shipping Name       -> [limelight_shipping_name], </br>
                                    Shipping Price      -> [limelight_shipping_price], </br>
                                    Support Phone       -> [xyz-ihs snippet="phone"], </br>
                                    Support Email       -> [xyz-ihs snippet="support-email"]' ,
                'default'       => '',
                'desc_tip'      => false
            ),
            
            /*
            'support_phone'      => array(
                'title'         => 'Support Phone',
                'type'          => 'text',
                'description'   => 'Your LimeLight Support Phone' ,
                'default'       => '1-866-220-9841',
                'desc_tip'      => false
            ),
            'support_email'      => array(
                'title'         => 'Support Email',
                'type'          => 'text',
                'description'   => 'Your LimeLight Support Email' ,
                'default'       => 'support@pnahealth.com',
                'desc_tip'      => false
            ),
            */
        );
    }
    
    function payment_fields() { 
        include_once(HTML_PATH . 'front/payment_form.php');   
    }
    
    function process_payment($order_id) {
        include("limelight/Transaction.php");
        
        global $woocommerce;
        $order = new WC_Order( $order_id );
        
        $plugin = new WC_Smash_Limelight_Gateway;
        $gateway_id = $plugin->gateway;
        
        // Convert CC expiration date from (M)M-YYYY to MMYY
        $expmonth = $this->get_post( 'expmonth' );
        if ( $expmonth < 10 ) $expmonth = '0' . $expmonth;
        if ( $this->get_post( 'expyear' ) != null ) $expyear = substr( $this->get_post( 'expyear' ), -2 );
        
        //transaction
        $options = get_option('woocommerce_limelight_settings');
        $instance = new Transaction($options['username'], $options['password'], $options['url']);
        
        $success = true;
        foreach($order->get_items() as $item) {
            
            if($item['shipping_info'] == null) continue;
            if(limelight_connection_disabled()) continue;
            
            $ll_product_id  = $item['item_meta']['_limelight_product_id'][0];
            $ll_campaign_id = $item['item_meta']['_limelight_campaign_id'][0];
            $ll_shipping_id = $item['item_meta']['_limelight_shipping_id'][0];
            
            if($gateway_id == 0 || $gateway_id == '') {
                $gateway_id  = $item['item_meta']['_limelight_gateway_id'][0];            
            }
            
            $params = array(
                'first_name'        => $order->shipping_first_name,
                'last_name'         => $order->shipping_last_name,
                'shipping_address1' => $order->shipping_address_1,
                'shipping_address2' => $order->shipping_address_2,
                'shipping_city'     => $order->shipping_city,
                'shipping_state'    => $order->shipping_state,
                'shipping_zip'      => $order->shipping_postcode,
                'shipping_country'  => $order->shipping_country,
                'phone'             => $order->billing_phone,
                'email'             => $order->billing_email,
                'card_type'         => $this->get_post( 'cardtype' ),
                'card_number'       => $this->get_post( 'ccnum' ),
                'card_expire'       => $expmonth.$expyear,
                'card_cvv'          => $this->get_post( 'cvv' ),
                'ip_address'        => $_SERVER['REMOTE_ADDR'],
                'product_id'        => $ll_product_id,
                'campaign_id'       => $ll_campaign_id,
                'shipping_id'       => $ll_shipping_id,
                'paypal_token'      => '',
                'paypal_payer_id'   => '',
                'check_account'     => '',
                'check_routing'     => '',
                'billing_first_name'=> $order->billing_first_name,
                'billing_last_name' => $order->billing_last_name,
                'billing_address1'  => $order->billing_address_1,
                'billing_address2'  => $order->billing_address_2,
                'billing_city'      => $order->billing_city,
                'billing_state'     => $order->billing_state,
                'billing_zip'       => $order->billing_postcode,
                'billing_country'   => $order->billing_country,
                'upsell_count'      => '',
                'upsell_product_ids'=> '',
                'dynamic_product_price_array' => '',
                'notes'             => '',
                'product_qty_array' => '',
                'force_gateway_id'  => $gateway_id == 0 ? '' : $gateway_id,
                'thm_session_id'    => '',
                'total_installments'=> '',
                'afid'  => '',
                'sid'   => '',
                'affid' => '',
                'c1'    => '',
                'c2'    => '',
                'c3'    => '',
                'aid'   => '',
                'opt'   => '',
                'click_id'      => '',
                'created_by'    => ''
            );    
            
            $response = $instance->GetResponse('NewOrder', $params);
            
            // Exceptions
            if($response['responseCode'] != 100) {
                if($response['responseCode'] != 800) {
                    $order->add_order_note( __( 'LimeLight payment failed. Payment declined.', 'limelight' ) );
                    wc_add_notice( __( $response['declineReason'], 'limelight' ), $notice_type = 'error' );
                    
                    $success = false;
                    break;
                } else if($response['responseCode'] != 200) {
                    $order->add_order_note( __( 'Invalid LimeLight login credentials.', 'limelight' ) );
                    wc_add_notice( __( 'LimeLight payment failed. Invalid LimeLight login credentials.', 'limelight' ), $notice_type = 'error' );
                    
                    $success = false;
                    break;
                } else if($response['responseCode'] != 342) {
                    $order->add_order_note( __( 'The credit card has expired.', 'limelight' ) );
                    wc_add_notice( __( 'LimeLight payment failed. The credit card has expired.', 'limelight' ), $notice_type = 'error' );
                    
                    $success = false;
                    break;
                } 
            }  
        }
        
        if($success === true) {
            // Return thank you redirect
            return array (
                'result'   => 'success',
                'redirect' => $this->get_return_url( $order ),
            );                  
        }
    }
    
    /**
     * Check payment details for valid format
     */
    function validate_fields() {

        //if ( $this->get_post( 'inspire-use-stored-payment-info' ) == 'yes' ) return true;

        global $woocommerce;

        // Check for saving payment info without having or creating an account
        /*if ( $this->get_post( 'saveinfo' )  && ! is_user_logged_in() && ! $this->get_post( 'createaccount' ) ) {
            wc_add_notice( __( 'Sorry, you need to create an account in order for us to save your payment information.', 'woocommerce'), $notice_type = 'error' );
            return false;
        }*/

        $cardType            = $this->get_post( 'cardtype' );
        $cardNumber          = $this->get_post( 'ccnum' );
        $cardCSC             = $this->get_post( 'cvv' );
        $cardExpirationMonth = $this->get_post( 'expmonth' );
        $cardExpirationYear  = $this->get_post( 'expyear' );

        // Check card number
        if ( empty( $cardNumber ) || ! ctype_digit( $cardNumber ) ) {
            wc_add_notice( __( 'Card number is invalid.', 'woocommerce' ), $notice_type = 'error' );
            return false;
        }

        if ( $this->cvv == 'yes' ){
            // Check security code
            if ( ! ctype_digit( $cardCSC ) ) {
                wc_add_notice( __( 'Card security code is invalid (only digits are allowed).', 'woocommerce' ), $notice_type = 'error' );
                return false;
            }
            if ( ( strlen( $cardCSC ) != 3 && in_array( $cardType, array( 'Visa', 'MasterCard', 'Discover' ) ) ) || ( strlen( $cardCSC ) != 4 && $cardType == 'American Express' ) ) {
                wc_add_notice( __( 'Card security code is invalid (wrong length).', 'woocommerce' ), $notice_type = 'error' );
                return false;
            }
        }

        // Check expiration data
        $currentYear = date( 'Y' );

        if ( ! ctype_digit( $cardExpirationMonth ) || ! ctype_digit( $cardExpirationYear ) ||
             $cardExpirationMonth > 12 ||
             $cardExpirationMonth < 1 ||
             $cardExpirationYear < $currentYear ||
             $cardExpirationYear > $currentYear + 20
        ) {
            wc_add_notice( __( 'Card expiration date is invalid', 'woocommerce' ), $notice_type = 'error' );
            return false;
        }

        // Strip spaces and dashes
        $cardNumber = str_replace( array( ' ', '-' ), '', $cardNumber );

        return true;

    }
    
    /**
     * Include jQuery and our scripts
     */
    function add_smash_limelight_scripts() {

        //if ( ! $this->user_has_stored_data( wp_get_current_user()->ID ) ) return;

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'edit_billing_details', PLUGIN_DIR . 'js/edit_billing_details.js', array( 'jquery' ), 1.0, true );

        //if ( $this->cvv == 'yes' ) wp_enqueue_script( 'check_cvv', PLUGIN_DIR . 'js/check_cvv.js', array( 'jquery' ), 1.0 );
        wp_enqueue_script( 'check_cvv', PLUGIN_DIR . 'js/check_cvv.js', array( 'jquery' ), 1.0, true );

    }
    
    /**
     * Get post data if set
     */
    private function get_post( $name ) {
        if ( isset( $_POST[ $name ] ) ) {
            return $_POST[ $name ];
        } 
        return null;
    }
    
    public function is_available() {
        if($this->enabled != 'yes') return false;
        
        if($this->username == '' ||
            $this->password == '' ||
            $this->url == '') return false;
        
        //check url is correct
        $pattern = '^(https:\/\/)+[a-zA-Z0-9\.\-]+(/admin/)$^';
        if(!preg_match($pattern, $this->url)) return false;
        
        return true;
    }
    
    public function install() {
    }
}