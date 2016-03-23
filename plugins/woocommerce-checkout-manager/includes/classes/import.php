<?php
/**
 * WooCommerce Checkout Manager 
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// =======================================================
// IMPORT FUNCTION
// =======================================================

$options = get_option( 'wccs_settings' );
$options2 = get_option( 'wccs_settings2' );
$options3 = get_option( 'wccs_settings3' );

if (isset($_FILES['import']) && check_admin_referer('ie-import')) {
    if ($_FILES['import']['error'] > 0) {
    } else {
		$encode_options = $_FILES['import']['tmp_name'];

        if (($handle = fopen( $encode_options , "r")) !== FALSE) {
            
            $rows = 0;
            $header = fgetcsv($handle, 10000, ",");
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                $c = 0;
                        foreach($header as $value) { 
                                $options['buttons'][$rows][$value] = $data[$c];
                        $c++;
                        }
            $rows++;
            update_option( 'wccs_settings', $options );       
            }   
            
        fclose($handle);
        }
    }
}

// BILLING IMPORT ===========================================================
// ==========================================================================
if (isset($_FILES['billing-import']) && check_admin_referer('ie-import')) {
    if ($_FILES['billing-import']['error'] > 0) {
	} else {
		$encode_options = $_FILES['billing-import']['tmp_name'];

        if (($handle = fopen( $encode_options , "r")) !== FALSE) {
            
            $rows = 0;
            $header = fgetcsv($handle, 10000, ",");
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                $c = 0;
                        foreach($header as $value) { 
                                $options3['billing_buttons'][$rows][$value] = $data[$c];
                        $c++;
                        }
            $rows++;
            update_option( 'wccs_settings3', $options3 );       
            }   
        fclose($handle);
        }       
    }
}


// SHIPPING IMPORT ================================================================
// ================================================================================
if (isset($_FILES['shipping-import']) && check_admin_referer('ie-import')) {
    if ($_FILES['shipping-import']['error'] > 0) {
	} else {
		$encode_options = $_FILES['shipping-import']['tmp_name'];

        if (($handle = fopen( $encode_options , "r")) !== FALSE) {
            
            $rows = 0;
            $header = fgetcsv($handle, 10000, ",");
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                $c = 0;
                        foreach($header as $value) { 
                                $options2['shipping_buttons'][$rows][$value] = $data[$c];
                        $c++;
                        }
            $rows++;
            update_option( 'wccs_settings2', $options2 );       
            }   
         fclose($handle);
        }
    }
}


// GENERAL IMPORT =========================================================== ..
// ==========================================================================
if (isset($_FILES['general-import']) && check_admin_referer('ie-import')) {
    if ($_FILES['general-import']['error'] > 0) {
	} else {
		$encode_options = $_FILES['general-import']['tmp_name'];

        if (($handle = fopen( $encode_options , "r")) !== FALSE) {
            
           $rows = 0;
            $header = fgetcsv($handle, 10000, ",");
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                $c = 0;
                        foreach($header as $value) { 
                                $options['checkness'][$value] = $data[$c];
                        $c++;
                        }
            $rows++;
            update_option( 'wccs_settings', $options );       
            }   
            
        }
                fclose($handle);
    }
}




// =======================================================================================================
		?>
		
		<form method='post' class='import_form' enctype='multipart/form-data'>
			
				<?php wp_nonce_field('ie-import'); ?>
				<input type="button" class="button button-hero button-secondary import tap_dat12" value="Import" />

                <div id="wp-auth-check-wrap" class="click_showWccm" style="display:none;">
	                <div id="wp-auth-check-bg"></div>
	                <div id="wp-auth-check" style="max-height: 700px;">
	                <div class="wp-auth-check-close" tabindex="0" title="Close"></div>
                    <div class="updated realStop"><p>Please choose CSV file. <br /><span class="make_smalla">Max Upload Size: <?php echo size_format( wp_max_upload_size() ); ?> <br /></span></p></div>
                        <div class="updated jellow">

							<p><span class="heading_smalla">General Settings<br></span><input type='submit' class="button button-hero button-secondary wccm_importer_submit" name='submit' value='<?php _e('Import CSV', 'woocommerce-checkout-manager'); ?>' /> <input type='file' name='general-import' class="wccm_importer" /></p>

				            <p><span class="heading_smalla">Billing fields<br></span><input type='submit' class="button button-hero button-secondary wccm_importer_submit" name='submit' value='<?php _e('Import CSV', 'woocommerce-checkout-manager'); ?>' /> <input type='file' name='billing-import' class="wccm_importer" /></p>

							<p><span class="heading_smalla">Shipping fields<br></span><input type='submit' class="button button-hero button-secondary wccm_importer_submit" name='submit' value='<?php _e('Import CSV', 'woocommerce-checkout-manager'); ?>' /><input type='file' name='shipping-import' class="wccm_importer" /> </p>

							<p><span class="heading_smalla">Additional fields<br></span><input type='submit' class="button button-hero button-secondary wccm_importer_submit" name='submit' value='<?php _e('Import CSV', 'woocommerce-checkout-manager'); ?>' /><input type='file' name='import' class="wccm_importer" /> </p>

                        </div>
	                </div>
	            </div>	
		</form>
	

<script type="text/javascript">
jQuery(document).ready(function() {

    jQuery('.import.tap_dat12').click(function() {
        jQuery('#wp-auth-check-wrap').slideToggle('slow');
    });
});
</script>