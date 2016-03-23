/**
 * Subscriptio Plugin Backend Scripts (loaded on all pages)
 */
jQuery(document).ready(function() {

    /**
     * Toggle subscription settings fields for simple product
     */
    function toggle_subscriptio_simple_product_fields() {
        if (jQuery('select#product-type').val() === 'simple') {
            if (jQuery('input#_subscriptio').is(':checked')) {
                jQuery('.show_if_subscriptio_simple').show();
            }
            else {
                jQuery('.show_if_subscriptio_simple').hide();
            }
        }
        else {
            jQuery('.show_if_subscriptio_simple').hide();
        }
    }

    toggle_subscriptio_simple_product_fields();

    jQuery('body').bind('woocommerce-product-type-change',function() {
        toggle_subscriptio_simple_product_fields();
    });

    jQuery('input#_subscriptio').change(function() {
        toggle_subscriptio_simple_product_fields();
    });

    /**
     * Toggle subscription settings fields for variable product
     */
    function toggle_subscriptio_variable_product_fields() {
        if (jQuery('select#product-type').val() === 'variable') {
            jQuery('input._subscriptio_variable').each(function() {
                if (jQuery(this).is(':checked')) {

                    // Display subscription options
                    jQuery(this).closest('tbody').find('tr.show_if_subscriptio_variable').each(function() {
                        jQuery(this).show();
                    });

                    // Write "Subscription" on variable product handle (if not present)
                    if (jQuery(this).closest('div.woocommerce_variation').find('.subscriptio_variable_product_handle_icon').length == 0) {
                        jQuery(this).closest('div.woocommerce_variation').find('h3').first().find('select').last().after('<i style="margin-left:10px;" class="fa fa-repeat subscriptio_variable_product_handle_icon" title="' + subscriptio_vars.title_subscription_product + '"></i>');
                    }
                }
                else {

                    // Hide subscription options
                    jQuery(this).closest('tbody').find('tr.show_if_subscriptio_variable').each(function() {
                        jQuery(this).hide();
                    });

                    // Remove "Subscription" from variable product handle
                    jQuery(this).closest('div.woocommerce_variation').find('.subscriptio_variable_product_handle_icon').remove();
                }
            });
        }
    }

    toggle_subscriptio_variable_product_fields();

    jQuery('input._subscriptio_variable').each(function() {
        jQuery(this).change(function() {
            toggle_subscriptio_variable_product_fields();
        });
    });

    jQuery('#variable_product_options').on('woocommerce_variations_added', function() {
        toggle_subscriptio_variable_product_fields();

        jQuery('input._subscriptio_variable').last().each(function() {
            jQuery(this).change(function() {
                toggle_subscriptio_variable_product_fields();
            });
        });
    });

    /**
     * Display admin shipping address edit fields
     */
    jQuery('#subscriptio_admin_edit_address').click(function(e) {
        e.preventDefault();
        jQuery(this).hide();
        jQuery('.subscriptio_admin_address').hide();
        jQuery('.subscriptio_admin_address_fields').show();
    });
    jQuery('#subscriptio_cancel_address_edit').click(function(e) {
        e.preventDefault();
        jQuery('.subscriptio_admin_address_fields').hide();
        jQuery('.subscriptio_admin_address').show();
        jQuery('#subscriptio_admin_edit_address').show();
    });

    /**
     * Show or hide pause fields
     */
    jQuery('#subscriptio_customer_pausing_allowed').each(function() {
        if (!jQuery(this).is(':checked')) {
            jQuery('#subscriptio_max_pauses').parent().parent().hide();
            jQuery('#subscriptio_max_pause_duration').parent().parent().hide();
        }
    });

    jQuery('#subscriptio_customer_pausing_allowed').change(function() {
        if (jQuery(this).is(':checked')) {
            jQuery('#subscriptio_max_pauses').parent().parent().show();
            jQuery('#subscriptio_max_pause_duration').parent().parent().show();
        }
        else {
            jQuery('#subscriptio_max_pauses').parent().parent().hide();
            jQuery('#subscriptio_max_pause_duration').parent().parent().hide();
        }
    });

});
