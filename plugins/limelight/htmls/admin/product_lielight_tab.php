<div id="limelight_fields" class="panel woocommerce_options_panel">
    <?php
        woocommerce_wp_hidden_input( array( 
            'id' => '_limelight_product_id_changed', 
            'value' => 0,
        ) );
        woocommerce_wp_hidden_input( array( 
            'id' => '_limelight_shipping_id_changed', 
            'value' => 0,
        ) );
    ?>
    
    <div class="options_group">
        <?php
            // product_id
            woocommerce_wp_text_input( array( 
                'id' => '_limelight_product_id', 
                'label' => __( 'Product ID', 'woocommerce' ), 
                'desc_tip' => 'true', 
                'description' => __( 'Limelight Product ID', 'woocommerce' ), 
                'value' => intval( $post->_limelight_product_id ),  //esc_attr( get_post_meta( $thepostid, '_sku', true ) ) 
                'type' => 'text', 
                //'custom_attributes' => array('step' => '1')  
            ) );
        ?>
    </div>
    
    <div class="options_group">
        <?php
            // campaign_id
            woocommerce_wp_text_input( array( 
                'id' => '_limelight_campaign_id', 
                'label' => __( 'Campaign ID', 'woocommerce' ), 
                'desc_tip' => 'true', 
                'description' => __( 'Limelight Campaign ID', 'woocommerce' ), 
                'value' => intval( $post->_limelight_campaign_id ), 
                'type' => 'text', 
                //'custom_attributes' => array('step' => '1')  
            ) );
        ?>
    </div>
    
    <div class="options_group">
        <?php
            // shipping_id
            woocommerce_wp_text_input( array( 
                'id' => '_limelight_shipping_id', 
                'label' => __( 'Shipping ID', 'woocommerce' ), 
                'desc_tip' => 'true', 
                'description' => __( 'Limelight Shipping ID', 'woocommerce' ), 
                'value' => intval( $post->_limelight_shipping_id ), 
                'type' => 'text', 
                //'custom_attributes' => array('step' => '1')  
            ) );
        ?>
    </div>
    
    <div class="options_group">
        <?php
            // shipping_id
            woocommerce_wp_text_input( array( 
                'id' => '_limelight_gateway_id', 
                'label' => __( 'Gateway ID', 'woocommerce' ), 
                'desc_tip' => 'true', 
                'description' => __( 'Limelight Gateway ID', 'woocommerce' ), 
                'value' => intval( $post->_limelight_gateway_id ), 
                'type' => 'text', 
                //'custom_attributes' => array('step' => '1')  
            ) );
        ?>
    </div>
    
    <div class="options_group">
        <?php
            // terms
            $term_desc = '
                <br/>Limelight Terms<br/>
                Available shortcodes: </br>
                Product Name        -> [limelight_product_name], </br>
                Product Price       -> [limelight_product_price], </br>
                Product Rebill Price-> [limelight_rebill_price], </br>
                Shipping Name       -> [limelight_shipping_name], </br>
                Shipping Price      -> [limelight_shipping_price], </br>
                Support Phone       -> [xyz-ihs snippet="phone"], </br>
                Support Email       -> [xyz-ihs snippet="support-email"]
            ';
            
            woocommerce_wp_textarea_input( array( 
                'id' => '_limelight_terms', 
                'label' => __( 'Custom Terms', 'woocommerce' ), 
                'desc_tip' => false, 
                'description' => __( $term_desc, 'woocommerce' ), 
                'value' => html_entity_decode($post->_limelight_terms), 
                'type' => 'textarea', 
                //'custom_attributes' => array('step' => '1')  
            ) );
        ?>
    </div>
    
    <div class="options_group">
        <?php
            // enable terms checkbox
            woocommerce_wp_checkbox( array( 
                'id' => '_limelight_terms_checkbox', 
                'label' => __( 'Enable Terms Checkbox', 'woocommerce' ), 
                'desc_tip' => true, 
                'description' => __( 'Show checkbox for each product page', 'woocommerce' ), 
                'type' => 'text', 
                //'custom_attributes' => array('step' => '1')  
            ) );
        ?>
    </div>
</div>

<script>
jQuery('#_limelight_product_id').change(function() {
    jQuery('#_limelight_product_id_changed').val(1);
});
jQuery('#_limelight_shipping_id').change(function() {
    jQuery('#_limelight_shipping_id_changed').val(1);
});
</script>
