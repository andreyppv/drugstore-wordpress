<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package Lattice
 * @since	1.0
 * @version	1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	lattice_post_thumbnail();

	if ( ! is_single() ) {
		the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	} // end if
	?>

	<?php if ( ! ( is_single() || is_singular() ) ) { ?>
		<div class="entry-meta">
			<?php
			if ( 'post' == get_post_type() ) {
				lattice_post_date();
			} // end if

			if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			?>
				<span class="comments-number"><?php lattice_comments_popup_link(); ?></span>
			<?php
			} // end if

			if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) ) {
			?>
				<span class="category-links"><?php echo get_the_category_list( ', ' ); ?></span>
			<?php
			} // end if

			edit_post_link( __( 'Edit', 'lattice' ), '<span class="edit-link">', '</span>' );
			?>
		</div><!-- /.entry-meta -->
	<?php } // end if ?>

	<?php if ( is_search() ) { ?>

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- /.entry-summary -->

	<?php } else { ?>

		<div class="entry-content">
			<?php
			the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'lattice' ) );

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
			?>
		</div><!-- /.entry-content -->

	<?php } // end if ?>

	<?php if ( get_the_tags() ) { ?>

		<footer class="entry-footer">
			<?php the_tags( '<i class="fa fa-tags"></i> <span class="tags">', ', ', '</span>' ); ?>
		</footer><!-- /.entry-footer -->

	<?php } // end if ?>
</article><!-- #post-<?php the_ID(); ?> -->