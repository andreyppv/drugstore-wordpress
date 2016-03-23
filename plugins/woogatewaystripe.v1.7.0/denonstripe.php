<?php
/*
Plugin Name: Stripe Gateway
Plugin URI: http://codecanyon.net/item/stripe-credit-card-gateway-for-woocommerce/1343790
Description: Provides a Credit Card Payment Gateway through Stripe for woo-commerece.
Version: 1.7.0
Author: DenonStudio
Author URI: http://http://codecanyon.net/user/DenonStudio
Requires at least: 4.0.0
Tested up to: 4.1.1
*/

/*
 * Title   : Stripe Payment extension for Woo-Commerece
 * Author  : DenonStudio
 * Url     : http://codecanyon.net/user/DenonStudio/portfolio
 * License : http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 */

function init_stripe_gateway() 
{
    if (class_exists('WC_Payment_Gateway'))
    {
        include_once('class.stripeextension.php');
    }
}

add_action('plugins_loaded', 'init_stripe_gateway', 0);