<?php
/**
 * The template for displaying 404 pages (Not Found)
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
			<h1 class="single-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'lattice' ); ?></h1>
		</div><!-- /.inside -->
	</section><!-- /.single-title-block -->

	<section class="main clearfix">
		<div class="container clearfix">
			<section class="content">
				<article class="post" id="post-0">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'lattice' ); ?></p>

					<?php get_search_form(); ?>

					<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>
				</article><!-- /.post -->
			</section><!-- /.content -->

			<?php get_sidebar(); ?>
		</div><!-- /.container -->
	</section><!-- /.main -->

<?php get_footer(); ?>