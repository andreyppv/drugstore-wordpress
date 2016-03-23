<?php 

/**
 * WooCommerce Checkout Manager
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
?>

<div class="widefat general-semi upload_files" border="0">

	<div class="section"><h3 class="heading"><?php _e('General File Upload', 'woocommerce-checkout-manager'); ?></h3></div>

	<div class="section">
        <h3 class="heading checkbox">
        
            <div class="option">
                <input name="wccs_settings[checkness][enable_file_upload]" type="checkbox" value="true" <?php if ( !empty($options['checkness']['enable_file_upload'])) echo "checked='checked'"; ?> /><span></span>
	
                <div class="info-of"><?php _e('Allow Customers to Upload Files', 'woocommerce-checkout-manager');  ?></div>
            </div>
        </h3>
    </div>



	<div class="section">
        <h3 class="heading checkbox">

            <div class="option" >
                <input name="wccs_settings[checkness][cat_file_upload]" type="checkbox" value="true" <?php if ( !empty($options['checkness']['cat_file_upload'])) echo "checked='checked'"; ?> /><span></span>

                <div class="info-of"><?php _e('Categorize Uploaded Files', 'woocommerce-checkout-manager');  ?>

                    | <span style="cursor: pointer;" class="show_hide2"><a>read more</a></span>

                    <span style="display:none;" class="slidingDiv2">
                        <br /><br />
                
                        <?php _e('Changes uploaded files location folder from', 'woocommerce-checkout-manager');  ?> <br />
                        <strong><?php echo $upload_dir['url']; ?>/</strong> <br />
                        <?php _e('to', 'woocommerce-checkout-manager');  ?><br />
                        <strong><?php echo $upload_dir['baseurl']; ?>/wooccm_uploads/{order number}/</strong>
                    </span>

                </div>
            </div>
        </h3> 
    </div>



	<div class="section">
            <h3 class="heading">
                <?php _e('Notification E-mail', 'woocommerce-checkout-manager');  ?>
            </h3>

            <div class="option">
                <input name="wccs_settings[checkness]['upload_title']" type="text" placeholder="Order Uploaded Files" value="<?php echo esc_attr($options['checkness']['upload_title']); ?>" />
	        </div>
    </div>
	
	<div class="section">
            <h3 class="heading">
                <?php _e('Notification E-mail', 'woocommerce-checkout-manager');  ?>
            </h3>

            <div class="option">
                <input name="wccs_settings[checkness][wooccm_notification_email]" type="text" value="<?php echo $options['checkness']['wooccm_notification_email']; ?>" />
	        </div>
    </div>



	<div class="section">
            <h3 class="heading">
                <?php _e('Products', 'woocommerce-checkout-manager');  ?>
            </h3>

            <div class="info-of">
                <?php _e('Allow File Upload', 'woocommerce-checkout-manager');  ?>
            </div>
            
            <div class="option allow" >
                <input name="wccs_settings[checkness][allow_file_upload]" placeholder="Enter Product ID(s); Example: 1674, 1423, 1234" type="text" value="<?php echo (empty($options['checkness']['allow_file_upload']) ) ? '' : $options['checkness']['allow_file_upload']; ?>" />
            </div>
	</div>
		


	<div class="section">
            <div class="info-of">
                <?php _e('Deny File Upload', 'woocommerce-checkout-manager');  ?>
            </div>
            
            <div class="option" >
                <input name="wccs_settings[checkness][deny_file_upload]" placeholder="Enter Product ID(s); Example: 1674, 1423, 1234" type="text" value="<?php echo (empty($options['checkness']['deny_file_upload'])) ? '' : $options['checkness']['deny_file_upload']; ?>" />
            </div>
	</div>



	<div class="section">
            <h3 class="heading">
                <?php _e('Categories', 'woocommerce-checkout-manager');  ?>
            </h3>

            <div class="info-of">
                <?php _e('Allow File Upload', 'woocommerce-checkout-manager');  ?>
            </div>

            <div class="option allow" >
                <input name="wccs_settings[checkness][allow_file_upload_cat]" placeholder="Enter Category Slug(s); Example: my-cat, flowers_in" type="text" value="<?php echo ( empty($options['checkness']['allow_file_upload_cat']) ) ? '' : $options['checkness']['allow_file_upload_cat']; ?>" />
            </div>
	</div>


	<div class="section">
            <div class="info-of">
                <?php _e('Deny File Upload', 'woocommerce-checkout-manager');  ?>
            </div>
            
            <div class="option" >
		        <input name="wccs_settings[checkness][deny_file_upload_cat]" placeholder="Enter Category Slug(s); Example: my-cat, flowers_in" type="text" value="<?php echo (empty( $options['checkness']['deny_file_upload_cat'])) ? '' : $options['checkness']['deny_file_upload_cat']; ?>" />
            </div>
	</div>
	
	
		<div class="section">
            <h3 class="heading">
                <?php _e('General Alerts', 'woocommerce-checkout-manager');  ?>
            </h3>

            <div class="info-of">
                <?php _e('Picture Editing Saved', 'woocommerce-checkout-manager');  ?>
            </div>

            <div class="option allow" >
                <input name="wccs_settings[checkness][picture_success]" placeholder="Picture Saved" type="text" value="<?php echo ( empty($options['checkness']['picture_success']) ) ? 'Picture Saved!' : $options['checkness']['picture_success']; ?>" />
            </div>
		</div>
	
		<div class="section">
            <div class="info-of">
                <?php _e('Deletion confirmation', 'woocommerce-checkout-manager');  ?>
            </div>
            
            <div class="option" >
		        <input name="wccs_settings[checkness][file_delete]" placeholder="Delete" type="text" value="<?php echo (empty( $options['checkness']['file_delete'])) ? 'Delete' : $options['checkness']['file_delete']; ?>" />
            </div>
	</div>
	
	
	<div class="section">
            <h3 class="heading">
                <?php _e('Restrictions', 'woocommerce-checkout-manager');  ?>
            </h3>

            <div class="info-of">
                <?php _e('File types', 'woocommerce-checkout-manager');  ?>
            </div>

            <div class="option allow" >
                <input name="wccs_settings[checkness][file_types]" placeholder="png,jpeg,gif" type="text" value="<?php echo ( empty($options['checkness']['file_types']) ) ? '' : $options['checkness']['file_types']; ?>" />
            </div>
			
			
			
			<div class="info-of">
                <?php _e('Number Of Files to Upload', 'woocommerce-checkout-manager');  ?>
            </div>
            
            <div class="option allow" >
		        <input name="wccs_settings[checkness][file_upload_number]" placeholder="4" type="text" value="<?php echo (empty( $options['checkness']['file_upload_number'])) ? '' : $options['checkness']['file_upload_number']; ?>" />
            </div>
	</div>
	
	<div class="section">
            <div class="info-of">
                <?php _e('Allow Upload for Order Status', 'woocommerce-checkout-manager');  ?>
            </div>
            
            <div class="option" >
		        <input name="wccs_settings[checkness][upload_os]" placeholder="completed" type="text" value="<?php echo (empty( $options['checkness']['upload_os'])) ? '' : $options['checkness']['upload_os']; ?>" />
            </div>
	</div>
	
	

</div>