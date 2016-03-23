<p class="">
    <input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" style="float:none;"/>
    <label for="terms" class="checkbox" style="display:inline;"><?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'woocommerce' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?> <span class="required">*</span></label>
    <input type="hidden" name="terms-field" value="1" />
</p>
<style>
#payment .form-row.place-order {float:none !important;}
</style>