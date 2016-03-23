<?php if ( $this->description ) { ?>
    <p><?php echo $this->description; ?></p>
<?php } ?>

<fieldset>
    <!-- Show input boxes for new data -->
    <div id="smash-credit-info" class="col2-set">
        <div class="col-1">
            <!-- Credit card number -->        
            <p class="form-row form-row-wide">
                <label for="ccnum"><?php echo __( 'Credit Card number', 'woocommerce' ) ?> <span class="required">*</span></label>
                <input type="text" class="input-text" id="ccnum" name="ccnum" maxlength="16" />
            </p>
            
            <!-- Credit card expiration -->
            <p class="form-row form-row-first">
                <label for="cc-expire-month"><?php echo __( 'Expiration date', 'woocommerce') ?> <span class="required">*</span></label>
                <select name="expmonth" id="expmonth" class="woocommerce-select woocommerce-cc-month select2">
                    <option value=""><?php _e( 'Month', 'woocommerce' ) ?></option>
                    <?php
                    $months = array();
                    for ( $i = 1; $i <= 12; $i ++ ) {
                        $timestamp = mktime( 0, 0, 0, $i, 1 );
                        $months[ date( 'n', $timestamp ) ] = date( 'F', $timestamp );
                    }
                    
                    foreach ( $months as $num => $name ) {
                        printf( '<option value="%u">%s</option>', $num, $name );
                    } 
                    ?>
                </select>
            </p>
            <p class="form-row form-row-last">
                <label for="">&nbsp;</label>
                <select name="expyear" id="expyear" class="woocommerce-select woocommerce-cc-year select2">
                    <option value=""><?php _e( 'Year', 'woocommerce' ) ?></option><?php
                    $years = array();
                    for ( $i = date( 'y' ); $i <= date( 'y' ) + 15; $i ++ ) {
                        printf( '<option value="20%u">20%u</option>', $i, $i );
                    } ?>
                </select>
            </p>
        </div>
        <div class="col-2">
            <!-- Credit card type -->
            <p class="form-row form-row-wide">
                <label for="cardtype"><?php echo __( 'Card type', 'woocommerce' ) ?> <span class="required">*</span></label>
                <select name="cardtype" id="cardtype" class="woocommerce-select select2">
                    <?php
                        $cardtypes = array(
                            'MasterCard'    => 'MasterCard',
                            'Visa'          => 'Visa',
                            'Discover'      => 'Discover',
                            'American Express' => 'American Express'
                        );
                    ?>
                    <?php  //foreach( $this->cardtypes as $type ) { ?>
                    <?php  foreach( $cardtypes as $type ) { ?>
                    <option value="<?php echo $type ?>"><?php _e( $type, 'woocommerce' ); ?></option>
                    <?php } ?>
                </select>
            </p>
            
            <!--Credit card security code-->    
            <p class="form-row form-row-first">
                <label for="cvv"><?php _e( 'Card security code', 'woocommerce' ) ?> <span class="required">*</span></label>
                <input oninput="validate_cvv(this.value)" type="text" class="input-text" id="cvv" name="cvv" maxlength="4" style="width:45px" />
                <span class="help"><?php _e( '3 or 4 digits', 'woocommerce' ) ?></span>
              </p>
        </div>
    </div>
</fieldset>
<style>
    .select2-search input {width:97%;}
</style>

<?php //if ( $this->saveinfo == 'yes' && ! ( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) ) { ?>
    <!--<p>
        <label for="saveinfo"><?php _e( 'Save this billing method?', 'woocommerce' ) ?></label>
        <input type="checkbox" class="input-checkbox" id="saveinfo" name="saveinfo" />
        <span class="help"><?php _e( 'Select to store your billing information for future use.', 'woocommerce' ) ?></span>
    </p>-->
<?php //} ?>