<?php
/*
 * Title   : Stripe Payment extension for Woo-Commerece
 * Author  : DenonStudio
 * Url     : http://codecanyon.net/user/DenonStudio/portfolio
 * License : http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 */
?>

<h3>
    <?php _e('Credit Card Payment', 'woocommerce'); ?>
</h3>

<p><?php _e('Allows Credit Card payments.', 'woocommerce'); ?></p>

<table class="form-table">
    <?php $this->generate_settings_html(); ?>
</table>
