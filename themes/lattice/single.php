<?php
/**
 * The template for displaying all single posts
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Lattice
 * @since Lattice 1.0
 */
?>

<?php get_header(); ?>

	<?php get_template_part( 'content-header' ); ?>

	<section class="main clearfix">
		<div class="container clearfix">
			<section class="content">
				<?php do_action( 'lattice_post_before' ); ?>

				<?php
				// Start the Loop.
				while ( have_posts() ) {
					the_post();

					do_action( 'lattice_post_start' );

					/*
					 * Include the post format-specific template for the content. If you want to
					 * use this in a child theme, then include a file called called content-___.php
					 * (where ___ is the post format) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );

					do_action( 'lattice_post_end' );

					lattice_post_navigation();

					// If comments are open or we have at least one comment, load up the comments template.
					if ( comments_open() || get_comments_number() ) {
						do_action( 'lattice_comments_before' );
						comments_template();
						do_action( 'lattice_comments_after' );
					} // end if
				} // end while
				?>

				<?php do_action( 'lattice_post_after' ); ?>
			</section><!-- /.content -->

			<?php get_sidebar(); ?>
		</div><!-- /.container -->
	</section><!-- /.main -->

<?php get_footer(); ?>