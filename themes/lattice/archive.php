<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Lattice
 * @since	1.0
 * @version	1.0
 */
?>

<?php get_header(); ?>

	<section class="single-title-block">
		<div class="inside">
			<h1 class="single-title">
				<?php
				if ( is_day() ) {
					printf( __( 'Daily Archives: %s', 'lattice' ), get_the_date() );
				} elseif ( is_month() ) {
					printf( __( 'Monthly Archives: %s', 'lattice' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'lattice' ) ) );
				} elseif ( is_year() ) {
					printf( __( 'Yearly Archives: %s', 'lattice' ), get_the_date( _x( 'Y', 'yearly archives date format', 'lattice' ) ) );
				} else {
					_e( 'Archives', 'lattice' );
				} // end if
				?>
			</h1>
		</div><!-- /.inside -->
	</section><!-- /.single-title-block -->

	<section class="main clearfix">
		<div class="container clearfix">
			<section class="content">
				<?php
				if ( have_posts() ) {
					// Start the Loop.
					while ( have_posts() ) {
						the_post();

						/*
						 * Include the post format-specific template for the content. If you want to
						 * use this in a child theme, then include a file called called content-___.php
						 * (where ___ is the post format) and that will be used instead.
						 */
						get_template_part( 'content', get_post_format() );

					} // end while

					lattice_page_navigation();
				} else {
					// If no content, include the "No posts found" template.
					get_template_part( 'content', 'none' );
				} // end if
				?>
			</section><!-- /.content -->

			<?php get_sidebar(); ?>
		</div><!-- /.container -->
	</section><!-- /.main -->

<?php get_footer(); ?>