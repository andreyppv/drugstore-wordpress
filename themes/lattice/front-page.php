<?php
/**
 * The homepage template file.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Lattice
 * @since	1.0
 * @version	1.0
 */
?>
<?php get_header(); ?>

	<section class="intro clearfix">
		<div class="inside">
			<h1 class="page-title"><?php echo get_theme_mod( 'lattice_homepage_text', __( 'Welcome To Our Store', 'lattice' ) ) ?></h1>
		</div><!-- /.inside -->
	</section><!-- /.intro -->

	<section class="main clearfix">
		<section class="downloads-container clearfix">
			<div class="inside clearfix">
				<?php
				$args = array(
					'post_type' => 'download',
					'posts_per_page' => 9
				);

				$downloads = new WP_Query( $args );

				if ( $downloads->have_posts() ) {
					$c = 0;
					// Start the Loop.
					while ( $downloads->have_posts() ) {
						$downloads->the_post(); $c++;

						if ( 0 == $c % 3 ) {
							$download_clear = 'download-clear clearfix';
						} else {
							$download_clear = '';
						} // end if
						?>
						<article <?php post_class( $download_clear ); ?> id="post-<?php the_ID(); ?>">
							<div class="download-image">
								<?php
								if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ) {
									the_post_thumbnail( 'lattice-download-grid' );
								} // end if
								?>
								<div class="overlay">
									<?php lattice_purchase_link(); ?>
									<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf( __( 'Permanent Link to %s', 'lattice' ), the_title_attribute( 'echo=0' ) ); ?>"><?php _e( 'More Info', 'lattice' ); ?></a>
								</div><!-- /.overlay -->
							</div><!-- /.entry-image -->

							<div class="download-title">
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							</div><!-- /.download-title -->
						</article><!-- /.download -->
					<?php
					} // end while
				} // end if
				?>
			</div><!-- /.inside -->

			<?php if ( $downloads->post_count >= 9 ) { ?>
				<div class="view-more-button-div">
					<p class="view-more-button clearfix"><a href="<?php echo get_post_type_archive_link( 'download' ); ?>"><?php _e( 'View More', 'lattice' ); ?></a></p>
				</div><!-- /.view-more-button-div -->
			<?php } // end if ?>
		</section><!-- /.downloads-container -->
	</section><!-- /.main -->

<?php get_footer(); ?>