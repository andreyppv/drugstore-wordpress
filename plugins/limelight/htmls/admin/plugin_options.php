<h3>Limelight Gateway</h3>
        
<table class="form-table">
    <?php $this->generate_settings_html(); ?>
        
    <?php if($this->is_available()) { ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label>Update Limelight Products</label>
            </th>
            <td class="forminp">
                <p><a href="<?php echo esc_url( add_query_arg( 'do_update_limelight_products', 'true', admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_smash_limelight_gateway' ) ) ); ?>" class="limelight-update-now button-primary"><?php _e( 'Update', 'woocommerce' ); ?></a></p>
                <p class="description">
                    Updating get all limelight products info from limelight crm. It will take some times. Please confirm before you update.
                </p>
                
                <?php //if(isset($_SESSION[SK_UPDATE_SUCCESS])) { ?>
                    <?php 
                        $info = get_latest_update_result(); 
                        if($info != null) { 
                            $fails_data = unserialize($info['fails_data']);
                        ?>
                            <div>
                                <h3>Latest Update Result</h3>
                                <p>Time: <?php echo $info['time']; ?></p>
                                <p>Total Products: <?php echo $info['total']; ?></p>
                                <p>Total Limelight Products: <?php echo $info['total_limelight']; ?></p>
                                <p>Updated Limelight Products: <?php echo $info['total_success']; ?></p>
                                <p>Failed Limelight Products: <?php echo count($fails_data); ?></p>
                                
                                <?php 
                                foreach($fails_data as $item) { 
                                    $product_id = $item['product_id'];
                                    $product_info = get_post_meta($product_id, '_product_info', true);
                                    $shipping_info = get_post_meta($product_id, '_shipping_info', true);
                                ?>
                                    &nbsp;- <a href="<?php echo admin_url("post.php?post={$item['product_id']}&action=edit"); ?>" target="_blank"><?php echo $item['product_name']; ?></a> - 
                                    <?php 
                                    if($item['limelight_product_id'] > 0) {
                                        echo 'Invalid product id(' . $item['limelight_product_id'] . ') ';
                                        echo ($product_info != null) ? '(Fixed)' : '';
                                    }
                                    ?>&nbsp;
                                    
                                    <?php 
                                    if($item['limelight_shipping_id'] > 0) {
                                        echo 'Invalid shipping id(' . $item['limelight_shipping_id'] . ') ';
                                        echo ($shipping_info != null) ? '(Fixed)' : '';
                                    }
                                    ?>
                                    <br>
                                    
                                <?php 
                                } 
                                ?>
                                <br/>
                                <b>***(Fixed) : Updated product successfully mannually</b>
                            </div>
                        <?php } ?>
                    <?php unset($_SESSION[SK_UPDATE_SUCCESS]);
                //} ?>
            </td>
        </tr>
    <?php } ?>
</table>

<script type="text/javascript">
jQuery( '.limelight-update-now' ).click( 'click', function() {
    return window.confirm( '<?php echo esc_js( __( 'It will get limelight product info from limelight crm. It will take some times. Are you sure you wish to run the updater now?', 'limelight' ) ); ?>' );
});
</script>