<?php
/**
 * Single Download Page Template.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Lattice
 * @since Lattice 1.0
 */
?>

<?php get_header(); ?>

	<?php the_post(); ?>

	<section class="single-title-block">
		<div class="inside">
			<h1 class="single-title"><?php the_title(); ?></h1>
			<?php
			if ( class_exists( 'EDD_Reviews' ) && get_comments_number( get_the_ID() ) ) {
				$average_rating = round( EDD_Reviews()->average_rating( false ) );

				echo '<div class="rating-info">';

					// Output filled stars
					for ( $i = 1; $i <= $average_rating; $i++ ) {
						echo '<i class="fa fa-star"></i>';
					} // end for

					// Output empty stars
					$empty_stars = 5 - $average_rating;
					for ( $i = 1; $i <= $empty_stars; $i++ ) {
						echo '<i class="fa fa-star-o"></i>';
					} // end for

					echo ' (' . get_comments_number( get_the_ID() ) . ' ' . __( 'reviews', 'lattice' ) . ')';

				echo '</div><!-- /.rating-info -->';
			} // end if
			?>
		</div><!-- /.inside -->
	</section><!-- /.single-title-block -->

	<?php rewind_posts(); ?>

	<section class="main clearfix">
		<div class="container clearfix">
			<section class="content">
				<?php do_action( 'lattice_download_before' ); ?>

				<?php
				// Start the Loop.
				while ( have_posts() ) {
					the_post(); ?>

					<?php do_action( 'lattice_download_start' ); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<?php the_post_thumbnail( 'lattice-content-single-download' ); ?>

							<?php do_action( 'lattice_download_content_before' ); ?>

							<?php the_content(); ?>

							<?php do_action( 'lattice_download_content_after' ); ?>

							<?php
							wp_link_pages( array(
								'before' => '<div class="pagination">' . __( 'Pages:', 'lattice' ),
								'after'  => '</div>',
							) );
							?>
						</article><!-- /#post-<?php the_ID(); ?> -->

					<?php do_action( 'lattice_download_end' ); ?>

					<?php do_action( 'lattice_comments_before' ); ?>

					<?php comments_template(); ?>

					<?php do_action( 'lattice_comments_after' ); ?>

				<?php } // end while ?>

				<?php do_action( 'lattice_download_after' ); ?>
			</section><!-- /.content -->

			<aside class="sidebar">
				<div id="download-details-<?php the_ID(); ?>" class="widget widget-product-details">
					<?php if ( edd_item_in_cart( $post->ID ) && ! edd_has_variable_prices( $post->ID ) ) { ?>
						<p class="already-in-cart"><?php echo sprintf( __( '%s already in cart.', 'lattice' ), ucwords( edd_get_label_singular() ) ); ?></p>
					<?php } // end if ?>

					<?php if ( ! edd_has_variable_prices( $post->ID ) ) { ?>
						<p class="download-price"><?php echo edd_currency_filter( edd_format_amount( edd_get_download_price( $post->ID ) ) ); ?></p>
					<?php } // end if ?>

					<?php lattice_purchase_link(); ?>
				</div>
				<?php dynamic_sidebar( 'single-download-sidebar' ); ?>
			</aside><!-- /.sidebar -->
		</div><!-- /.container -->
	</section><!-- /.main -->

<?php get_footer(); ?>