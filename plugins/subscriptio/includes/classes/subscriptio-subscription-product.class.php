<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to subscription products
 *
 * @class Subscriptio_Subscription_Product
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Subscription_Product')) {

class Subscriptio_Subscription_Product
{
    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        add_filter('product_type_options', array($this, 'wc_hook_product_type_options'));
        add_action('woocommerce_product_options_general_product_data', array($this, 'wc_hook_woocommerce_product_options_general_product_data'));
        add_action('woocommerce_variation_options', array($this, 'wc_hook_woocommerce_variation_options'), 10, 3);
        add_action('woocommerce_product_after_variable_attributes', array($this, 'wc_hook_woocommerce_product_after_variable_attributes'), 10, 3);
        add_action('woocommerce_process_product_meta_simple', array($this, 'wc_hook_woocommerce_process_product_meta'));
        add_action('woocommerce_process_product_meta_variable', array($this, 'wc_hook_woocommerce_process_product_meta_variable'));
        add_filter('manage_edit-product_columns', array($this, 'wc_hook_manage_edit_product_columns'), 99);
        add_action('manage_product_posts_custom_column', array($this, 'wc_hook_manage_product_posts_custom_column'), 99);

        // Change product prices
        add_filter('woocommerce_get_price_html', array($this, 'change_product_price'), 10, 2);
        add_filter('woocommerce_get_variation_price_html', array($this, 'change_product_price'), 10, 2);
        add_filter('woocommerce_grouped_price_html', array($this, 'change_product_price'), 10, 2);

        // Hook to WordPress 'init' action
        add_action('init', array($this, 'on_init'));
    }

    /**
     * Executred on init action
     *
     * @access public
     * @return void
     */
    public function on_init()
    {
        // Maybe change Add To Cart button text
        if (Subscriptio::option('add_to_cart')) {
            add_filter('woocommerce_product_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_variable_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_grouped_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_external_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_out_of_stock_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
        }
    }

    /**
     * Add simple product property checkbox (checkbox that converts simple product to simple subscription product)
     *
     * @access public
     * @param array $checkboxes
     * @return array
     */
    public function wc_hook_product_type_options($checkboxes)
    {
        $checkboxes['subscriptio'] = array(
            'id'            => '_subscriptio',
            'wrapper_class' => 'show_if_simple',
            'label'         => __('Subscription', 'subscriptio'),
            'description'   => __('Sell this product as a subscription product with recurring billing.', 'subscriptio'),
            'default'       => 'no'
        );

        return $checkboxes;
    }

    /**
     * Display subscription settings fields on product page (simple product)
     *
     * @access public
     * @return void
     */
    public function wc_hook_woocommerce_product_options_general_product_data()
    {
        // Get post
        global $post;
        $post_id = $post->ID;

        // Retrieve required post meta fields
        $_subscriptio_price_time_value      = get_post_meta($post_id, '_subscriptio_price_time_value', true);
        $_subscriptio_price_time_unit       = get_post_meta($post_id, '_subscriptio_price_time_unit', true);
        $_subscriptio_free_trial_time_value = get_post_meta($post_id, '_subscriptio_free_trial_time_value', true);
        $_subscriptio_free_trial_time_unit  = get_post_meta($post_id, '_subscriptio_free_trial_time_unit', true);
        $_subscriptio_signup_fee            = get_post_meta($post_id, '_subscriptio_signup_fee', true);
        $_subscriptio_max_length_time_value = get_post_meta($post_id, '_subscriptio_max_length_time_value', true);
        $_subscriptio_max_length_time_unit  = get_post_meta($post_id, '_subscriptio_max_length_time_unit', true);

        require SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/product/simple-product-meta.php';
    }

    /**
     * Display subscription settings fields on product page (variation product)
     *
     * @access public
     * @param int $loop
     * @param array $variation_data
     * @param object $variation
     * @return void
     */
    public function wc_hook_woocommerce_product_after_variable_attributes($loop, $variation_data, $variation)
    {
        // Get post id
        $post_id = $variation->ID;

        // Retrieve required post meta fields
        $_subscriptio_price_time_value      = get_post_meta($post_id, '_subscriptio_price_time_value', true);
        $_subscriptio_price_time_unit       = get_post_meta($post_id, '_subscriptio_price_time_unit', true);
        $_subscriptio_free_trial_time_value = get_post_meta($post_id, '_subscriptio_free_trial_time_value', true);
        $_subscriptio_free_trial_time_unit  = get_post_meta($post_id, '_subscriptio_free_trial_time_unit', true);
        $_subscriptio_signup_fee            = get_post_meta($post_id, '_subscriptio_signup_fee', true);
        $_subscriptio_max_length_time_value = get_post_meta($post_id, '_subscriptio_max_length_time_value', true);
        $_subscriptio_max_length_time_unit  = get_post_meta($post_id, '_subscriptio_max_length_time_unit', true);

        require SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/product/variable-product-meta.php';
    }

    /**
     * Add variable product property checkbox (checkbox that converts variable product to variable subscription product)
     *
     * @access public
     * @param int $loop
     * @param array $variation_data
     * @param object $variation
     * @return void
     */
    public function wc_hook_woocommerce_variation_options($loop, $variation_data, $variation)
    {
        echo '<label><input type="checkbox" class="checkbox _subscriptio_variable" name="_subscriptio[' . $loop . ']" ' . checked(self::is_subscription($variation->ID), true, false) . ' /> ' . __('Subscription', 'subscriptio') . ' <a class="tips" data-tip="' . __('Sell this variable product as a subscription product with recurring billing.', 'subscriptio') . '" href="#">[?]</a></label>';
    }

    /**
     * Save simple product subscription-related meta data
     *
     * @access public
     * @param int $post_id
     * @param bool $variable
     * @return void
     */
    public function wc_hook_woocommerce_process_product_meta($post_id, $variable = false, $loop = null)
    {
        if ($variable) {
            if (empty($_POST['_subscriptio']) || !isset($_POST['_subscriptio'][$loop])) {
                return array('result' => false);
            }
            $_subscriptio = $_POST['_subscriptio'][$loop];
        }
        else if (!empty($_POST['_subscriptio'])) {
            $_subscriptio = $_POST['_subscriptio'];
        }

        // Not a subscription?
        if (!isset($_subscriptio) || $_subscriptio != 'on') {
            delete_post_meta($post_id, '_subscriptio');
            delete_post_meta($post_id, '_subscriptio_price_time_value');
            delete_post_meta($post_id, '_subscriptio_price_time_unit');
            delete_post_meta($post_id, '_subscriptio_free_trial_time_value');
            delete_post_meta($post_id, '_subscriptio_free_trial_time_unit');
            delete_post_meta($post_id, '_subscriptio_max_length_time_value');
            delete_post_meta($post_id, '_subscriptio_max_length_time_unit');
            delete_post_meta($post_id, '_subscriptio_signup_fee');
            return;
        }

        // Mark this product as subscription
        update_post_meta($post_id, '_subscriptio', 'yes');

        // Get time units
        $time_units = Subscriptio::get_time_units();

        // Price period value (how many periods of time (by time unit) the price covers, e.g. price can be for 3 months)
        $_subscriptio_price_time_value = $variable ? $_POST['_subscriptio_price_time_value'][$loop] : $_POST['_subscriptio_price_time_value'];
        $price_period_value = Subscriptio::get_natural_number($_subscriptio_price_time_value);
        update_post_meta($post_id, '_subscriptio_price_time_value', $price_period_value);

        // Price time unit (e.g. a month)
        $_subscriptio_price_time_unit = $variable ? $_POST['_subscriptio_price_time_unit'][$loop] : $_POST['_subscriptio_price_time_unit'];
        $price_time_unit = isset($time_units[$_subscriptio_price_time_unit]) ? $_subscriptio_price_time_unit : array_shift(array_keys($time_units));
        update_post_meta($post_id, '_subscriptio_price_time_unit', $price_time_unit);

        // Get correct trial period field value
        $_subscriptio_free_trial_time_value = $variable ? $_POST['_subscriptio_free_trial_time_value'][$loop] : $_POST['_subscriptio_free_trial_time_value'];

        // Trial period
        if (isset($_subscriptio_free_trial_time_value) && !empty($_subscriptio_free_trial_time_value)) {

            // Free trial period value (how many periods of time (by time unit) the trial period covers, e.g. trial period can be 2 weeks)
            $trial_period_value = Subscriptio::get_natural_number($_subscriptio_free_trial_time_value);
            update_post_meta($post_id, '_subscriptio_free_trial_time_value', $trial_period_value);

            // Free trial time unit
            $_subscriptio_free_trial_time_unit = $variable ? $_POST['_subscriptio_free_trial_time_unit'][$loop] : $_POST['_subscriptio_free_trial_time_unit'];
            $trial_time_unit = isset($time_units[$_subscriptio_free_trial_time_unit]) ? $_subscriptio_free_trial_time_unit : array_shift(array_keys($time_units));
            update_post_meta($post_id, '_subscriptio_free_trial_time_unit', $trial_time_unit);
        }
        else {
            delete_post_meta($post_id, '_subscriptio_free_trial_time_value');
            delete_post_meta($post_id, '_subscriptio_free_trial_time_unit');
        }

        // Get correct maximum length period value
        $_subscriptio_max_length_time_value = $variable ? $_POST['_subscriptio_max_length_time_value'][$loop] : $_POST['_subscriptio_max_length_time_value'];

        // Max length period
        if (isset($_subscriptio_max_length_time_value) && !empty($_subscriptio_max_length_time_value)) {

            // Max subscription length period value (e.g. up to 12 months)
            $length_period_value = Subscriptio::get_natural_number($_subscriptio_max_length_time_value);
            update_post_meta($post_id, '_subscriptio_max_length_time_value', $length_period_value);

            // Max subscription length time unit
            $_subscriptio_max_length_time_unit = $variable ? $_POST['_subscriptio_max_length_time_unit'][$loop] : $_POST['_subscriptio_max_length_time_unit'];
            $length_time_unit = isset($time_units[$_subscriptio_max_length_time_unit]) ? $_subscriptio_max_length_time_unit : array_shift(array_keys($time_units));
            update_post_meta($post_id, '_subscriptio_max_length_time_unit', $length_time_unit);
        }
        else {
            delete_post_meta($post_id, '_subscriptio_max_length_time_value');
            delete_post_meta($post_id, '_subscriptio_max_length_time_unit');
        }

        // Get correct signup fee field value
        $_subscriptio_signup_fee = $variable ? $_POST['_subscriptio_signup_fee'][$loop] : $_POST['_subscriptio_signup_fee'];

        // Signup fee
        if (isset($_subscriptio_signup_fee) && !empty($_subscriptio_signup_fee)) {
            $signup_fee = Subscriptio::get_float_number_as_string($_subscriptio_signup_fee);

            if ($signup_fee != 0) {
                update_post_meta($post_id, '_subscriptio_signup_fee', $signup_fee);
            }
            else {
                delete_post_meta($post_id, '_subscriptio_signup_fee');
            }
        }
        else {
            delete_post_meta($post_id, '_subscriptio_signup_fee');
        }

        return !$variable ? null : array('result' => true);
    }

    /**
     * Save variable product subscription-related meta data
     *
     * @access public
     * @param int $post_id
     * @return void
     */
    public function wc_hook_woocommerce_process_product_meta_variable($post_id)
    {
        // Find max post id
        $all_ids = $_POST['variable_post_id'];
        $max_id = max(array_keys($all_ids));

        $variable_product_has_subscriptions = false;

        // Iterate over all variations and save them
        for ($i = 0; $i <= $max_id; $i++) {

            // Skip non-existing keys
            if (!isset($all_ids[$i])) {
                continue;
            }

            // Get post ID for current variable product
            $variable_post_id = (int) $all_ids[$i];

            // Handle as simple product
            $result = $this->wc_hook_woocommerce_process_product_meta($variable_post_id, true, $i);

            if ($result['result']) {
                $variable_product_has_subscriptions = true;
            }
        }

        if ($variable_product_has_subscriptions) {
            update_post_meta($post_id, '_subscriptio', 'yes');
        }
        else {
            delete_post_meta($post_id, '_subscriptio');
        }
    }

    /**
     * Insert custom column into product list view header
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function wc_hook_manage_edit_product_columns($columns)
    {
        // Check if array format is as expected
        if (!is_array($columns) || !isset($columns['product_type'])) {
            return $columns;
        }

        // Insert new column after column product_type
        $offset = array_search('product_type', array_keys($columns)) + 1;

        return array_merge (
                array_slice($columns, 0, $offset),
                array('subscriptio' => '<span class="subscriptio_product_list_header_icon tips" data-tip="' . __('Subscription', 'subscriptio') . '">' . __('Subscription', 'subscriptio') . '</span>'),
                array_slice($columns, $offset, null)
            );
    }

    /**
     * Insert custom column into product list view
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function wc_hook_manage_product_posts_custom_column($column)
    {
        global $post, $woocommerce, $the_product;

        if (empty($the_product) || $the_product->id != $post->ID) {
            $the_product = get_product($post);
        }

        if ($column == 'subscriptio') {
            if (self::is_subscription($the_product->id)) {
                $tip = $the_product->product_type == 'simple' ? __('This product is a subscription', 'subscriptio') : __('Contains at least one subscription', 'subscriptio');
                echo '<i class="fa fa-repeat subscriptio_product_list_icon tips" data-tip="' . $tip . '"></i>';
            }
        }
    }

    /**
     * Check if Subscriptio settings for specific product seems to be ok
     *
     * @access public
     * @param array $product_post_meta
     * @return bool
     */
    public static function is_ok($product_post_meta)
    {
        $time_units = Subscriptio::get_time_units();

        // Make sure the payment interval looks ok
        if (isset($product_post_meta['_subscriptio_price_time_value']) && isset($product_post_meta['_subscriptio_price_time_unit'])) {
            if (!isset($time_units[$product_post_meta['_subscriptio_price_time_unit']])) {
                return false;
            }
            if (!is_numeric($product_post_meta['_subscriptio_price_time_value'])) {
                return false;
            }
        }
        else {
            return false;
        }

        return true;
    }

    /**
     * Check if regular product is a subscription or if variable product contains subscription variations
     * $product_id can be variation id
     *
     * @access public
     * @param int $product_id
     * @return bool
     */
    public static function is_subscription($product_id)
    {
        if (get_post_meta($product_id, '_subscriptio')) {
            return true;
        }

        // No? Check children...
        $children = get_children(array(
            'post_parent' => $product_id,
            'post_type' => 'product'
        ));

        foreach ($children as $child) {
            if (get_post_meta($child->ID, '_subscriptio')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format recurring amount
     *
     * @access public
     * @param float $amount
     * @param string $unit
     * @param int $value
     * @param string $currency
     * @param bool $sale_price
     * @return string|bool
     */
    public static function format_recurring_amount($amount, $unit, $value, $currency = null, $sale_price = false)
    {
        // Get time units
        $time_units = Subscriptio::get_time_units();

        if (!isset($time_units[$unit]) || !is_numeric($value)) {
            return false;
        }

        // Get store currency if none passed in
        if (!$currency) {
            $currency = get_woocommerce_currency();
        }

        // Format price with currency
        $formatted_amount = Subscriptio::get_formatted_price($amount, $currency, $sale_price);

        // Possibly pluralize unit name first
        $formatted_unit_name = call_user_func($time_units[$unit]['translation_callback'], $unit, $value);

        // Format and return
        if ($value == 1) {
            return apply_filters('subscriptio_formatted_recurring_amount', sprintf(__('%1$s / %2$s', 'subscriptio'), $formatted_amount, $formatted_unit_name));
        }
        else {
            return apply_filters('subscriptio_formatted_recurring_amount', sprintf(__('%1$s / %2$s %3$s', 'subscriptio'), $formatted_amount, $value, $formatted_unit_name));
        }
    }

    /**
     * Change frontend product price with Subscription price
     *
     * @access public
     * @param float $price
     * @param object $product
     * @return string
     */
    public function change_product_price($price, $product)
    {
        // Product is subscription?
        if (!is_admin() && self::is_subscription($product->id)) {

            // Variable or grouped product? Then we need to check if all prices are equal or choose cheapest variation and display "From: ..."
            if (in_array($product->product_type, array('variable', 'grouped'))) {
                list($id, $equal) = self::get_cheapest_child_price($product);
                return Subscriptio_Subscription_Product::get_formatted_subscription_price($id, false, false, 1, null, true, $equal);
            }

            // Simple product or variation
            $id = $product->product_type == 'variation' ? $product->variation_id : $product->id;

            // Double check that particular variation is a subscription
            if ($product->product_type != 'variation' || self::is_subscription($id)) {
                return Subscriptio_Subscription_Product::get_formatted_subscription_price($id, false, false);
            }
        }

        return $price;
    }

    /**
     * Check if variation or grouped product prices are all equal and return cheapest price
     *
     * @access public
     * @param object $product
     * @return array
     */
    public static function get_cheapest_child_price($product)
    {
        $ids = array();

        // Grouped product?
        if ($product->product_type == 'grouped') {
            $children = get_children(array(
                'post_parent' => $product->id,
                'post_type' => 'product',
            ));

            foreach ($children as $child) {
                $ids[] = $child->ID;
            }
        }

        // Variable product
        else {
            $variations = $product->get_available_variations();

            foreach ($variations as $variation) {
                $ids[] = $variation['variation_id'];
            }
        }

        if (empty($ids)) {
            return array(0, true);
        }

        $selected_id = null;
        $cheapest = null;
        $equal = true;

        foreach ($ids as $id) {

            // We skip non-subscription variations as there is no easy way to compare recurring payments with one-off payments
            if (!self::is_subscription($id)) {
                $equal = false;
                continue;
            }

            $child_product = new WC_Product($id);
            $child_meta = Subscriptio::unwrap_post_meta(get_post_meta($id));

            $child_days = Subscriptio_Subscription::get_period_length_in('day', $child_meta['_subscriptio_price_time_unit'], $child_meta['_subscriptio_price_time_value']);
            $price_per_day = $child_days == 0 ? 0 : $child_product->get_price() / $child_days;

            if ($cheapest === null) {
                $selected_id = $id;
                $cheapest = $price_per_day;
            }
            else if ($price_per_day < $cheapest) {
                $selected_id = $id;
                $cheapest = $price_per_day;
                $equal = false;
            }
            else if ($price_per_day > $cheapest) {
                $equal = false;
            }
        }

        return array($selected_id, $equal);
    }

    /**
     * Check if cart item price needs to be changed and return new price if it does
     *
     * @access public
     * @param int $id
     * @param float $price
     * @return float|bool
     */
    public static function get_new_price($id, $price)
    {
        $new_price = false;

        $meta = Subscriptio::unwrap_post_meta(get_post_meta($id));

        // Free trial?
        if (!empty($meta['_subscriptio_free_trial_time_value'])) {
            $new_price = 0;
        }

        // Signup fee?
        if (!empty($meta['_subscriptio_signup_fee'])) {
            $new_price = (float) $meta['_subscriptio_signup_fee'] + ($new_price === false ? $price : $new_price);
        }

        return $new_price;
    }

    /**
     * Return recurring subscription price
     * For now this is basically product price as set in settings
     * adjusted to account for tax display mode
     *
     * @access public
     * @param int $id
     * @param bool $is_cart
     * @param int $quantity
     * @param float $price
     * @param bool $is_signup_fee
     * @return float
     */
    public static function get_recurring_price($id, $is_cart = false, $quantity = 1, $price = '', $is_signup_fee = false)
    {
        // Load product object
        $product = new WC_Product($id);

        // Get only regular price, even for product on sale, but don't change the signup fee
        if ($product->is_on_sale() && !$is_signup_fee) {
            $price = $product->get_regular_price();
        }

        // Get correct price
        if (self::tax_display_mode_is_inclusive($is_cart)) {
            $price = $product->get_price_including_tax($quantity, $price);
        }
        else {
            $price = $product->get_price_excluding_tax($quantity, $price);
        }

        return (float) $price;
    }

    /**
     * Return sale price for subscription
     *
     * @access public
     * @param int $id
     * @param bool $is_cart
     * @param int $quantity
     * @return float
     */
    public static function get_sale_price($id, $is_cart = false, $quantity = 1)
    {
        // Load product object
        $product = new WC_Product($id);

        // Is it on sale?
        if ($product->is_on_sale()) {

            // Get correct sale price
            if (self::tax_display_mode_is_inclusive($is_cart)) {
                $price = $product->get_price_including_tax($quantity, $product->get_sale_price());
            }
            else {
                $price = $product->get_price_excluding_tax($quantity, $product->get_sale_price());
            }

            return (float) $price;
        }

        else {
            return false;
        }
    }

    /**
     * Check if tax display mode is inclusive
     *
     * @access public
     * @param bool $is_cart
     * @return string
     */
    public static function tax_display_mode_is_inclusive($is_cart = false)
    {
        return ($is_cart ? get_option('woocommerce_tax_display_cart') : get_option('woocommerce_tax_display_shop')) === 'incl';
    }

    /**
     * Return formatted subscription price to be displayed on product pages and cart
     *
     * @access public
     * @param int $id
     * @param bool $is_cart
     * @param bool $is_subtotal
     * @param int $quantity
     * @param float $price_now
     * @param bool $is_variable
     * @param bool $variations_equal
     * @return string
     */
    public static function get_formatted_subscription_price($id, $is_cart, $is_subtotal = false, $quantity = 1, $price_now = null, $is_variable = false, $variations_equal = false)
    {
        $meta = Subscriptio::unwrap_post_meta(get_post_meta($id));

        $recurring_price = Subscriptio_Subscription_Product::get_recurring_price($id, $is_cart, $quantity);
        $sale_price = Subscriptio_Subscription_Product::get_sale_price($id, $is_cart, $quantity);

        // Check if product is configured properly, if not - revert to standard price display
        if (!isset($meta['_subscriptio_price_time_unit']) || !isset($meta['_subscriptio_price_time_value'])) {
            return Subscriptio::get_formatted_price($recurring_price, get_woocommerce_currency());
        }

        // Cart/checkout page?
        if ($is_subtotal) {

            $recurring_price_html = self::format_recurring_amount($recurring_price, $meta['_subscriptio_price_time_unit'], $meta['_subscriptio_price_time_value'], null, $sale_price);

            // Payable now differs from recurring amount?
            if ($price_now != $recurring_price) {
                $html_now = Subscriptio::get_formatted_price($price_now, get_woocommerce_currency());
                return sprintf(__('%1$s now then %2$s', 'subscriptio'), $html_now, $recurring_price_html);
            }
            else {
                return self::format_recurring_amount($price_now, $meta['_subscriptio_price_time_unit'], $meta['_subscriptio_price_time_value']);
            }
        }

        // Other pages
        else {

            $recurring_price_html = self::format_recurring_amount($recurring_price, $meta['_subscriptio_price_time_unit'], $meta['_subscriptio_price_time_value'], null, $sale_price);

            // Get tax mode adjusted signup fee
            if (!empty($meta['_subscriptio_signup_fee'])) {
                $signup_fee = self::get_recurring_price($id, $is_cart, $quantity, $meta['_subscriptio_signup_fee'], true);
                $signup_fee = Subscriptio::get_formatted_price($signup_fee, get_woocommerce_currency());
            }

            // Any free trial?
            if (!empty($meta['_subscriptio_free_trial_time_value'])) {
                $time_units = Subscriptio::get_time_units();

                if (isset($time_units[$meta['_subscriptio_free_trial_time_unit']]) && is_numeric($meta['_subscriptio_free_trial_time_value'])) {
                    $free_trial = $meta['_subscriptio_free_trial_time_value'] . ' ' . call_user_func($time_units[$meta['_subscriptio_free_trial_time_unit']]['translation_callback'], $meta['_subscriptio_free_trial_time_unit'], $meta['_subscriptio_free_trial_time_value']);
                }
                else {
                    $free_trial = '';
                }
            }

            // Free trial & Signup fee
            if (!empty($meta['_subscriptio_signup_fee']) && !empty($meta['_subscriptio_free_trial_time_value'])) {
                if ($is_variable && !$variations_equal) {
                    return sprintf(__('From %1$s', 'subscriptio'), $recurring_price_html);
                }
                else {
                    return sprintf(__('%1$s with a free trial of %2$s and a sign-up fee of %3$s', 'subscriptio'), $recurring_price_html, $free_trial, $signup_fee);
                }
            }

            // Free trial
            else if (!empty($meta['_subscriptio_free_trial_time_value'])) {
                if ($is_variable && !$variations_equal) {
                    return sprintf(__('From %1$s', 'subscriptio'), $recurring_price_html);
                }
                else {
                    return sprintf(__('%1$s with a free trial of %2$s', 'subscriptio'), $recurring_price_html, $free_trial);
                }
            }

            // Signup fee
            else if (!empty($meta['_subscriptio_signup_fee'])) {
                if ($is_variable && !$variations_equal) {
                    return sprintf(__('From %1$s', 'subscriptio'), $recurring_price_html);
                }
                else {
                    return sprintf(__('%1$s with a sign-up fee of %2$s', 'subscriptio'), $recurring_price_html, $signup_fee);
                }
            }

            // Plain recurring price
            else {
                if ($is_variable && !$variations_equal) {
                    return sprintf(__('From %s', 'subscriptio'), $recurring_price_html);
                }
                else {
                    return $recurring_price_html;
                }
            }
        }
    }

    /**
     * Change Add To Cart button label
     *
     * @access public
     * @return string
     */
    public function change_add_to_cart_label($label)
    {
        global $product;

        // Change label only if product is subscription
        if (self::is_subscription($product->id)) {
            return Subscriptio::option('add_to_cart');
        }

        // Or return the default label
        return $label;
    }

}

new Subscriptio_Subscription_Product();

}
