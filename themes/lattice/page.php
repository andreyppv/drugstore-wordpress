<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Lattice
 * @since	1.0
 * @version	1.0
 */
?>

<?php get_header(); ?>

	<?php the_post(); ?>

	<section class="single-title-block">
		<div class="inside">
			<h1 class="single-title"><?php the_title(); ?></h1>
		</div><!-- /.inside -->
	</section><!-- /.single-title-block -->

	<?php rewind_posts(); ?>

	<section class="main clearfix">
		<div class="container clearfix">
			<section class="content">
				<?php
				// Start the Loop.
				while ( have_posts() ) {
					the_post();
				?>
					<article <?php post_class(); ?> id="post-<?php echo get_the_ID(); ?>">
						<?php the_content(); ?>
					</article><!-- /#post-<?php echo get_the_ID(); ?> -->
				<?php
				} // end while
				?>
			</section><!-- /.content -->

			<?php get_sidebar(); ?>
		</div><!-- /.container -->
	</section><!-- /.main -->

<?php get_footer(); ?>