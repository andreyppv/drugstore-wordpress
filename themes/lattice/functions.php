<?php
/**
 * Lattice Functions
 *
 * This file adds all the core features to the Lattice theme.
 *
 * @package Lattice
 * @since   1.0
 * @version 1.0
 */

/* ------------------------------------------------------------------------------- *
 *
 * /\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ CONTENTS /\/\/\/\/\/\/\/\/\/\/\/\/\/\//\/\/\/\
 *
 * 1.  Theme Setup
 * 2.  Includes
 * 3.  Stylesheets and JavaScript Files
 * 4.  Comments Template
 * 5.  Features/Template Functions
 * 6.  Custom Actions/Filters
 * 7.  Widgets
 * 8.  Misc
 * 9.  Customizer
 * 10. Software Licensing Integration
 *
 * ------------------------------------------------------------------------------- */

/**
 * Define theme version. This is used for cache-busting stylesheets,
 * JavaScript, and for serializing the version in the database.
 *
 * @since   1.0
 */
define( 'LATTICE_THEME_VERSION', '1.1.4' );

/* ----------------------------------------------------------- *
 * 1. Theme Setup
 * ----------------------------------------------------------- */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * This has to be done outside of a function for it to perform correctly for Jetpack.
 *
 * @since   1.0
 * @version 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 680; /* pixels */
} // end if

if ( ! function_exists( 'lattice_theme_setup' ) ) {
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_theme_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 */
	load_theme_textdomain( 'lattice', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// This theme styles the visual editor to resemble the theme style.
	add_editor_style( array( 'css/editor-style.css', lattice_font_url() ) );

	/**
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'lattice_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => ''
	) ) );

	// Register custom editor styleshset
	add_editor_style( 'css/editor-style.css' );

	// Register custom image sizes
	add_image_size( 'lattice-download-grid', 280, 210, true );
	add_image_size( 'lattice-content-single-download', 680, 9999, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'lattice' ),
	) );
} // end lattice_theme_setup
} // end if
add_action( 'after_setup_theme', 'lattice_theme_setup' );

/* ----------------------------------------------------------- *
 * 2. Includes
 * ----------------------------------------------------------- */

/**
 * Load EDD Theme Updater Class
 *
 * @since   1.0
 * @version 1.0
 */
if ( ! class_exists( 'EDD_SL_Theme_Updater' ) ) {
	require_once( dirname( __FILE__ ) . '/inc/class-edd-sl-theme-updater.php' );
} // end if

/* ----------------------------------------------------------- *
 * 3. Stylesheets and JavaScript Files
 * ----------------------------------------------------------- */

/**
 * Register and enqueue scripts and styles
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_register_theme_scripts() {
	global $wp_styles;

	$deps = array( 'lato-font' );

	wp_register_style( 'lato-font', '//fonts.googleapis.com/css?family=Lato:300,400,700' );
	wp_register_style( 'lattice-style', get_stylesheet_uri(), $deps, '1.0' );

	$path = EDD_PLUGIN_URL . '/templates';

	$edd_fonts_css = <<<CSS
		@font-face {
			font-family: 'padlock';
			src: url('$path/fonts/padlock.eot?64116112');
			src: url('$path/fonts/padlock.eot?64116112#iefix') format('embedded-opentype'), url('$path/fonts/padlock.svg?64116112#padlock') format('svg');
			font-weight: normal;
			font-style: normal;
		}

		@font-face {
			font-family: 'padlock';
			src: url('data:application/octet-stream;base64,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") format('woff'), url("data:application/octet-stream;base64,AAEAAAAPAIAAAwBwRkZUTWKUVToAAAD8AAAAHE9TLzJWNGNEAAABGAAAAFZjbWFwAB/uvgAAAXAAAAFmY3Z0IAZD/54AAAbkAAAAHGZwZ20w9Z6VAAAHAAAACZZnYXNwAAAAEAAABtwAAAAIZ2x5ZmgZgQoAAALYAAAA6mhlYWT8juf9AAADxAAAADZoaGVhBi8ClQAAA/wAAAAkaG10eAiOADAAAAQgAAAAEGxvY2EAnQBQAAAEMAAAAAptYXhwAL8J2AAABDwAAAAgbmFtZdCrDhsAAARcAAACTHBvc3Rj0jvbAAAGqAAAADFwcmVwuL3ioQAAEJgAAABYAAAAAQAAAADH/rDfAAAAAM1K0i0AAAAAzUrSLQABAtoB9AAFAAACigK7AAAAjAKKArsAAAHfADEBAgAAAgAGCQAAAAAAAAAAAAACAAAAAAAAAAAAAABQZkVkAED/////A1L/agBaAwgASgAAAAEAAAAAAAAAAAAFAAAAAwAAACwAAAAEAAAARAABAAAAAABgAAMAAQAAACwAAwAKAAAARAAEABgAAAACAAIAAAAA//8AAP//AAEAAAAMAAAAAAAcAAAAAAAAAAEAAfUSAAH1EgAAAAMAAAEGAAABAAAAAAAAAAECAAAAAgAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgAhAAABKgKaAAMABwApQCYAAAADAgADVwACAQECSwACAgFPBAEBAgFDAAAHBgUEAAMAAxEFDyszESERJzMRIyEBCejHxwKa/WYhAlgAAAACAA//tgLLAwgAGwAjAC1AKgAFBQNRAAMDCkEEAgYDAAABUQABAQsBQgEAIiEeHRgXFBILCAAbARsHDisBMhYVERQPAQYjISIvASY1ETQ2OwE1NDYyFh0BJRUzNTQmIgYCjxQoMDwpN/7eOSk8MB4UZGXGZf7UyDVeNQHcJxX+ejASEhAQEhIwAYYWJkZsenpsRlpaWjI8PAAAAAABAAAAAQAAGVR5JF8PPPUACwPoAAAAAM1K0i0AAAAAzUrSLQAP/7YCywMIAAAACAACAAAAAAAAAAEAAAMI/7YAWgLaAAAAAALLAAEAAAAAAAAAAAAAAAAAAAAEAtoAIQAAAAAC2gAAAtoADwAAACgAKAAoAHUAAAABAAAABAAkAAIAAAAAAAIADgAcAGwAAAA+CZYAAAAAAAAADgCuAAEAAAAAAAAANQBsAAEAAAAAAAEABwCyAAEAAAAAAAIABgDIAAEAAAAAAAMAIwEXAAEAAAAAAAQABwFLAAEAAAAAAAUAEAF1AAEAAAAAAAYABwGWAAMAAQQJAAAAagAAAAMAAQQJAAEADgCiAAMAAQQJAAIADAC6AAMAAQQJAAMARgDPAAMAAQQJAAQADgE7AAMAAQQJAAUAIAFTAAMAAQQJAAYADgGGAEMAbwBwAHkAcgBpAGcAaAB0ACAAKABDACkAIAAyADAAMQAyACAAYgB5ACAAbwByAGkAZwBpAG4AYQBsACAAYQB1AHQAaABvAHIAcwAgAEAAIABmAG8AbgB0AGUAbABsAG8ALgBjAG8AbQAAQ29weXJpZ2h0IChDKSAyMDEyIGJ5IG9yaWdpbmFsIGF1dGhvcnMgQCBmb250ZWxsby5jb20AAHAAYQBkAGwAbwBjAGsAAHBhZGxvY2sAAE0AZQBkAGkAdQBtAABNZWRpdW0AAEYAbwBuAHQARgBvAHIAZwBlACAAMgAuADAAIAA6ACAAcABhAGQAbABvAGMAawAgADoAIAAyADAALQAyAC0AMgAwADEAMwAARm9udEZvcmdlIDIuMCA6IHBhZGxvY2sgOiAyMC0yLTIwMTMAAHAAYQBkAGwAbwBjAGsAAHBhZGxvY2sAAFYAZQByAHMAaQBvAG4AIAAwADAAMQAuADAAMAAwACAAAFZlcnNpb24gMDAxLjAwMCAAAHAAYQBkAGwAbwBjAGsAAHBhZGxvY2sAAAIAAAAAAAD/gwAyAAAAAQAAAAAAAAAAAAAAAAAAAAAABAAAAAEAAgECBnUxRjUxMgAAAAABAAH//wAPAAAAAAAAAAAAAAAAAAAAAAAyADIDCP+2Awj/trAALLAgYGYtsAEsIGQgsMBQsAQmWrAERVtYISMhG4pYILBQUFghsEBZGyCwOFBYIbA4WVkgsApFYWSwKFBYIbAKRSCwMFBYIbAwWRsgsMBQWCBmIIqKYSCwClBYYBsgsCBQWCGwCmAbILA2UFghsDZgG2BZWVkbsAArWVkjsABQWGVZWS2wAiwgRSCwBCVhZCCwBUNQWLAFI0KwBiNCGyEhWbABYC2wAywjISMhIGSxBWJCILAGI0KyCgACKiEgsAZDIIogirAAK7EwBSWKUVhgUBthUllYI1khILBAU1iwACsbIbBAWSOwAFBYZVktsAQssAgjQrAHI0KwACNCsABDsAdDUViwCEMrsgABAENgQrAWZRxZLbAFLLAAQyBFILACRWOwAUViYEQtsAYssABDIEUgsAArI7ECBCVgIEWKI2EgZCCwIFBYIbAAG7AwUFiwIBuwQFlZI7AAUFhlWbADJSNhREQtsAcssQUFRbABYUQtsAgssAFgICCwCkNKsABQWCCwCiNCWbALQ0qwAFJYILALI0JZLbAJLCC4BABiILgEAGOKI2GwDENgIIpgILAMI0IjLbAKLEtUWLEHAURZJLANZSN4LbALLEtRWEtTWLEHAURZGyFZJLATZSN4LbAMLLEADUNVWLENDUOwAWFCsAkrWbAAQ7ACJUKyAAEAQ2BCsQoCJUKxCwIlQrABFiMgsAMlUFiwAEOwBCVCioogiiNhsAgqISOwAWEgiiNhsAgqIRuwAEOwAiVCsAIlYbAIKiFZsApDR7ALQ0dgsIBiILACRWOwAUViYLEAABMjRLABQ7AAPrIBAQFDYEItsA0ssQAFRVRYALANI0IgYLABYbUODgEADABCQopgsQwEK7BrKxsiWS2wDiyxAA0rLbAPLLEBDSstsBAssQINKy2wESyxAw0rLbASLLEEDSstsBMssQUNKy2wFCyxBg0rLbAVLLEHDSstsBYssQgNKy2wFyyxCQ0rLbAYLLAHK7EABUVUWACwDSNCIGCwAWG1Dg4BAAwAQkKKYLEMBCuwaysbIlktsBkssQAYKy2wGiyxARgrLbAbLLECGCstsBwssQMYKy2wHSyxBBgrLbAeLLEFGCstsB8ssQYYKy2wICyxBxgrLbAhLLEIGCstsCIssQkYKy2wIywgYLAOYCBDI7ABYEOwAiWwAiVRWCMgPLABYCOwEmUcGyEhWS2wJCywIyuwIyotsCUsICBHICCwAkVjsAFFYmAjYTgjIIpVWCBHICCwAkVjsAFFYmAjYTgbIVktsCYssQAFRVRYALABFrAlKrABFTAbIlktsCcssAcrsQAFRVRYALABFrAlKrABFTAbIlktsCgsIDWwAWAtsCksALADRWOwAUVisAArsAJFY7ABRWKwACuwABa0AAAAAABEPiM4sSgBFSotsCosIDwgRyCwAkVjsAFFYmCwAENhOC2wKywuFzwtsCwsIDwgRyCwAkVjsAFFYmCwAENhsAFDYzgtsC0ssQIAFiUgLiBHsAAjQrACJUmKikcjRyNhIFhiGyFZsAEjQrIsAQEVFCotsC4ssAAWsAQlsAQlRyNHI2GwBkUrZYouIyAgPIo4LbAvLLAAFrAEJbAEJSAuRyNHI2EgsAQjQrAGRSsgsGBQWCCwQFFYswIgAyAbswImAxpZQkIjILAJQyCKI0cjRyNhI0ZgsARDsIBiYCCwACsgiophILACQ2BkI7ADQ2FkUFiwAkNhG7ADQ2BZsAMlsIBiYSMgILAEJiNGYTgbI7AJQ0awAiWwCUNHI0cjYWAgsARDsIBiYCMgsAArI7AEQ2CwACuwBSVhsAUlsIBisAQmYSCwBCVgZCOwAyVgZFBYIRsjIVkjICCwBCYjRmE4WS2wMCywABYgICCwBSYgLkcjRyNhIzw4LbAxLLAAFiCwCSNCICAgRiNHsAArI2E4LbAyLLAAFrADJbACJUcjRyNhsABUWC4gPCMhG7ACJbACJUcjRyNhILAFJbAEJUcjRyNhsAYlsAUlSbACJWGwAUVjIyBYYhshWWOwAUViYCMuIyAgPIo4IyFZLbAzLLAAFiCwCUMgLkcjRyNhIGCwIGBmsIBiIyAgPIo4LbA0LCMgLkawAiVGUlggPFkusSQBFCstsDUsIyAuRrACJUZQWCA8WS6xJAEUKy2wNiwjIC5GsAIlRlJYIDxZIyAuRrACJUZQWCA8WS6xJAEUKy2wNyywLisjIC5GsAIlRlJYIDxZLrEkARQrLbA4LLAvK4ogIDywBCNCijgjIC5GsAIlRlJYIDxZLrEkARQrsARDLrAkKy2wOSywABawBCWwBCYgLkcjRyNhsAZFKyMgPCAuIzixJAEUKy2wOiyxCQQlQrAAFrAEJbAEJSAuRyNHI2EgsAQjQrAGRSsgsGBQWCCwQFFYswIgAyAbswImAxpZQkIjIEewBEOwgGJgILAAKyCKimEgsAJDYGQjsANDYWRQWLACQ2EbsANDYFmwAyWwgGJhsAIlRmE4IyA8IzgbISAgRiNHsAArI2E4IVmxJAEUKy2wOyywLisusSQBFCstsDwssC8rISMgIDywBCNCIzixJAEUK7AEQy6wJCstsD0ssAAVIEewACNCsgABARUUEy6wKiotsD4ssAAVIEewACNCsgABARUUEy6wKiotsD8ssQABFBOwKyotsEAssC0qLbBBLLAAFkUjIC4gRoojYTixJAEUKy2wQiywCSNCsEErLbBDLLIAADorLbBELLIAATorLbBFLLIBADorLbBGLLIBATorLbBHLLIAADsrLbBILLIAATsrLbBJLLIBADsrLbBKLLIBATsrLbBLLLIAADcrLbBMLLIAATcrLbBNLLIBADcrLbBOLLIBATcrLbBPLLIAADkrLbBQLLIAATkrLbBRLLIBADkrLbBSLLIBATkrLbBTLLIAADwrLbBULLIAATwrLbBVLLIBADwrLbBWLLIBATwrLbBXLLIAADgrLbBYLLIAATgrLbBZLLIBADgrLbBaLLIBATgrLbBbLLAwKy6xJAEUKy2wXCywMCuwNCstsF0ssDArsDUrLbBeLLAAFrAwK7A2Ky2wXyywMSsusSQBFCstsGAssDErsDQrLbBhLLAxK7A1Ky2wYiywMSuwNistsGMssDIrLrEkARQrLbBkLLAyK7A0Ky2wZSywMiuwNSstsGYssDIrsDYrLbBnLLAzKy6xJAEUKy2waCywMyuwNCstsGkssDMrsDUrLbBqLLAzK7A2Ky2waywrsAhlsAMkUHiwARUwLQAAS7gAyFJYsQEBjlm5CAAIAGMgsAEjRCCwAyNwsgQoCUVSRLMKCwYEK7EGAUSxJAGIUViwQIhYsQYDRLEmAYhRWLgEAIhYsQYBRFlZWVm4Af+FsASNsQUARA==') format('truetype');
		}

		@font-face {
			font-family: 'EDD-Icons';
			src:url('$path/fonts/EDD-Icons.eot');
			src:url('$path/fonts/EDD-Icons.eot?#iefix') format('embedded-opentype'), url('$path/fonts/EDD-Icons.woff') format('woff'), url('$path/fonts/EDD-Icons.ttf') format('truetype'), url('$path/fonts/EDD-Icons.svg#EDD-Icons') format('svg');
			font-weight: normal;
			font-style: normal;
		}

		#edd_checkout_form_wrap input.edd-input.card-number.valid {
			background-image: url('$path/images/tick.png');
		}
CSS;

	wp_enqueue_style( 'lato-font'     );
	wp_enqueue_style( 'lattice-style' );
	wp_add_inline_style( 'lattice-style', $edd_fonts_css );

	wp_dequeue_style( 'edd-styles' );

	wp_register_script( 'lattice-js', get_template_directory_uri() . '/js/lattice.js', array( 'jquery' ), '1.0', false );

	wp_enqueue_script( 'jquery'     );
	wp_enqueue_script( 'lattice-js' );

	wp_localize_script( 'lattice-js', 'lattice_vars', array(
		'purchase_text' => __( 'Purchase Options', 'lattice' )
	) );

	array_push( $wp_styles->registered, $wp_styles->registered['lattice-style'] );
} // end lattice_register_theme_scripts
add_action( 'wp_enqueue_scripts', 'lattice_register_theme_scripts' );

/* ----------------------------------------------------------- *
 * 4. Comments Templates
 * ----------------------------------------------------------- */

/**
 * Template for comments.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @global  $post    WordPress Post Object
 * @global  $comment WordPress Comment Object
 * @since   1.0
 * @version 1.0
 */
function lattice_comment( $comment, $args, $depth ) {
	global $post;

	$GLOBALS['comment'] = $comment; ?>

	<li <?php comment_class( 'clearfix' ); ?> id="comment-<?php comment_ID(); ?>">
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<div class="comment-author vcard">
				<?php echo get_avatar( $comment, $args['avatar_size'] );?>
				<cite class="fn"><?php echo get_comment_author_link(); ?></cite>

				<?php if ( class_exists( 'EDD_Reviews' ) && 'download' == get_post_type( $post->ID ) && edd_has_user_purchased( $comment->user_id, $post->ID ) ) {  ?>
					<div class="tooltip top">
						<div class="tooltip-arrow"></div>
						<div class="tooltip-inner">
							<?php echo sprintf( apply_filters( 'lattice_verified_buyer_text', '%s' ), __( 'Verified Buyer', 'lattice' ) ); ?>
						</div>
					</div><!-- /.tooltip -->
					<i class="fa fa-check-circle-o"></i>
				<?php } // end if ?>
			</div><!-- /.comment-author -->

			<div class="comment-meta commentmetadata">
				<a class="comment-date" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php printf( __( '%1$s at %2$s' ), get_comment_date(), get_comment_time() ); ?></a>
				<?php edit_comment_link( __( '(Edit)', 'lattice' ), '' ); ?>
			</div><!-- /.comment-meta -->

			<?php if ( class_exists( 'EDD_Reviews' ) && 'download' == get_post_type( $post->ID ) ) { ?>
			<div class="review-meta clearfix">
				<?php $rating = get_comment_meta( $comment->comment_ID, 'edd_rating', true ); ?>
				<span itemprop="name" class="review-title-text"><?php echo get_comment_meta( $comment->comment_ID, 'edd_review_title', true ); ?></span>

				<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating">
					<div class="rating-box" role="img" aria-label="<?php echo $rating . ' ' . __( 'stars', 'lattice' ); ?>">
						<?php
						// Output filled stars
						for ( $i = 1; $i <= $rating; $i++ ) {
							echo '<i class="fa fa-star"></i>';
						} // end for

						// Output empty stars
						$empty_stars = 5 - $rating;
						for ( $i = 1; $i <= $empty_stars; $i++ ) {
							echo '<i class="fa fa-star-o"></i>';
						} // end for
						?>
					</div>
					<div style="display:none" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
						<meta itemprop="worstRating" content="1" />
						<span itemprop="ratingValue"><?php echo $rating; ?></span>
						<span itemprop="bestRating">5</span>
					</div>
				</div>
			</div><!-- /.review-meta -->
			<?php } // end if ?>

			<?php if ( '0' == $comment->comment_approved ) { ?>
				<p class="alert"><?php echo apply_filters( 'lattice_comment_awaiting_moderation', __( 'Your comment is awaiting moderation.', 'lattice' ) ); ?></p>
			<?php } // end if ?>

			<div class="comment-text">
				<?php
				comment_text();

				if ( class_exists( 'EDD_Reviews' ) && 'download' !== get_post_type( $post->ID ) ) {
					comment_reply_link(
						array_merge(
							$args,
							array(
								'depth'      => $depth,
								'max_depth'  => $args['max_depth'],
								'reply_text' => sprintf( '<p>%s</p>', __( 'Reply', 'lattice' ) )
							)
						)
					);
				} // end if
				?>
			</div><!-- /.comment-text -->
		</div><!-- /#div-comment-<?php comment_ID(); ?> -->
<?php
} // end lattice_comment

/**
 * Display custom star icons in the comments form
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_ratings_box( $output ) {
	ob_start();
	?>
	<span class="edd_reviews_rating_box">
		<span class="edd_ratings">
			<a class="edd_rating" href="" data-rating="5"><i class="fa fa-star-o"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="5"/>5&nbsp;</span>

			<a class="edd_rating" href="" data-rating="4"><i class="fa fa-star-o"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="4"/>4&nbsp;</span>

			<a class="edd_rating" href="" data-rating="3"><i class="fa fa-star-o"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="3"/>3&nbsp;</span>

			<a class="edd_rating" href="" data-rating="2"><i class="fa fa-star-o"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="2"/>2&nbsp;</span>

			<a class="edd_rating" href="" data-rating="1"><i class="fa fa-star-o"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="1"/>1&nbsp;</span>
		</span>
	</span>
	<?php
	return ob_get_clean();
} // end lattice_ratings_box
add_filter( 'edd_reviews_rating_box', 'lattice_ratings_box' );

/* ----------------------------------------------------------- *
 * 5. Features/Template Functions
 * ----------------------------------------------------------- */

/**
 * Customisze the EDD Purchase Link
 *
 * @global  $edd_options EDD Options
 * @global  $post WordPress Post Object
 * @since   1.0
 * @version 1.0
 */
function lattice_purchase_link( $post_id = null ) {
	global $edd_options, $post;

	if ( ! $post_id ) {
		$post_id = $post->ID;
	} // end if

	if( ! is_singular( 'download' ) ) {
		remove_action( 'edd_purchase_link_top', 'edd_download_purchase_form_quantity_field', 10 );
	}

	$variable_pricing = edd_has_variable_prices( $post_id );

	if ( ! $variable_pricing ) {
		echo edd_get_purchase_link( array( 'download_id' => $post_id, 'price' => false ) );
	} else if ( ! is_single() && $variable_pricing ) {
		$button = ! empty( $edd_options[ 'add_to_cart_text' ] ) ? $edd_options[ 'add_to_cart_text' ] : __( 'Purchase', 'lattice' );
		printf( '<a href="#edd-pricing-%s" class="button edd-add-to-cart-trigger">%s</a>', $post->ID, $button );
		echo edd_get_purchase_link( array( 'download_id' => $post_id, 'price' => false ) );
	} else if ( is_single() && $variable_pricing ) {
		echo edd_get_purchase_link( array( 'download_id' => $post_id, 'price' => false ) );
	} // end if
} // end lattice_purchase_link

/**
 * Display a custom post thumbnail based on the current template
 *
 * @uses    the_post_thumbnail()
 * @since   1.0
 * @version 1.0
 */
function lattice_post_thumbnail() {
	// Bail if the post is password protected or a post thumbnail isn't added
	if ( post_password_required() || ! has_post_thumbnail() ) {
		return;
	} // end if

	if ( is_singular() ) { ?>

		<div class="post-thumbnail">
			<?php
			if ( is_page_template( 'page-templates/full-width.php' ) ) {
				the_post_thumbnail( 'full' );
			} else {
				the_post_thumbnail();
			} // end if
			?>
		</div><!-- /.post-thumbnail -->

	<?php } else { ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>">
			<?php
			if ( is_page_template( 'page-templates/full-width.php' ) ) {
				the_post_thumbnail( 'full' );
			} else {
				the_post_thumbnail();
			} // end if
			?>
		</a>

	<?php } // end if
}
// end lattice_post_thumbnail

/**
 * Add post navigation
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_post_navigation() {
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	} // end if
	?>

	<nav class="post-navigation" role="navigation">
		<div class="nav-links">
			<?php
			if ( is_attachment() ) {
				previous_post_link( '%link', __( '<span class="nav">Published In</span> %title', 'lattice' ) );
			} else {
				previous_post_link( '%link', __( '<span class="nav">Previous Post</span> %title', 'lattice' ) );
				next_post_link( '%link', __( '<span class="nav">Next Post</span> %title', 'lattice' ) );
			} // end if
			?>
		</div><!-- /.nav-links -->
	</nav><!-- /.post-navigation -->

	<?php
} // end lattice_post_navigation

/**
 * Page Navigation
 *
 * @global  WP_Query $wp_query WordPress Query object.
 * @since   1.0
 * @version 1.0
 */
function lattice_page_navigation() {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) {
	?>
		<div class="clearfix">&nbsp;</div>
		<div id="page-nav" class="clearfix">
			<ul class="paged">
				<?php if ( get_next_posts_link() ) { ?>

					<li class="previous">
						<?php next_posts_link( __( '<span class="nav-previous meta-nav"><i class="fa fa-chevron-left"></i> Older</span>', 'edd' ) ); ?>
					</li><!-- /.previous -->

				<?php
				} // end if

				if ( get_previous_posts_link() ) { ?>

					<li class="next">
						<?php previous_posts_link( __( '<span class="nav-next meta-nav">Newer <i class="fa fa-chevron-right"></i></span>', 'edd' ) ); ?>
					</li><!-- /.next -->

				<?php } // end if ?>
			</ul><!-- /.paged -->
		</div><!-- /#page-nav -->
	<?php } // end if
} // end lattice_page_navigation

/**
 * Post Date
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_post_date() {
	printf(
		'<span class="entry-date"><time datetime="%1$s">%2$s</time></span>',
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
} // end lattice_post_date

/**
 * Comments Popup Link
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_comments_popup_link() {
	$icon = '<i class="fa fa-comments-o"></i>';
	comments_popup_link(
		sprintf( __( '%1$s Leave a comment', 'lattice' ), $icon ),
		sprintf( __( '%1$s 1 Comment', 'lattice' ), $icon ),
		$icon . ' ' . __( '% Comments', 'lattice' )
	);
} // end lattice_comments_popup_link

/**
 * Comments Title
 *
 * @global  $post WordPress Post Object
 * @since   1.0
 * @version 1.0
 */
function lattice_comments_title() {
	global $post;

	$comments_number = get_comments_number( $post->ID );

	if ( 'download' == get_post_type( $post->ID ) && class_exists( 'EDD_Reviews' ) ) {
		if ( 0 < $comments_number ) {
			printf(
				_nx(
					'One Review',
					'%1$s Reviews',
					get_comments_number(),
					'comments title',
					'lattice'
				),
				number_format_i18n( get_comments_number() )
			);
		} elseif ( 0 == $comments_number ) {
			_e( 'Reviews', 'lattice' );
		} // end if
	} else {
		_e( 'Comments', 'lattice' );
	} // end if
} // end lattice_comments_title

/**
 * Custom shopping cart which is displayed in the modal box after a user clicks
 * on the 'shopping cart' icon in the header
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_shopping_cart() {
	ob_start();
	?>
	<div id="shopping-cart-modal">
		<!--dynamic-cached-content-->
		<?php $cart_items = edd_get_cart_contents(); ?>

		<?php
		if ( $cart_items ) {
			printf( '<h2>%s</h2>', __( 'Shopping Cart', 'lattice' ) );
			foreach ( $cart_items as $key => $item ) {
				echo edd_get_cart_item_template( $key, $item, false );
			} // end foreach
			edd_get_template_part( 'widget', 'cart-checkout' );
		} else {
			edd_get_template_part( 'widget', 'cart-empty' );
		} // end if
		?>
		<!--/dynamic-cached-content-->
	</div><!-- /#shopping-cart-modal -->
	<?php
	echo ob_get_clean();
} // end lattice_shopping_cart

/**
 * Display the term description if one has been set
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_term_description() {
	$description = term_description();

	if ( ! empty( $description ) ) {
		?>
		<p class="term-description"><?php echo strip_tags( term_description() ); ?></p>
		<?php
	}
} // end lattice_term_description

/**
 * Display social icons in the footer based on the options added form the
 * WordPress Customizer
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_social_icons() {
	$twitter = get_theme_mod( 'lattice_twitter' );
	$facebook = get_theme_mod( 'lattice_facebook' );
	$instagram = get_theme_mod( 'lattice_instagram' );
	$gplus = get_theme_mod( 'lattice_gplus' );

	if ( ! ( empty( $twitter ) || empty( $facebook ) || empty( $instagram ) || empty( $gplus ) ) ) {
		echo '<div class="social-profiles">';
	}

	if ( ! empty( $twitter ) ) {
		printf( '<a class="twitter" href="https://twitter.com/%1$s"><i class="fa fa-twitter"></i></a>', strip_tags( html_entity_decode( $twitter ) ) );
	}

	if ( ! empty( $facebook ) ) {
		printf( '<a class="facebook" href="%1$s"><i class="fa fa-facebook"></i></a>', esc_url( $facebook ) );
	}

	if ( ! empty( $instagram ) ) {
		printf( '<a class="instagram" href="http://instagram.com/%1$s"><i class="fa fa-instagram"></i></a>', strip_tags( html_entity_decode( $instagram ) ) );
	}

	if ( ! empty( $gplus ) ) {
		printf( '<a class="google-plus" href="%1$s"><i class="fa fa-google-plus"></i></a>', esc_url( $gplus ) );
	}

	if ( ! ( empty( $twitter ) || empty( $facebook ) || empty( $instagram ) || empty( $gplus ) ) ) {
		echo '</div><!-- /.social-profiles -->';
	}
} // end lattice_social_icons

/* ----------------------------------------------------------- *
 * 6. Custom Actions/Filters
 * ----------------------------------------------------------- */

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param   string $title Default title
 * @param   string $sep   Separator
 * @global  $paged Current page
 * @global  $page  Current page
 * @global  $post  WordPress Post Object
 * @return  string $title Newly formatted title
 * @since   1.0
 * @version 1.0
 */
function lattice_wp_title( $title, $sep ) {
	global $paged, $page, $post;

	/* Default title */
	$title = get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' );

	/* Search */
	if ( is_search() ) {
		$title = __( 'Search Results For' , 'lattice' ) . ' ' . get_query_var( 's' ) . ' | ' . get_bloginfo( 'name' );
	/* Homepage */
	} elseif ( is_home() || is_front_page() ) {
		$title = get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' );
	/* Single page */
	} elseif ( is_page() ) {
		$title = strip_tags( htmlspecialchars_decode( get_the_title( $post->ID ) ) ) . ' | ' . get_bloginfo( 'name' );
	/* 404 Page */
	} elseif ( is_404() ) {
		$title = __( '404 - Nothing Found', 'lattice' ) . ' | ' . get_bloginfo( 'name' );
	/* Author Archive */
	} elseif ( is_author() ) {
		$title = get_userdata( get_query_var( 'author' ) )->display_name . ' | ' . __( 'Author Archive', 'lattice' )    . ' | ' . get_bloginfo( 'name' );
	/* Category Archive */
	} elseif ( is_category() ) {
		$title = single_cat_title( '', false ) . ' | ' . __( 'Category Archive', 'lattice' ) . ' | ' . get_bloginfo( 'name' );
	/* Tag Archive */
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false ) . ' | ' . __( 'Tag Archive', 'lattice' ) . ' | ' . get_bloginfo( 'name' );
	/* Single Blog Post */
	} elseif ( is_single() ) {
		$post_title = the_title_attribute( 'echo=0' );

		if ( ! empty( $post_title ) ) {
			$title = $post_title . ' | ' . get_bloginfo( 'name' );
		} // end if
	} // end if

	/* Feed (RSS|Atom) */
	if ( is_feed() ) {
		return $title;
	} // end if

	/* Pagination */
	if ( $paged >= 2 || $page >= 2 ) {
		$title = "$title $sep " . sprintf( __( 'Page %s', 'lattice' ), max( $paged, $page ) );
	} // end if

	return apply_filters( 'lattice_wp_title', $title );
} // end lattice_wp_title
add_filter( 'wp_title', 'lattice_wp_title', 10, 2 );

/**
 * We don't want the purchase link to be displayed by default as we'll
 * be using a custom built one
 *
 * @since   1.0
 * @version 1.0
 */
remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );

/**
 * Remove default output of the review title and star rating,
 * lattice_comment() for the modified output of the review title
 * and star rating.
 */
add_filter( 'edd_reviews_ratings_html', '__return_false' );

/**
 * Override the default image quality and make sure when images are uploaded that
 * the quality doesn't get degraded.
 *
 * @param   int $quality Default image quality assigned by WordPress
 * @return  int
 * @since   1.0
 * @version 1.0
 */
function lattice_image_full_quality( $quality ) {
	return 100;
} // end lattice_image_full_quality
add_filter( 'jpeg_quality', 'lattice_image_full_quality' );
add_filter( 'wp_editor_set_quality', 'lattice_image_full_quality' );

/**
 * Add template name body classes to custom pages
 *
 * @global  $edd_options
 * @param   array $classes Default body classes
 * @return  array $classes Updated body classes
 * @since   1.0
 * @version 1.0
 */
function lattice_body_class( $classes ) {
	global $edd_options;

	if( ! function_exists( 'edd_get_option' ) ) {
		return;
	}

	/* Add custom <body> classes to the checkout */
	if ( is_page( edd_get_option( 'purchase_page' ) ) ) {
		$classes[] = 'page-template';
		$classes[] = 'page-template-template-full-width-php';
	} // end if

	/* Add custom <body> classes to the purchase confirmation page */
	if ( is_page( edd_get_option( 'success_page' ) ) ) {
		$classes[] = 'edd-success-page';
	} // end if

	return $classes;
} // end lattice_body_class
add_filter( 'body_class', 'lattice_body_class' );

/**
 * Override the template based on the page being viewed
 *
 * @global  $edd_options
 * @global  WordPress Post Object $post;
 * @since   1.0
 * @version 1.0
 */
function lattice_template_override() {
	global $edd_options, $post;

	if ( is_page( $edd_options['purchase_page'] ) ) {
		load_template( dirname( __FILE__ ) . '/template-full-width.php' );
		die();
	} // end if
} // end lattice_template_override
add_action( 'template_redirect', 'lattice_template_override' );

/**
 * Generate the HTML for the modal
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_modal() {
	ob_start();
	?>
	<div id="overlay"></div><!-- /#overlay -->
	<div id="modal">
		<div id="content"></div><!-- /#content -->
		<a href="#" id="close-modal"><i class="fa fa-times-circle-o"></i></a>
	</div><!-- /#modal -->
	<?php
	$modal = ob_get_clean();
	echo apply_filters( 'lattice_modal', $modal );
} // end lattice_modal

/**
 * Modify the default output of the [downloads] shortcode
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_downloads_shortcode( $display, $atts, $buy_button, $columns, $column_width, $downloads, $excerpt, $full_content, $price, $thumbnails, $query ) {
	switch( intval( $columns ) ) {
		case 1:
			$column_number = 'col-1';
			break;
		case 2:
			$column_number = 'col-2';
			break;
		case 3:
		case 4:
		case 5:
		case 6:
			$column_number = 'col-3';
			break;
	} // end switch

	ob_start();
	$i = 1;
	?>
	<div class="downloads <?php echo $column_number; ?> clearfix">
		<?php
		// Start the Loop.
		while ( $downloads->have_posts() ) {
			$downloads->the_post();
		?>
		<div itemscope itemtype="http://schema.org/Product" class="edd-download <?php if ( $i % $columns == 0 ) { echo 'col-end'; } ?>" id="edd_download_<?php echo get_the_ID(); ?>">
			<div class="edd-download-inner">
				<?php do_action( 'edd_download_before' ); ?>

				<?php
				if ( 'false' != $thumbnails ) {
					edd_get_template_part( 'shortcode', 'content-image' );
				} // end if

				edd_get_template_part( 'shortcode', 'content-title' );

				if ( $excerpt == 'yes' && $full_content != 'yes' ) {
						edd_get_template_part( 'shortcode', 'content-excerpt' );
				} else if ( $full_content == 'yes' ) {
						edd_get_template_part( 'shortcode', 'content-full' );
				} // end if

				if ( $price == 'yes' ) {
					edd_get_template_part( 'shortcode', 'content-price' );
				} // end if

				if ( $buy_button == 'yes' ) {
					echo edd_get_purchase_link( array( 'download_id' => get_the_ID(), 'price' => false ) );
				} // end if
				?>

				<?php do_action( 'edd_download_after' ); ?>
			</div><!-- /.edd-download-inner -->
		</div><!-- /.edd-download -->
		<?php if ( $i % $columns == 0 ) { ?><div style="clear:both;"></div><?php } ?>
		<?php $i++; } // end while ?>

		<?php wp_reset_postdata(); ?>
	</div><!-- /.downloads.<?php echo $column_number; ?> -->

	<div class="download-pagination navigation">
		<?php
		if ( is_single() ) {
			echo paginate_links( array(
				'base'    => get_permalink() . '%#%',
				'format'  => '?paged=%#%',
				'current' => max( 1, $query['paged'] ),
				'total'   => $downloads->max_num_pages
			) );
		} else {
			$big = 999999;
			echo paginate_links( array(
				'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'  => '?paged=%#%',
				'current' => max( 1, $query['paged'] ),
				'total'   => $downloads->max_num_pages
			) );
		} // end if
		?>
	</div><!-- /.download-pagination -->
	<?php
	$display = ob_get_clean();
	return $display;
} // end lattice_downloads_shortcode
add_filter( 'downloads_shortcode', 'lattice_downloads_shortcode', 10, 11 );


/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @since   1.0
 * @version 1.0
 * @param   array $args Configuration arguments.
 * @return  array $args
 */
function lattice_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
} // end lattice_page_menu_args
add_filter( 'wp_page_menu_args', 'lattice_page_menu_args' );

/* ----------------------------------------------------------- *
 * 7. Widgets
 * ----------------------------------------------------------- */

/**
 * Register widgetized area and update sidebar with default widgets.
 *
 * @uses    register_sidebar()
 * @since   1.0
 * @version 1.0
 */
function lattice_sidebar_setup() {
	/* Global Sidebar */
   register_sidebar( array(
		'name'          => __( 'Sidebar', 'lattice' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );

	/* Single Download */
   register_sidebar( array(
		'name'          => __( 'Download Page Sidebar', 'lattice' ),
		'id'            => 'single-download-sidebar',
		'description'   => __( 'Sidebar which displays on the individual download pages',' lattice' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );

   /* Footer */
   register_sidebar( array(
		'name'          => __( 'Footer', 'lattice' ),
		'id'            => 'footer-sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h6 class="widget-title">',
		'after_title'   => '</h6>'
	) );
} // end lattice_sidebar_setup
add_action( 'widgets_init', 'lattice_sidebar_setup' );

/* ----------------------------------------------------------- *
 * 8. Misc
 * ----------------------------------------------------------- */

/**
 * Sets the authordata global when viewing an author archive.
 *
 * This provides backwards compatibility with
 * http://core.trac.wordpress.org/changeset/25574
 *
 * It removes the need to call the_post() and rewind_posts() in an author
 * template to print information about the author.
 *
 * @global  WP_Query $wp_query WordPress Query object.
 * @since   1.0
 * @version 1.0
 */
function lattice_setup_author() {
	global $wp_query;

	if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
		$GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
	} // end if
} // end lattice_setup_author
add_action( 'wp', 'lattice_setup_author' );

/**
 * Register Lato Google font for Lattice
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_font_url() {
	$font_url = '';
	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Lato, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Lato font: on or off', 'lattice' ) ) {
		$font_url = esc_url( add_query_arg( 'family', urlencode( 'Lato:300,400,700' ), "//fonts.googleapis.com/css" ) );
	} // end if

	return $font_url;
} // end lattice_font_url

/* ----------------------------------------------------------- *
 * 9. Customizer
 * ----------------------------------------------------------- */

/**
 * Implement WordPress Theme Customizer
 *
 * @param   object WP_Customize_Manager $wp_customize Theme Customizer object.
 * @since   1.0
 * @version 1.0
 */
function lattice_customize_register( $wp_customize ) {
	// Add postMessage support
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	// Add main options section
	$wp_customize->add_section( 'lattice', array(
		'title'       => __( 'Lattice Options', 'lattice' ),
		'description' => '',
		'priority'    => 130,
	) );

	// Add social profiles section
	$wp_customize->add_section( 'lattice_social', array(
		'title'       => __( 'Social Profiles', 'lattice' ),
		'description' => __( 'Add URLs to your social profiles to display a link to them in the footer.', 'lattice' ),
		'priority'    => 131,
	) );

	// Register all the settings
	$wp_customize->add_setting( 'lattice_homepage_text', array(
		'default' => __( 'Welcome To Our Store', 'lattice' )
	) );
	$wp_customize->add_setting( 'lattice_twitter' );
	$wp_customize->add_setting( 'lattice_facebook' );
	$wp_customize->add_setting( 'lattice_instagram' );
	$wp_customize->add_setting( 'lattice_gplus' );

	// Add all the controls
	$wp_customize->add_control( 'lattice_homepage_text', array(
		'label'   => __( 'Homepage Introduction Text', 'lattice' ),
		'section' => 'lattice',
		'type'    => 'text'
	) );

	$wp_customize->add_control( 'lattice_twitter', array(
		'label'   => __( 'Twitter Username (without the @)', 'lattice' ),
		'section' => 'lattice_social',
		'type'    => 'text'
	) );

	$wp_customize->add_control( 'lattice_facebook', array(
		'label'   => __( 'Facebook Page URL', 'lattice' ),
		'section' => 'lattice_social',
		'type'    => 'text'
	) );

	$wp_customize->add_control( 'lattice_instagram', array(
		'label'   => __( 'Instagram Username', 'lattice' ),
		'section' => 'lattice_social',
		'type'    => 'text'
	) );

	$wp_customize->add_control( 'lattice_gplus', array(
		'label'   => __( 'Google+ ID', 'lattice' ),
		'section' => 'lattice_social',
		'type'    => 'text'
	) );
} // end lattice_customize_register
add_action( 'customize_register', 'lattice_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_customize_preview_js() {
	wp_enqueue_script( 'lattice-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), LATTICE_THEME_VERSION, true );
} // end lattice_customize_preview_js
add_action( 'customize_preview_init', 'lattice_customize_preview_js' );

/* ----------------------------------------------------------- *
 * 10. Software Licensing Integration
 * ----------------------------------------------------------- */

/**
 * Initialise the updater
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_theme_updater() {
	$license = trim( get_option( 'lattice_theme_license_key' ) );

	$edd_updater = new EDD_SL_Theme_Updater( array(
		'remote_api_url' => 'https://easydigitaldownloads.com',
		'version'        => LATTICE_THEME_VERSION,
		'license'        => $license,
		'item_name'      => 'Lattice',
		'author'         => 'Sunny Ratilal'
	) );
} // end lattice_theme_updater
add_action( 'admin_init', 'lattice_theme_updater' );

/**
 * Add menu item to input license key
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_add_menu_page() {
	add_theme_page( __( 'Theme License', 'lattice' ), __( 'Theme License', 'lattice' ), 'manage_options', 'lattice-license', 'lattice_license_page' );
} // end lattice_add_menu_page
add_action( 'admin_menu', 'lattice_add_menu_page' );

/**
 * Render the license page
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_license_page() {
	$license    = get_option( 'lattice_license_key' );
	$status     = get_option( 'lattice_license_key_status' );
	?>
	<div class="wrap">
		<h2><?php _e( 'Lattice Theme License Options', 'lattice' ); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'lattice_license' ); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e( 'License Key', 'lattice' ); ?>
						</th>
						<td>
							<input id="lattice_license_key" name="lattice_license_key" type="text" class="regular-text" value="<?php esc_attr( $license ); ?>" />
							<label class="description" for="lattice_license_key"><?php _e( 'Enter your license key', 'lattice' ); ?></label>
						</td>
					</tr>
					<?php if ( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Activate License', 'lattice' ); ?>
							</th>
							<td>
								<?php if ( $status !== false && $status == 'valid' ) { ?>
									<span style="color:green;"><?php _e('active'); ?></span>
									<?php wp_nonce_field( 'lattice_nonce', 'lattice_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_theme_license_deactivate" value="<?php _e( 'Deactivate License', 'lattice' ); ?>"/>
								<?php } else {
									wp_nonce_field( 'lattice_nonce', 'lattice_nonce' ); ?>
									<input type="submit" class="button-secondary" name="edd_theme_license_activate" value="<?php _e( 'Activate License', 'lattice' ); ?>"/>
								<?php } // end if ?>
							</td>
						</tr>
					<?php } // end if ?>
				</tbody>
			</table><!-- /.form-table -->
			<?php submit_button(); ?>
		</form>
	<?php
} // end lattice_license_page

/**
 * Register setting for theme license page
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_register_settings() {
	// creates our settings in the options table
	register_setting( 'lattice_license', 'lattice_license_key', 'lattice_sanitize_license' );
} // end lattice_register_settings
add_action( 'admin_init', 'lattice_register_settings' );

/**
 * Gets rid of the local license status option when adding a new one
 *
 * @param   string $new New license key
 * @return  string      New license key
 * @since   1.0
 * @version 1.0
 */
function lattice_sanitize_license( $new ) {
	$old = get_option( 'lattice_license_key' );

	if ( $old && $old != $new ) {
		delete_option( 'lattice_license_key_status' ); // new license has been entered, so must reactivate
	} // end if

	return $new;
} // end lattice_sanitize_license

/**
 * Activate the license key based on the input
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_activate_license() {
	if ( isset( $_POST['edd_theme_license_activate'] ) ) {
		if ( ! check_admin_referer( 'lattice_nonce', 'lattice_nonce' ) ) {
			return;
		} // end if

		$license = trim( get_option( 'lattice_license_key' ) );

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( 'Lattice' )
		);

		$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, 'https://easydigitaldownloads.com' ) ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) {
			return false;
		} // end if

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'lattice_license_key_status', $license_data->license );
	} // end if
} // end lattice_activate_license
add_action( 'admin_init', 'lattice_activate_license' );

/**
 * Activate the license and reduce the site count on the remote site
 *
 * @since   1.0
 * @version 1.0
 */
function lattice_deactivate_license() {
	if ( isset( $_POST['edd_theme_license_deactivate'] ) ) {
		if ( ! check_admin_referer( 'lattice_nonce', 'lattice_nonce' ) ) {
			return;
		} // end if

		$license = trim( get_option( 'lattice_license_key' ) );

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( 'Lattice' )
		);

		$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, 'https://easydigitaldownloads.com' ) ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) {
			return false;
		} // end if

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data->license == 'deactivated' ) {
			delete_option( 'lattice_license_key_status' );
		} // end if
	} // end if
} // end lattice_deactivate_license
add_action( 'admin_init', 'lattice_deactivate_license' );
