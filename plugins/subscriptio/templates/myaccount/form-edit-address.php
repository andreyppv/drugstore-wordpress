<?php

/**
 * Customer Subscription Edit Shipping Address
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php do_action('subscriptio_before_subscription_address_edit'); ?>

<h2><?php echo __('Edit Address', 'subscriptio'); ?></h2>

<p class="subscriptio_subscription_info"><?php printf(__('You are editing shipping address for subscription %s.', 'subscriptio'), $subscription->get_subscription_number()); ?></p>

<?php wc_print_notices(); ?>

<form method="post">

    <?php foreach ($address as $key => $field): ?>
        <?php woocommerce_form_field($key, $field, $field['value']); ?>
    <?php endforeach; ?>

    <p>
        <input type="hidden" name="action" value="subscriptio_edit_address" />
        <input type="submit" class="button" name="save_address" value="<?php _e('Save Address', 'subscriptio'); ?>" />
    </p>

</form>

<?php do_action('subscriptio_after_subscription_address_edit'); ?>
