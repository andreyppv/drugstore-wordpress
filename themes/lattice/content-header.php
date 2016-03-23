<?php
/**
 * Content header which shows up on single posts (title and post meta)
 *
 * @package Lattice
 * @since	1.0
 * @version	1.0
 */
?>

<?php the_post(); ?>

<section class="single-title-block">
	<div class="inside">
		<h1 class="single-title"><?php the_title(); ?></h1>
		<p class="entry-date"><span><?php the_date(); ?></span></p>

		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) ) : ?>
			<p class="categories"><span class="category-links"><?php echo get_the_category_list( ' ' ); ?></span></p>
		<?php endif; ?>
	</div><!-- /.inside -->
</section><!-- /.single-title-block -->

<?php rewind_posts(); ?>