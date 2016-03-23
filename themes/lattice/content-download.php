<?php
/**
 * The template for displaying downloads.
 *
 * Used on the downloads archive.
 *
 * @package Lattice
 * @since	1.0
 * @version	1.0
 */
?>

<div itemscope itemtype="http://schema.org/Product" class="edd-download" id="edd_download_<?php echo get_the_ID(); ?>" style="float: left;">
	<div class="edd_download_inner">
		<?php do_action( 'edd_download_before' ); ?>

		<?php
		edd_get_template_part( 'shortcode', 'content-image' );
		edd_get_template_part( 'shortcode', 'content-title' );
		edd_get_template_part( 'shortcode', 'content-excerpt' );
		?>

		<div class="edd_download_buy_button">
			<?php echo edd_get_purchase_link( array( 'download_id' => get_the_ID(), 'price' => false ) ); ?>
		</div><!-- /.edd_download_buy_button -->

		<?php do_action( 'edd_download_after' ); ?>
	</div><!-- /.edd_download_inner -->
</div><!-- /.edd_download -->