<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <section class="main">
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Lattice
 * @since	1.0
 * @version	1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<div id="container" class="hfeed site">
		<?php do_action( 'lattice_before' ); ?>

		<header class="header clearfix" role="banner">
			<div class="inside">
				<div class="top">
					<div class="shopping-cart">
						<a href="#" class="shopping-cart-trigger"><i class="fa fa-shopping-cart"></i> <span class="edd-cart-quantity"><?php echo edd_get_cart_quantity(); ?></span></a>
					</div><!-- /.shopping-cart -->

					<?php get_search_form(); ?>
				</div><!-- /.top -->

				<div class="left">
					<?php if ( get_header_image() ) { ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<img width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" src="<?php header_image(); ?>" alt="">
						</a>
					<?php } // end if ?>

					<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>

					<i class="fa mobile-menu-toggle fa-bars"></i>
				</div><!-- /.left -->

				<div class="right">
					<div class="search-toggle">
						<a href="#search-container" class="screen-reader-text"><?php _e( 'Search', 'lattice' ); ?></a>
					</div><!-- /.search-toggle -->

					<nav id="primary" class="navigation-main" role="navigation">
						<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
					</nav><!-- /#primary -->
				</div><!-- /.right -->
			</div><!-- /.inside -->
		</header><!-- /.header -->