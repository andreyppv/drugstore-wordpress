<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle subscription-related events
 *
 * @class Subscriptio_Scheduler
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Scheduler')) {

class Subscriptio_Scheduler
{
    public static $scheduler_hooks = array(
        'subscriptio_scheduled_payment'     => 'Subscriptio_Scheduler::scheduled_payment',
        'subscriptio_scheduled_order'       => 'Subscriptio_Scheduler::scheduled_order',
        'subscriptio_scheduled_suspension'  => 'Subscriptio_Scheduler::scheduled_suspension',
        'subscriptio_scheduled_cancellation'  => 'Subscriptio_Scheduler::scheduled_cancellation',
        'subscriptio_scheduled_expiration'  => 'Subscriptio_Scheduler::scheduled_expiration',
        'subscriptio_scheduled_reminder'    => 'Subscriptio_Scheduler::scheduled_reminder',
        'subscriptio_scheduled_resume'    => 'Subscriptio_Scheduler::scheduled_resume',
    );

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Set up all hooks
        foreach (self::$scheduler_hooks as $hook => $callable) {
            add_action($hook, $callable, 10, 20);
        }

    }

    /**
     * Main scheduling function
     *
     * @access public
     * @param int $timestamp
     * @param string $hook
     * @param int $subscription_id
     * @return bool
     */
    public static function schedule($timestamp, $hook, $subscription_id)
    {
        if (wp_schedule_single_event($timestamp, $hook, array((int)$subscription_id)) === false) {
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * Unschedule possibly previously scheduled task(s)
     *
     * @access public
     * @param string $hook
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule($hook, $subscription_id = null, $timestamp = null)
    {
        // Specific single event?
        if ($timestamp) {

            // Match arguments?
            if ($subscription_id) {
                wp_unschedule_event($timestamp, $hook, array((int)$subscription_id));
            }
            else {
                wp_unschedule_event($timestamp, $hook);
            }
        }

        // All matching events?
        else {

            // Match arguments?
            if ($subscription_id) {
                wp_clear_scheduled_hook($hook, array((int)$subscription_id));
            }
            else {
                wp_clear_scheduled_hook($hook);
            }
        }
    }

    /**
     * Unschedule multiple events
     *
     * @access public
     * @param array $hooks
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_multiple($hooks, $subscription_id, $timestamp = null)
    {
        if (empty($hooks) || empty($subscription_id)) {
            return;
        }

        foreach ($hooks as $hook) {
            self::unschedule($hook, $subscription_id, $timestamp);
        }
    }

    /**
     * Unschedule all defined events
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_all($subscription_id, $timestamp = null)
    {
        if (empty($subscription_id)) {
            return;
        }

        foreach (self::$scheduler_hooks as $hook => $callable) {
            self::unschedule($hook, $subscription_id, $timestamp);
        }
    }

    /**
     * Get all scheduled events' timestamps
     *
     * @access public
     * @param int $subscription_id
     * @return int
     */
    public static function get_scheduled_events_timestamps($subscription_id)
    {
        $events = array();

        foreach (self::$scheduler_hooks as $hook => $callable) {
            if ($timestamp = self::get_scheduled_event_timestamp($hook, $subscription_id)) {
                $events[] = array(
                    'hook'      => $hook,
                    'timestamp' => $timestamp,
                );
            }
        }

        return $events;
    }

    /**
     * Get scheduled event timestamp
     *
     * @access public
     * @param string $hook
     * @param int $subscription_id
     * @return int
     */
    public static function get_scheduled_event_timestamp($hook, $subscription_id)
    {
        return wp_next_scheduled($hook, array((int)$subscription_id));
    }

    /**
     * Get scheduled event datetime
     *
     * @access public
     * @param string $hook
     * @param int $subscription_id
     * @return string|boolean
     */
    public static function get_scheduled_event_datetime($hook, $subscription_id)
    {
        // Get timestamp of the scheduled event
        $timestamp = self::get_scheduled_event_timestamp($hook, $subscription_id);

        if (!$timestamp) {
            return false;
        }

        return Subscriptio::get_adjusted_datetime($timestamp, null, $hook);
    }

    /**
     * Get permission to run scheduled task (prevent tasks from running more than once if WP cron misbehaves)
     *
     * @access public
     * @param string $event
     * @param int $subscription_id
     * @return bool
     */
    public static function get_permission($event, $subscription_id)
    {

    }

    /**
     * Schedule next payment event for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_payment($subscription_id, $timestamp)
    {
        return self::schedule($timestamp, 'subscriptio_scheduled_payment', $subscription_id);
    }

    /**
     * Scheduled next payment event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_payment($subscription_id)
    {
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'payment_due');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!isset($subscription->status)) {
            return;
        }

        // Load related order
        $order = new WC_Order($subscription->last_order_id);

        if (!$order) {
            $transaction->update_result('error');
            $transaction->update_note(__('Renewal order not found.', 'subscriptio'), true);
            return;
        }

        // Check if payment has been received manually - last order must be marked as processing or completed and payment_due date must be in the future
        if (time() <= $subscription->payment_due || in_array($order->status, array('processing', 'completed'))) {
            $transaction->update_result('error');
            $transaction->update_note(__('Payment seems to be already received.', 'subscriptio'), true);
            return;
        }

        // Attempt to process automatic payment if this is main website
        if (apply_filters('subscriptio_process_automatic_payment', Subscriptio::is_main_site(), $order, $subscription)) {
            if (apply_filters('subscriptio_automatic_payment', false, $order, $subscription)) {
                return;
            }
        }

        // Now either set to overdue or suspend or cancel, depending on settings
        try {
            $overdue_end_time = $subscription->calculate_overdue_time();
            $suspension_end_time = $subscription->calculate_suspension_time();  // This will be "fake" time for now in case $overdue_end_time is set

            // Overdue
            if ($overdue_end_time > 0) {

                // Set subscription to overdue
                $subscription->overdue();

                // Update transaction
                $transaction->update_result('success');
                $transaction->update_note(__('Payment not received. Subscription marked as overdue.', 'subscriptio'), true);

                // Schedule suspension and/or cancellation
                if ($suspension_end_time > 0) {
                    self::schedule_suspension($subscription->id, $overdue_end_time);
                    self::schedule_cancellation($subscription->id, $subscription->calculate_suspension_time($overdue_end_time));
                    $transaction->update_note(__('Suspension and cancellation scheduled.', 'subscriptio'), true);
                }
                else {
                    self::schedule_cancellation($subscription->id, $overdue_end_time);
                    $transaction->update_note(__('Cancellation scheduled.', 'subscriptio'), true);
                }
            }

            // Suspend
            else if ($suspension_end_time > 0) {

                // Not yet suspended? (can be suspended manually)
                if ($subscription->status != 'suspended') {

                    // Suspend suscription
                    $subscription->suspend();

                    // Update transaction
                    $transaction->update_result('success');
                    $transaction->update_note(__('Payment not received. Subscription suspended.', 'subscriptio'), true);
                }
                else {
                    $transaction->update_result('error');
                    $transaction->update_note(__('Payment not received but subscription is already suspended.', 'subscriptio'), true);
                }

                // Schedule cancellation
                self::schedule_cancellation($subscription->id, $suspension_end_time);
                $transaction->update_note(__('Cancellation scheduled.', 'subscriptio'), true);
            }

            // Cancel instantly (no overdue or suspension periods configured)
            else {

                // Cancel subscription
                $subscription->cancel();

                // Update transaction
                $transaction->update_result('success');
                $transaction->update_note(__('Payment not received. Subscription cancelled.', 'subscriptio'), true);
            }
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
    }

    /**
     * Schedule renewal order generation for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_order($subscription_id, $timestamp)
    {
        return self::schedule($timestamp, 'subscriptio_scheduled_order', $subscription_id);
    }

    /**
     * Scheduled renewal order event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_order($subscription_id)
    {
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'renewal_order');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!$subscription) {
            return;
        }

        // Create renewal order
        try {
            $order_id = Subscriptio_Order_Handler::create_renewal_order($subscription);
            $transaction->add_order_id($order_id);
            $transaction->update_result('success');
            $transaction->update_note(__('New order created, status set to pending.', 'subscriptio'), true);
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
    }

    /**
     * Schedule suspension for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_suspension($subscription_id, $timestamp)
    {
        return self::schedule($timestamp, 'subscriptio_scheduled_suspension', $subscription_id);
    }

    /**
     * Scheduled suspension event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_suspension($subscription_id)
    {
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'suspension');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!$subscription) {
            return;
        }

        // Make sure that subscription is not already suspended
        if ($subscription->status == 'suspended') {
            $transaction->update_result('error');
            $transaction->update_note(__('Subscription is already suspended.', 'subscriptio'), true);
            return;
        }

        // Make sure that payment due is in the past (double check that the payment has not been received until now)
        if (time() < $subscription->payment_due) {
            $transaction->update_result('error');
            $transaction->update_note(__('Payment due date is in the future, no reason to suspend.', 'subscriptio'), true);
            return;
        }

        // Suspend subscription
        try {
            $subscription->suspend();
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription suspended.', 'subscriptio'), true);
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
    }

    /**
     * Schedule cancellation for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_cancellation($subscription_id, $timestamp)
    {
        return self::schedule($timestamp, 'subscriptio_scheduled_cancellation', $subscription_id);
    }

    /**
     * Scheduled cancellation event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_cancellation($subscription_id)
    {
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'automatic_cancellation');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!$subscription) {
            return;
        }

        // Make sure that subscription is not already cancelled
        if ($subscription->status == 'cancelled') {
            $transaction->update_result('error');
            $transaction->update_note(__('Subscription is already cancelled.', 'subscriptio'), true);
            return;
        }

        // Make sure that payment due is in the past (double check that the payment has not been received until now)
        if (time() < $subscription->payment_due) {
            $transaction->update_result('error');
            $transaction->update_note(__('Payment due date is in the future, no reason to cancel.', 'subscriptio'), true);
            return;
        }

        // Cancel subscription
        try {
            $subscription->cancel();
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription cancelled.', 'subscriptio'), true);
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
    }

    /**
     * Schedule subscription expiration event for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_expiration($subscription_id, $timestamp)
    {
        return self::schedule($timestamp, 'subscriptio_scheduled_expiration', $subscription_id);
    }

    /**
     * Scheduled subscription expiration event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_expiration($subscription_id)
    {
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'expiration');

        // Make sure we are not caught in an infinite loop
        define('SUBSCRIPTIO_DOING_EXPIRATION', 'yes');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!$subscription) {
            return;
        }

        // Make sure that subscription is not already cancelled
        if ($subscription->status == 'cancelled') {
            $transaction->update_result('error');
            $transaction->update_note(__('Subscription is already cancelled.', 'subscriptio'), true);
            return;
        }

        // Make sure that subscription is not already expired
        if ($subscription->status == 'expired') {
            $transaction->update_result('error');
            $transaction->update_note(__('Subscription is already expired.', 'subscriptio'), true);
            return;
        }

        // Expire subscription
        try {
            $subscription->expire();
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription expired.', 'subscriptio'), true);
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
    }

    /**
     * Schedule reminder for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_reminder($subscription_id, $timestamp)
    {
        return self::schedule($timestamp, 'subscriptio_scheduled_reminder', $subscription_id);
    }

    /**
     * Scheduled reminder event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_reminder($subscription_id)
    {
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'payment_reminder');

        // Get subscription
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        if ($subscription) {

            // Send reminder
            try {
                Subscriptio_Mailer::send('payment_reminder', $subscription);
                $transaction->update_result('success');
                $transaction->update_note(__('Payment reminder sent.', 'subscriptio'), true);
            } catch (Exception $e) {
                $transaction->update_result('error');
                $transaction->update_note($e->getMessage(), true);
            }
        }
    }

    /**
     * Schedule the resume of paused subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_resume($subscription_id, $timestamp)
    {
        return self::schedule($timestamp, 'subscriptio_scheduled_resume', $subscription_id);
    }

    /**
     * Scheduled resume event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_resume($subscription_id)
    {
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'subscription_resume');

        // Get subscription
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        if ($subscription) {

            try {
                // Resume subscription
                $subscription->resume();
                // Update transaction
                $transaction->update_result('success');
                $transaction->update_note(__('Subscription was automatically resumed.', 'subscriptio'), true);
            } catch (Exception $e) {
                $transaction->update_result('error');
                $transaction->update_note($e->getMessage(), true);
            }
        }

    }

}

new Subscriptio_Scheduler();

}
