<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to handling IPN responses
 *
 * @class Subscriptio_PayPal_IPN_Handler
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_PayPal_IPN_Handler')) {

class Subscriptio_PayPal_IPN_Handler
{

    /**
     * Constructor class
     *
     * @access public
     * @param bool $sandbox
     * @return void
     */
    public function __construct($sandbox = false)
    {
        add_action('woocommerce_api_subscriptio_paypal_gateway', array($this, 'check_and_process_response'));
        $this->sandbox = $sandbox;
    }

    /**
     * Check and process IPN response from PayPal
     *
     * @access public
     * @params array $args
     * @return string
     */
    public function check_and_process_response()
    {
        if (!empty($_POST)) {

            // Validate the request
            if ($this->validate_ipn() !== true) {
                wp_die('PayPal IPN Request Failure', 'PayPal IPN', array('response' => 200));
            }

            $posted = wp_unslash($_POST);

            // Check if the request is about PAY operation
            if (!empty($posted['transaction_type']) && strtolower($posted['transaction_type']) == 'adaptive payment pay') {

                // Get the pay key and find the order with such key as temporal
                if (!empty($posted['pay_key'])) {

                    $pay_key = $posted['pay_key'];

                    // Get the order
                    if ($order = $this->get_order_for_ipn('temp_pay', $pay_key)) {

                        $posted['status'] = strtolower($posted['status']);

                        // Change the status for sandbox operation
                        if (isset($posted['test_ipn']) && $posted['test_ipn'] == 1 && $posted['status'] == 'pending') {
                            $posted['status'] = 'completed';
                        }

                        // Actions for different payment statuses
                        if ($posted['status'] == 'completed') {

                            // Check if order was already completed
                            if ($order->has_status('completed')) {
                                exit;
                            }

                            // Save user meta data
                            $this->save_paypal_user_meta($order, $posted);

                            // Delete temporal pay key field, save normal
                            delete_post_meta($order->id, '_subscriptio_paypal_temp_paykey');
                            update_post_meta($order->id, '_subscriptio_paypal_paykey', $pay_key);

                            // Now make changes to the order
                            $order->add_order_note(__('PayPal IPN payment completed.', 'subscriptio-paypal'));
                            $order->payment_complete($pay_key);
                        }

                        else if ($posted['status'] == 'pending') {
                            $order->update_status('on-hold', sprintf(__( 'Payment pending: %s', 'subscriptio-paypal'), $posted['pending_reason']));
                        }

                        else if ($posted['status'] == 'refunded') {
                            $order->update_status('refunded', sprintf(__('Payment %s via IPN.', 'subscriptio-paypal'), wc_clean($posted['status'])));
                        }

                        else if ($posted['status'] == 'reversed') {
                            $order->update_status('on-hold', sprintf(__('Payment %s via IPN.', 'subscriptio-paypal'), wc_clean($posted['status'])));
                        }

                        else if (in_array($posted['status'], array('failed', 'denied', 'expired', 'voided'))) {
                            $order->update_status('failed', sprintf(__('Payment %s via IPN.', 'subscriptio-paypal'), wc_clean($posted['status'])));
                        }
                    }
                }
            }

            // And/or if the request is about PREAPPROVAL operation
            if (!empty($posted['transaction_type']) && strtolower($posted['transaction_type']) == 'adaptive payment preapproval') {

                // Get the pay key and find the order with such key as temporal
                if (!empty($posted['preapproval_key'])) {
                    $preapproval_key = $posted['preapproval_key'];

                    // Get the order
                    if ($order = $this->get_order_for_ipn('temp_preapproval', $preapproval_key)) {

                        // Check if it was approved
                        if (!empty($posted['approved']) && $posted['approved'] == 'true') {

                            // Save user meta data
                            $this->save_paypal_user_meta($order, $posted);

                            // Get temp payment fields
                            $payment_fields = maybe_unserialize(get_post_meta($order->id, '_subscriptio_paypal_temp_payfields', true));

                            // Execute the first payment from fields saved on checkout
                            $this->execute_first_subscription_payment($order, $payment_fields);
                        }
                        else {
                            $order->update_status('failed', __('Preapproval request and payment failed (IPN).', 'subscriptio-paypal'));
                        }
                    }

                    // Check if it got cancelled and remove preapproval key (no typo here - PayPal returns 'CANCELED')
                    if (!empty($posted['status']) && strtolower($posted['status']) == 'canceled') {
                        $this->remove_preapproval_key($preapproval_key);
                    }

                }
            }

            exit;
        }
    }

    /**
     * Validate IPN response with PayPal
     *
     * @access private
     * @return bool
     */
    private function validate_ipn()
    {
        // Combine received input values with validate command
        $post_input_validate = 'cmd=_notify-validate&' . file_get_contents("php://input");

        // Set url
        $post_url = $this->sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

        // Send back POST to PayPal
        $response = wp_remote_post(
            $post_url,
            array(
                'method'    => 'POST',
                'body'      => $post_input_validate,
                'sslverify' => false,
                'timeout'   => 100,
            )
        );

        // Check if we received the VERIFIED
        if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr($response['body'], 'VERIFIED')) {
            return true;
        }

        // If received an error
        if (is_wp_error($response) || strstr($response['body'], 'INVALID')) {
            return false;
        }

        return null;
    }

    /**
     * Get order with specific temporal key to use with IPN response
     *
     * @access private
     * @param string $key
     * @param string $value
     * @return object|array
     */
    private function get_order_for_ipn($key, $value)
    {
        // Set map for easier use of keys
        $meta_keys_map = array(
            'temp_pay'          => '_subscriptio_paypal_temp_paykey',
            'temp_preapproval'  => '_subscriptio_paypal_temp_preapproval_key',
        );

        // Search for related subscription post ids
        $order_post_ids = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'shop_order',
            'post_status'       => 'any',
            'meta_query'        => array(
                array(
                    'key'       => $meta_keys_map[$key],
                    'value'     => $value,
                    'compare'   => '=',
                ),
            ),
            'fields'            => 'ids',
        ));

        // Check how many found - there should be only one
        if (count($order_post_ids) == 1) {
            $order = new WC_Order($order_post_ids[0]);
            return $order;
        }

        // If there are more - gather them all, but return only first one
        else if (count($order_post_ids) > 1) {

            $orders = array();
            foreach ($order_post_ids as $order_id) {

                $order = new WC_Order($order_id);

                if ($order) {
                    $orders[] = $order;
                }
            }

            return $orders[0];
        }

        // Return false if nothing found
        return false;
    }

    /**
     * Save data from response to user meta
     *
     * @access private
     * @param array $order
     * @param array $posted
     * @return void
     */
    private function save_paypal_user_meta($order, $posted)
    {
        // Get user id
        $user_id = $order->get_user_id();

        // Save the preapproval key
        if (!empty($posted['preapproval_key'])) {

            // Get current preapproval keys
            $current_keys = get_user_meta($user_id, '_subscriptio_paypal_preapproval_keys', true);

            // Get subscriptions from order
            $subscriptions = Subscriptio_Order_Handler::get_subscriptions_from_order_id($order->id);

            // And set preapproval keys for those subscriptions
            foreach ($subscriptions as $id => $subscription) {

                if (empty($current_keys[$id])) {
                    $current_keys[$id] = $posted['preapproval_key'];
                }
            }

            update_user_meta($user_id, '_subscriptio_paypal_preapproval_keys', $current_keys);
        }

        // Save customer email
        if (!empty($posted['sender_email'])) {
            update_user_meta($user_id, '_subscriptio_paypal_customer_email', wc_clean($posted['sender_email']));
        }
    }

    /**
     * Remove preapproval key if preapproval was canceled
     *
     * @access private
     * @param string $preapproval_key
     * @return void
     */
    private function remove_preapproval_key($preapproval_key)
    {
        // Search for related subscriptions
        $subscription_post_ids = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'subscription',
            'post_status'       => 'any',
            'meta_query'        => array(
                array(
                    'key'       => '_subscriptio_preapproval',
                    'value'     => $preapproval_key,
                    'compare'   => '=',
                ),
            ),
            'fields'            => 'ids',
        ));

        // Iterate over found ids
        foreach ($subscription_post_ids as $id) {

            if ($subscription = Subscriptio_Subscription::get_by_id($id)) {

                // Get all keys of this user and iterate through them
                $current_keys = get_user_meta($subscription->user_id, '_subscriptio_paypal_preapproval_keys', true);

                foreach ($current_keys as $subscription_id => $key) {

                    // If the keys match, delete them
                    if ($preapproval_key == $key) {
                        unset($current_keys[$subscription_id]);
                        delete_post_meta($subscription_id, '_subscriptio_preapproval');

                        // Also cancel the latest order
                        if (isset($subscription->last_order_id)) {
                            $order = new WC_Order($subscription->last_order_id);
                            $order->add_order_note(__('Preapproval cancelled by user (IPN).', 'subscriptio-paypal'));
                        }

                        // Maybe cancel the subscription
                        $this->maybe_cancel_subscription($subscription);
                    }
                }

                // Save the updated keys
                update_user_meta($subscription->user_id, '_subscriptio_paypal_preapproval_keys', $current_keys);
            }
        }
    }

    /**
     * Maybe cancel the whole subscription
     *
     * @access private
     * @param obj $subscription
     * @return void
     */
    private function maybe_cancel_subscription($subscription)
    {
        // Check the corresponding option in settings
        $gateway = new Subscriptio_PayPal_Gateway();

        if ($gateway->preapproval_cancel == 'cancel' && $subscription->can_be_cancelled()) {

            // Start transaction
            $transaction = new Subscriptio_Transaction(null, 'automatic_cancellation', $subscription->id);

            // Make sure that subscription is not already cancelled
            if ($subscription->status == 'cancelled') {
                $transaction->update_result('error');
                $transaction->update_note(__('Subscription is already cancelled.', 'subscriptio'), true);
                return;
            }

            // Cancel subscription
            try {
                $subscription->cancel();
                $transaction->update_result('success');
                $transaction->update_note(__('User cancelled preapproval agreement - subscription cancelled as well.', 'subscriptio'), true);
            } catch (Exception $e) {
                $transaction->update_result('error');
                $transaction->update_note($e->getMessage(), true);
            }
        }
    }

    /**
     * Process payment set along with preapproval request
     *
     * @access private
     * @param obj $order
     * @param array $payment_fields
     * @return void
     */
    private function execute_first_subscription_payment($order, $payment_fields)
    {
        // Load payment gateway object to access its methods
        $gateway = new Subscriptio_PayPal_Gateway();

        $payment_response = $gateway->send_request('pay', $payment_fields);

        $pay_key = $payment_response->payKey;

        // Get results
        $result_message = $payment_response->responseEnvelope->ack;
        $payment_status = $payment_response->paymentExecStatus;

        // Request failed
        if ($payment_status == 'ERROR' || $result_message == 'Failure' || $result_message == 'FailureWithWarning') {
            $payment_error = $payment_response->error[0]->message;
            $order->add_order_note(__('Payment failed (PayPal). Error message: ', 'subscriptio-paypal') . $payment_error);
        }

        // Request was successful
        if ($payment_status == 'COMPLETED' && ($result_message == 'Success' || $result_message == 'SuccessWithWarning')) {

            // Add payment method
            update_post_meta($order->id, '_payment_method', 'subscriptio_paypal');

            // Save paykey in normal field
            update_post_meta($order->id, '_subscriptio_paypal_paykey', $pay_key);

            // Remove the temp fields
            delete_post_meta($order->id, '_subscriptio_paypal_temp_payfields');
            delete_post_meta($order->id, '_subscriptio_paypal_temp_preapproval_key');

            // Complete the order
            $order->add_order_note(sprintf(__('PayPal payment %s completed.', 'subscriptio-paypal'), $pay_key));
            $order->payment_complete();
        }
    }

}
}
