<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Subscriptio PayPal payment gateway class
 *
 * @class Subscriptio_PayPal_Gateway
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_PayPal_Gateway') && class_exists('WC_Payment_Gateway')) {

class Subscriptio_PayPal_Gateway extends WC_Payment_Gateway
{

    /**
     * Constructor class
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function __construct($id = null)
    {
        global $woocommerce;

        // Gateway configuration
        $this->id                    = 'subscriptio_paypal';
        $this->has_fields            = true;

        $this->supports              = array('products', 'refunds', 'subscriptio');
        $this->method_title          = __('PayPal by Subscriptio', 'subscriptio-paypal');
        $this->method_description    = sprintf(wp_kses(__('PayPal by Subscriptio is a PayPal payment gateway extension that enables automatic recurring payments for subscription products. It uses <a href="%s">PayPal Adaptive Payments API</a> to handle payment preapprovals and recurring billing.', 'subscriptio-paypal'), array('a' => array('href' => array()))), 'https://developer.paypal.com/docs/classic/adaptive-payments/integration-guide/APIntro/');

        // Load settings fields
        $this->init_form_fields();
        $this->init_settings();

        // Define properties
        $this->enabled               = apply_filters('subscriptio_paypal_enabled', $this->get_option('enabled'));
        $this->sandbox               = apply_filters('subscriptio_paypal_sandbox', $this->get_option('sandbox'));
        $this->paypal_receiver_email = $this->get_option('paypal_receiver_email');
        $this->title                 = $this->get_option('title');
        $this->description           = $this->get_option('description');

        // API Credentials
        $this->api_username          = $this->sandbox == 'yes' ? $this->get_option('sandbox_api_username') : $this->get_option('api_username');
        $this->api_password          = $this->sandbox == 'yes' ? $this->get_option('sandbox_api_password') : $this->get_option('api_password');
        $this->api_signature         = $this->sandbox == 'yes' ? $this->get_option('sandbox_api_signature') : $this->get_option('api_signature');
        $this->app_id                = $this->sandbox == 'yes' ? $this->get_option('sandbox_app_id') : $this->get_option('app_id');
        $this->endpoint_url          = $this->sandbox == 'yes' ? 'https://svcs.sandbox.paypal.com/AdaptivePayments' : 'https://svcs.paypal.com/AdaptivePayments';

        // Preapproval Settings
        $this->force_preapproval      = $this->get_option('force_preapproval');
        $this->preapproval_cancel     = $this->get_option('preapproval_cancel_action');
        $this->max_preapproval_term   = $this->get_option('max_preapproval_term');
        $this->max_all_payments_total = $this->get_option('max_all_payments_total');
        $this->expiration_override    = $this->get_option('expiration_override');

        // Save gateway settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // Get admin notices and disable payment gateway if any
        $this->notices = $this->get_notices();

        // Disable payments and show admin notices if something is wrong with settings
        if ($this->enabled == 'yes' && !empty($this->notices)) {

            // Show admin notices
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                add_action('admin_notices', array($this, 'show_admin_notices'));
            }

            // Disable payments
            if (count($this->notices) > 1 || !isset($this->notices['ssl'])) {
                $this->enabled = 'no';
            }
        }

        // Disable payments if PayPal is unavailable
        if (!$this->is_paypal_available_for_use()) {
            $this->enabled = 'no';
        }

        // If still enabled, activate IPN listener
        if ($this->enabled == 'yes') {
            new Subscriptio_PayPal_IPN_Handler($this->sandbox, $this->paypal_receiver_email);
        }
    }

    /**
     * Get notices for admin
     *
     * @access public
     * @param bool $return_error
     * @return array|bool
     */
    public function get_notices()
    {
        $notices = array();

        // Check WooCommerce version
        if (!Subscriptio::wc_version_gte('2.1')) {
            $notices['version'] = __('Subscriptio PayPal payment gateway requires WooCommerce 2.1 or later.', 'subscriptio-paypal');
        }

        // Check secret keys
        if ($this->sandbox == 'no' && empty($this->api_username) || empty($this->api_password) || empty($this->api_signature) || empty($this->app_id)) {
            $notices['secret'] = __('Subscriptio PayPal payment gateway requires all API credentials to be set.', 'subscriptio-paypal');
        }

        return $notices;
    }

    /**
     * Show admin notices
     *
     * @access public
     * @return void
     */
    public function show_admin_notices()
    {
        foreach ($this->notices as $notice) {
            echo '<div class="error"><p>' . __($notice, 'subscriptio-paypal') . '</p></div>';
        }
    }

    /**
     * Check if this gateway is available for use
     *
     * @access public
     * @return bool
     */
    public function is_available()
    {
        return $this->enabled == 'yes' ? true : false;
    }

    /**
     * Check if PayPal is enabled and available in the user's country
     *
     * @access public
     * @return bool
     */
    public function is_paypal_available_for_use()
    {
        // Using the woocommerce filter
        $supported_currencies = apply_filters('woocommerce_paypal_supported_currencies', array('AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB'));

        return in_array(get_woocommerce_currency(), $supported_currencies);
    }

    /**
     * Get headers for request
     *
     * @access public
     * @return array $headers
     */
    public function get_http_headers()
    {
        $headers = array(
            'X-PAYPAL-SECURITY-USERID' => $this->api_username,
            'X-PAYPAL-SECURITY-PASSWORD' => $this->api_password,
            'X-PAYPAL-SECURITY-SIGNATURE' => $this->api_signature,
            'X-PAYPAL-APPLICATION-ID' => $this->app_id,
            'X-PAYPAL-REQUEST-DATA-FORMAT' => 'JSON',
            'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'JSON',
        );

        return $headers;
    }

    /**
     * Send request to PayPal
     *
     * @access public
     * @param string $action
     * @param array $params
     * @return object|string
     */
    public function send_request($action, $params)
    {
        // Different actions require different endpoint URL
        switch ($action) {

            case 'preapproval':
                $context = '/Preapproval';
                break;

            case 'preapproval_cancel':
                $context = '/CancelPreapproval';
                break;

            case 'preapproval_details':
                $context = '/PreapprovalDetails';
                break;

            case 'pay':
                $context = '/Pay';
                break;

            case 'execute_payment':
                $context = '/ExecutePayment';
                break;

            case 'payment_details':
                $context = '/PaymentDetails';
                break;

            case 'refund':
                $context = '/Refund';
                break;

            case 'currency':
                $context = '/ConvertCurrency';
                break;

            default:
                break;
        }

        $result = wp_remote_post(
            $this->endpoint_url . $context,
            array(
                'method'    => 'POST',
                'headers'   => $this->get_http_headers(),
                'body'      => json_encode($params),
                'sslverify' => false,
                'timeout'   => 100,
            )
        );

        return is_wp_error($result) ? $result->get_error_message() : empty($result['body']) ? __('Response body empty.', 'subscriptio-paypal') : json_decode($result['body']);
    }

    /**
     * Send the request for preapproval
     *
     * @access public
     * @param obj $order
     * @return void
     */
    public function send_preapproval_request($order)
    {
        // Create the preapproval request
        $preapproval_fields = array(
            'startingDate'                => date('c'),
            'currencyCode'                => strtoupper($order->get_order_currency()),
            'cancelUrl'                   => esc_url($order->get_cancel_order_url()),
            'returnUrl'                   => esc_url($this->get_return_url($order)),
            'memo'                        => sprintf(__('Recurring payment preapproval for subscription payments on %s.', 'subscriptio-paypal'), esc_html(get_bloginfo('name'))),
            'requestEnvelope'             => array('errorLanguage' => 'en_US'),
            'ipnNotificationUrl'          => WC()->api_request_url('Subscriptio_PayPal_Gateway')
        );

        // If expiration override is enabled
        if ($this->expiration_override == 'yes') {

            // Check how many subscriptions there are in order
            $subscriptions = Subscriptio_Order_Handler::get_subscriptions_from_order_id($order->id);

            // Override will work only for single subscription
            if (count($subscriptions) == 1) {

                foreach ($subscriptions as $subscription) {

                    // Check if subscription has expiration and calculate the limits
                    if (isset($subscription->max_length_time_unit) && isset($subscription->max_length_time_value)) {

                        $limits = $this->calculate_subscription_limits($subscription, $order);
                        $preapproval_fields['endingDate'] = $limits['ending_date'];
                        $preapproval_fields['maxTotalAmountOfAllPayments'] = $limits['total_payments_amount'];
                    }
                }
            }
        }

        // Check if fields wasn't set before: expiration override is disabled, more than 1 subscription in order, etc.
        if (!isset($preapproval_fields['endingDate']) || !isset($preapproval_fields['maxTotalAmountOfAllPayments'])) {

            // Check if there's configured custom values in settings
            if ($this->max_preapproval_term > 0) {
                $months = $this->max_preapproval_term;
                $preapproval_fields['endingDate'] = date('c', strtotime('+' . $months . 'months', time()));
            }

            if ($this->max_all_payments_total > 0) {
                $preapproval_fields['maxTotalAmountOfAllPayments'] = $this->max_all_payments_total;
            }

            // Use default values if settings are empty
            if ($this->max_preapproval_term == '') {
                $preapproval_fields['endingDate'] = date('c', strtotime('+12 months', time()));
            }

            if ($this->max_all_payments_total == '') {
                $preapproval_fields['maxTotalAmountOfAllPayments'] = $this->calculate_currency($order, 2000);
            }
        }

        // Send preapproval request and return the response
        return $this->send_request('preapproval', $preapproval_fields);
    }

    /**
     * Create the payment request
     *
     * @access public
     * @param obj $order
     * @return array
     */
    public function create_pay_request($order)
    {
        return array(
            'actionType'      => 'PAY',
            'cancelUrl'       => esc_url($order->get_cancel_order_url()),
            'returnUrl'       => esc_url($this->get_return_url($order)),
            'currencyCode'    => strtoupper($order->get_order_currency()),
            'receiverList'    => array(
                'receiver'        => array(
                    'email'           => $this->paypal_receiver_email,
                    'amount'          => $order->order_total)),
            'requestEnvelope' => array('errorLanguage' => 'en_US'),
            'ipnNotificationUrl' => WC()->api_request_url('Subscriptio_PayPal_Gateway')
        );
    }

    /**
     * Calculate currency with PayPal
     *
     * @access public
     * @param obj $order
     * @param int $amount
     * @return int
     */
    public function calculate_currency($order, $amount = null)
    {
        $currency_fields = array(
            'convertToCurrencyList' => array('currencyCode' => strtoupper($order->get_order_currency())),
            'baseAmountList'        => array(
                'currency'            => array(
                    'amount'            => $amount ? $amount : $order->order_total,
                    'code'              => 'USD')),
            'requestEnvelope'       => array('errorLanguage' => 'en_US'),
        );

        // Send request and get the response
        $currency_response = $this->send_request('currency', $currency_fields);

        // Get results
        $result_message = $currency_response->responseEnvelope->ack;

        // Request failed
        if ($result_message == 'Failure' || isset($currency_response->error)) {
            $currency_error = $currency_response->error[0]->message;
            Subscriptio::add_woocommerce_frontend_error(__('PayPal currency conversion request failed. ', 'subscriptio-paypal') . $currency_error);
            return;
        }

        // Request sucessful
        if ($result_message == 'Success' || $result_message == 'SuccessWithWarning') {
            $converted_amount = $currency_response->estimatedAmountTable->currencyConversionList[0]->currencyList->currency[0]->amount;
            return (int) floor($converted_amount);
        }
    }

    /**
     * Calculate subscription limits
     *
     * @access public
     * @param obj $subscription
     * @param obj $order
     * @return int
     */
    public function calculate_subscription_limits($subscription, $order)
    {
        $limits = array(
            'ending_date'           => '',
            'total_payments_amount' => '',
        );

        // Check free trial
        if (isset($subscription->free_trial_time_unit) && isset($subscription->free_trial_time_value)) {
            $trial_period_in_seconds = Subscriptio_Subscription::get_period_length_in('second', $subscription->free_trial_time_unit, $subscription->free_trial_time_value);
        }
        else {
            $trial_period_in_seconds = 0;
        }

        // Calculate expiration time
        $expiration_time = $subscription->calculate_expiration_time() + $trial_period_in_seconds;

        // Save the expiration date
        $limits['ending_date'] = date('c', $expiration_time);

        // Calculate total payments - start with current order total
        $total_payments = $order->order_total;

        // Get one renewal payment
        $one_renewal_payment = $subscription->renewal_order_total;

        // Get one cicle length in seconds
        $renewal_period_in_seconds = Subscriptio_Subscription::get_period_length_in('second', $subscription->price_time_unit, $subscription->price_time_value);

        // Calculate amount of payments until expiration
        $payments_count = floor(($expiration_time - time() - $trial_period_in_seconds) / $renewal_period_in_seconds);

        // Add all of the payments to the total
        $total_payments += $payments_count * $one_renewal_payment;

        // Save the total
        $limits['total_payments_amount'] = $total_payments;

        return $limits;
    }

    /**
     * Process payment
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function process_payment($order_id)
    {
        global $woocommerce;

        // Get order object
        $order = new WC_Order($order_id);

        if (!$order) {
            Subscriptio::add_woocommerce_frontend_error(__('Order not found.', 'subscriptio-paypal') . ' ' . __('We have not charged you for this order. Please try again.', 'subscriptio-paypal'));
            return;
        }

        // Check if the order is renewal and try to process it with preapproval key
        if (Subscriptio_Order_Handler::order_is_renewal($order_id)) {

            // Complete the payment if order was processed successfully
            if ($this->process_renewal_order_payment($order) === true) {
                return array(
                    'result'    => 'success',
                    'redirect'  => esc_url($this->get_return_url($order)),
                );
            }
        }

        // Create the payment request
        $payment_fields = $this->create_pay_request($order);

        // Send user to preapproval page if there's subscription and either if it's forced or the checkbox is checked
        if (Subscriptio_Order_Handler::contains_subscription($order->id) && ($this->force_preapproval == 'yes' || (isset($_POST['subscriptio_paypal_preapproval']) && $_POST['subscriptio_paypal_preapproval'] == 'on'))) {

            // Get the response
            $preapproval_response = $this->send_preapproval_request($order);

            // Check the key
            if (!empty($preapproval_response->preapprovalKey)) {

                // Get the key and save in temporal field
                $preapproval_key = $preapproval_response->preapprovalKey;

                update_post_meta($order->id, '_subscriptio_paypal_temp_preapproval_key', $preapproval_key);

                // Set args for PayPal authorization request
                $preapproval_paypal_args = array(
                    'cmd' => '_ap-preapproval',
                    'preapprovalkey' => $preapproval_key,
                );

                // Get the url
                $redirect_url = $this->get_paypal_request_url($preapproval_paypal_args);

                // And also set up the payment for order
                $payment_fields['preapprovalKey'] = $preapproval_key;

                // And store it to pay later - after approval
                update_post_meta($order->id, '_subscriptio_paypal_temp_payfields', $payment_fields);
            }

            // Add error if there was no key
            else {
                $error_message = isset($preapproval_response->error[0]->message) ? $preapproval_response->error[0]->message : __('Unknown error.', 'subscriptio-paypal');
                Subscriptio::add_woocommerce_frontend_error(__('PayPal preapproval request failed. ', 'subscriptio-paypal') . $error_message);
                return;
            }
        }

        // Otherwise send user to regular payment page
        else {

            // Send payment request
            $payment_response = $this->send_request('pay', $payment_fields);

            // Check the key
            if (!empty($payment_response->payKey)) {

                // Get the key and save in temporal field
                $pay_key = $payment_response->payKey;

                update_post_meta($order->id, '_subscriptio_paypal_temp_paykey', $pay_key);

                // Set args for PayPal authorization request
                $payment_paypal_args = array(
                    'cmd' => '_ap-payment',
                    'paykey' => $pay_key,
                );

                // Get the url
                $redirect_url = $this->get_paypal_request_url($payment_paypal_args);
            }

            // Add error if there was no key
            else {
                $error_message = isset($payment_response->error[0]->message) ? $payment_response->error[0]->message : __('Unknown error.', 'subscriptio-paypal');
                Subscriptio::add_woocommerce_frontend_error(__('PayPal payment request failed. ', 'subscriptio-paypal') . $error_message);
                return;
            }
        }

        // Empty cart
        $woocommerce->cart->empty_cart();

        // Redirect user
        return array(
            'result'    => 'success',
            'redirect'  => $redirect_url,
        );
    }

    /**
     * Process renewal order payment
     *
     * @access public
     * @params obj $order
     * @return bool
     */
    public function process_renewal_order_payment($order)
    {
        // Get user id
        $user_id = $order->get_user_id();

        // Get current preapproval keys
        $current_keys = get_user_meta($user_id, '_subscriptio_paypal_preapproval_keys', true);

        // Get subscription from order
        $subscriptions = Subscriptio_Order_Handler::get_subscriptions_from_order_id($order->id);

        foreach ($subscriptions as $id => $subscription) {

            // If user has preapproval for such subscription
            if (!empty($current_keys[$id])) {

                // Create basic request
                $payment_fields = $this->create_pay_request($order);

                // Add key to request
                $payment_fields['preapprovalKey'] = $current_keys[$id];

                // Change cancel url to prevent cancelling order
                $payment_fields['cancelUrl'] = esc_url(get_bloginfo('url'));

                // Remove IPN notification
                unset($payment_fields['ipnNotificationUrl']);

                // Send payment request
                $payment_response = $this->send_request('pay', $payment_fields);

                // Get paykey
                $pay_key = isset($payment_response->payKey) ? $payment_response->payKey : '';

                // Get results
                $result_message = $payment_response->responseEnvelope->ack;
                $payment_status = $payment_response->paymentExecStatus;

                // Request failed
                if ($payment_status == 'ERROR' || $result_message == 'Failure' || $result_message == 'FailureWithWarning') {
                    $payment_error = $payment_response->error[0]->message;
                    $order->add_order_note(__('Payment failed (PayPal). Error message: ', 'subscriptio-paypal') . $payment_error);

                    return false;
                }

                // Request was successful
                if ($payment_status == 'COMPLETED' && ($result_message == 'Success' || $result_message == 'SuccessWithWarning')) {

                    // Add payment method
                    update_post_meta($order->id, '_payment_method', 'subscriptio_paypal');

                    // Save paykey
                    update_post_meta($order->id, '_subscriptio_paypal_paykey', $pay_key);

                    // Complete the order
                    $order->add_order_note(sprintf(__('PayPal payment %s completed.', 'subscriptio-paypal'), $pay_key));
                    $order->payment_complete();

                    return true;
                }
            }
        }
    }

    /**
     * Get the url for PayPal request
     *
     * @access public
     * @params array $args
     * @return string
     */
    public function get_paypal_request_url($args)
    {
        // Check the args
        if (!$args) {
            return false;
        }

        // Set the base url
        if ($this->sandbox == 'yes') {
            $baseurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
        }
        else {
            $baseurl = 'https://www.paypal.com/cgi-bin/webscr?';
        }

        // Encode and return the full url
        return $baseurl . http_build_query($args, '', '&');
    }

    /**
     * Initialize form fields
     *
     * @access public
     * @return void
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __('Enable/Disable', 'subscriptio-paypal'),
                'type'    => 'checkbox',
                'label'   => __('Enable PayPal Adaptive Payments', 'subscriptio-paypal'),
                'default' => 'no',
            ),
            'sandbox' => array(
                'title'   => __('Sandbox Mode', 'subscriptio-paypal'),
                'type'    => 'checkbox',
                'label'   => __('Enable PayPal Sandbox mode', 'subscriptio-paypal'),
                'default' => 'no',
            ),
            'paypal_receiver_email' => array(
                'title'       => __('PayPal Email', 'subscriptio-paypal'),
                'type'        => 'email',
                'description' => __('PayPal Email of receiver.', 'subscriptio-paypal'),
                'default'     => '',
                'placeholder' => 'you@youremail.com',
            ),
            'title' => array(
                'title'       => __('Title', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('The title which the user sees during checkout.', 'subscriptio-paypal'),
                'default'     => __('PayPal', 'subscriptio-paypal'),
            ),
            'description' => array(
                'title'       => __('Description', 'subscriptio-paypal'),
                'type'        => 'textarea',
                'description' => __('The description which the user sees during checkout.', 'subscriptio-paypal'),
                'default'     => __('Pay Securely via PayPal', 'subscriptio-paypal'),
            ),
            'api_credentials' => array(
                    'title'       => __( 'API Credentials', 'subscriptio-paypal' ),
                    'type'        => 'title',
                    'description' => sprintf(wp_kses(__('Refer to <a href="%s">this knowledge base article</a> for some guidance on how to acquire your API credentials.', 'subscriptio-paypal'), array('a' => array('href' => array()))), 'http://support.rightpress.net/hc/en-us/articles/204501849-PayPal-Integration'),
            ),
            'sandbox_api_username' => array(
                'title'       => __('Sandbox API Username', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('Sandbox API Username from your PayPal Account.', 'subscriptio-paypal'),
                'default'     => '',
            ),
            'sandbox_api_password' => array(
                'title'       => __('Sandbox API Password', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('Sandbox API Password from your PayPal Account.', 'subscriptio-paypal'),
                'default'     => '',
            ),
            'sandbox_api_signature' => array(
                'title'       => __('Sandbox API Signature', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('Sandbox API Signature from your PayPal Account.', 'subscriptio-paypal'),
                'default'     => '',
            ),
            'sandbox_app_id' => array(
                'title'       => __('Sandbox App ID', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('Sandbox App ID.', 'subscriptio-paypal'),
                'default'     => 'APP-80W284485P519543T',
            ),
            'api_username' => array(
                'title'       => __('API Username', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('API Username from your PayPal Account.', 'subscriptio-paypal'),
                'default'     => '',
            ),
            'api_password' => array(
                'title'       => __('API Password', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('API Password from your PayPal Account.', 'subscriptio-paypal'),
                'default'     => '',
            ),
            'api_signature' => array(
                'title'       => __('API Signature', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('API Signature from your PayPal Account.', 'subscriptio-paypal'),
                'default'     => '',
            ),
            'app_id' => array(
                'title'       => __('App ID', 'subscriptio-paypal'),
                'type'        => 'text',
                'description' => __('App ID.', 'subscriptio-paypal'),
                'default'     => '',
            ),
            'preapproval_settings' => array(
                    'title'       => __( 'Preapproval Settings', 'subscriptio-paypal' ),
                    'type'        => 'title',
                    'description' => __( 'The following settings control how recurring payments will work on your online store.', 'subscriptio-paypal' ),
            ),
            'force_preapproval' => array(
                'title'       => __('Force Automatic Payments', 'subscriptio-paypal'),
                'type'        => 'checkbox',
                'label'       => __('Enable Forcing of Automatic Payments', 'subscriptio-paypal'),
                'description' => __('Force automatic subscription payments (hides the optional preapproval checkbox)', 'subscriptio-paypal'),
                'default'     => 'no',
            ),
            'preapproval_cancel_action' => array(
                'title'       => __('When Payment Agreement is Cancelled', 'subscriptio-paypal'),
                'type'        => 'select',
                'description' => __('Choose what action you want to perform when user cancels the preapproval from PayPal account settings.', 'subscriptio-paypal'),
                'default'     => 'manual',
                'options'     => array(
                    'manual'    => __('Revert to manual subscription payments', 'subscriptio-paypal'),
                    'cancel'    => __('Cancel corresponding subscription immediately', 'subscriptio-paypal')
		)
            ),
            'max_preapproval_term' => array(
                'title'       => __('Max Preapproval Term', 'subscriptio-paypal'),
                'type'        => 'number',
                'description' => __('This controls API property endingDate. Enter custom number of months to change default of 12 months. Enter zero to not send this property to PayPal at all. Contact PayPal in advance to get this limitation lifted for your account.', 'subscriptio-paypal'),
                'default'     => '',
                'placeholder' => '12 months',
            ),
            'max_all_payments_total' => array(
                'title'       => __('Max All Payments Total', 'subscriptio-paypal'),
                'type'        => 'number',
                'description' => __('This controls API property maxTotalAmountOfAllPayments. Enter custom decimal number to change default of $2000 USD (or equivalent in other currency). Enter zero to not send this property to PayPal at all. Contact PayPal in advance to get this limitation lifted for your account.', 'subscriptio-paypal'),
                'default'     => '',
                'placeholder' => '$2000 USD or equivalent',
            ),
            'expiration_override' => array(
                'title'   => __('Expiration Override', 'subscriptio-paypal'),
                'type'    => 'checkbox',
                'label'   => __('Enable Expiration Override', 'subscriptio-paypal'),
                'description'   => __('For example, if a subscription product with monthly payments of $10 is set to expire after 24 months, this extension would send 24 months as max term and $240 as max total.', 'subscriptio-paypal'),
                'default' => 'no',
            ),
        );
    }

    /**
     * Checkbox field on Checkout page
     *
     * @access public
     * @return void
     */
    public function payment_fields()
    {
        echo $this->description;

        // Display this field only if user is not a guest, cart contains at least one subscription product and preapproval is not forced
        if (is_user_logged_in() && Subscriptio::cart_contains_subscription() && $this->force_preapproval == 'no') {
            echo '<br><input type="checkbox" name="subscriptio_paypal_preapproval">' . __('Preapprove all future payments for subscriptions in this order.', 'subscriptio-paypal');
        }
    }

    /**
     * Process refund manually issued from order page
     *
     * @access public
     * @param int $order_id
     * @param float $amount
     * @param string $reason
     * @return bool
     */
    public function process_refund($order_id, $amount = null, $reason = '')
    {
        // Load order
        $order = new WC_Order($order_id);

        if (!$order) {
            return;
        }

        // Get pay key
        $paykey = get_post_meta($order_id, '_subscriptio_paypal_paykey', true);

        if (empty($paykey)) {
            return;
        }

        // Create the refund request
        $refund_fields = array(
            'currencyCode'    => strtoupper($order->get_order_currency()),
            'payKey'          => $paykey,
            'receiverList'    => array(
                'receiver'        => array(
                    'email'           => $this->paypal_receiver_email,
                    'amount'          => $amount)),
            'requestEnvelope' => array('errorLanguage' => 'en_US')
        );

        // Send request to refund payment
        $refund_response = $this->send_request('refund', $refund_fields);
        $result_message = $refund_response->responseEnvelope->ack;

        // Request failed
        if ($result_message == 'Failure' || $result_message == 'FailureWithWarning') {
            $order->add_order_note(__('PayPal refund failed.', 'subscriptio-paypal'));
            return false;
        }

        // Request was successful
        if ($result_message == 'Success' || $result_message == 'SuccessWithWarning') {
            $order->add_order_note(sprintf(__('%s of PayPal charge %s refunded.', 'subscriptio-paypal'), Subscriptio::get_formatted_price($amount), $paykey));
            return true;
        }
    }

}
}
