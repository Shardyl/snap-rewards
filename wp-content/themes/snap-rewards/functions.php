<?php
/**
 * Snap Rewards theme functions.
 * Faithful migration replica of snap-rewards.com onto WP Engine.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SNAP_VERSION', '1.0.0' );

/**
 * BUILD-TIME NOINDEX.
 * Keep the replica invisible to search engines during the build/iterate phase.
 * Flip to false ONLY when the operator approves go-live (see go-live checklist).
 */
if ( ! defined( 'SNAP_BUILD_NOINDEX' ) ) {
	define( 'SNAP_BUILD_NOINDEX', true );
}

/* ---------------------------------------------------------------------------
 * Theme setup
 * ------------------------------------------------------------------------- */
function snap_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus( array(
		'primary' => 'Header Menu (menu-menu-1)',
		'footer'  => 'Footer Menu (menu-menu-2)',
	) );
}
add_action( 'after_setup_theme', 'snap_setup' );

/* ---------------------------------------------------------------------------
 * Styles + scripts — reproduce the original load set exactly.
 * Plugin CSS (Contact Form 7, Complianz) and core (wp-block-library) come from
 * the plugins/core themselves; here we enqueue the theme's own bundles.
 * ------------------------------------------------------------------------- */
function snap_assets() {
	$css = get_template_directory_uri() . '/css';
	$ver = SNAP_VERSION;

	// Order mirrors the original document order of the theme stylesheets.
	wp_enqueue_style( 'snap-rewards-style', $css . '/snap-rewards-style.css', array(), $ver );
	wp_enqueue_style( 'bootstrap',          $css . '/bootstrap.css',          array(), $ver );
	wp_enqueue_style( 'animate',            $css . '/animate.css',            array(), $ver );
	wp_enqueue_style( 'magnific-popup',     $css . '/magnific-popup.css',     array(), $ver );
	wp_enqueue_style( 'fontawesome',        $css . '/fontawesome.css',        array(), $ver );
	wp_enqueue_style( 'dripicons',          $css . '/dripicons.css',          array(), $ver );
	wp_enqueue_style( 'slick',              $css . '/slick.css',              array(), $ver );
	wp_enqueue_style( 'snap-default',       $css . '/default.css',            array(), $ver );
	wp_enqueue_style( 'swiper',             $css . '/swiper.css',             array(), $ver );
	wp_enqueue_style( 'main-style',         $css . '/main-style.css',         array(), $ver );
	wp_enqueue_style( 'responsive',         $css . '/responsive.css',         array(), $ver );

	// JS — jQuery from core, then the theme scripts in the original order.
	$js = get_template_directory_uri() . '/js';
	wp_enqueue_script( 'modernizr', $js . '/vendor/modernizr-3.5.0.min.js', array(), $ver, false );

	$jq = array( 'jquery' );
	$scripts = array(
		'snap-navigation'   => '/navigation.js',
		'popper'            => '/popper.min.js',
		'bootstrap'         => '/bootstrap.min.js',
		'one-page-nav'      => '/one-page-nav-min.js',
		'slick'             => '/slick.min.js',
		'ajax-form'         => '/ajax-form.js',
		'paroller'          => '/paroller.js',
		'wow'               => '/wow.min.js',
		'isotope'           => '/js_isotope.pkgd.min.js',
		'parallax'          => '/parallax.min.js',
		'waypoints'         => '/jquery.waypoints.min.js',
		'counterup'         => '/jquery.counterup.min.js',
		'scrollup'          => '/jquery.scrollUp.min.js',
		'typed'             => '/typed.js',
		'parallax-scroll'   => '/parallax-scroll.js',
		'magnific-popup'    => '/jquery.magnific-popup.min.js',
		'element-in-view'   => '/element-in-view.js',
		'swiper'            => '/swiper.min.js',
		'snap-main'         => '/main.js',
	);
	foreach ( $scripts as $handle => $path ) {
		wp_enqueue_script( $handle, $js . $path, $jq, $ver, true );
	}
}
add_action( 'wp_enqueue_scripts', 'snap_assets' );

/* ---------------------------------------------------------------------------
 * Build-time noindex output (independent of blog_public / SEO plugin).
 * ------------------------------------------------------------------------- */
function snap_noindex_meta() {
	if ( SNAP_BUILD_NOINDEX ) {
		echo "<meta name=\"robots\" content=\"noindex, nofollow\">\n";
	}
}
add_action( 'wp_head', 'snap_noindex_meta', 1 );

function snap_noindex_header( $headers ) {
	if ( SNAP_BUILD_NOINDEX ) {
		$headers['X-Robots-Tag'] = 'noindex, nofollow';
	}
	return $headers;
}
add_filter( 'wp_headers', 'snap_noindex_header' );

/* ---------------------------------------------------------------------------
 * Render a captured content partial verbatim (the faithful page body).
 * Partials live in /inc/content/{slug}.html and are the exact <main> region of
 * the original page, with all URLs rewritten to root-relative.
 * ------------------------------------------------------------------------- */
function snap_render( $slug ) {
	$file = get_template_directory() . '/inc/content/' . sanitize_file_name( $slug ) . '.html';
	if ( is_readable( $file ) ) {
		$html = file_get_contents( $file );
		// Swap the contact-form token for the live Contact Form 7 shortcode.
		if ( false !== strpos( $html, '<!--SNAP_CF7-->' ) ) {
			$form_id = (int) get_option( 'snap_cf7_form_id', 0 );
			$shortcode = $form_id ? do_shortcode( '[contact-form-7 id="' . $form_id . '"]' ) : '';
			$html = str_replace( '<!--SNAP_CF7-->', $shortcode, $html );
		}
		echo $html; // phpcs:ignore — trusted theme-bundled markup
	} else {
		echo '<main><div class="container" style="padding:120px 0;text-align:center"><h2>Content not found: ' . esc_html( $slug ) . '</h2></div></main>';
	}
}

/* ---------------------------------------------------------------------------
 * Fallback nav (used before the menus are created in WP admin / via WP-CLI).
 * ------------------------------------------------------------------------- */
function snap_menu_fallback() {
	echo '<ul id="menu-menu-1" class="primary-nav">';
	$items = array(
		'/'              => 'Home',
		'/#pricing'      => 'Pricing',
		'/blog/'         => 'Loyalty news',
		'/partnerships/' => 'Partnerships',
		'/contact/'      => 'Contact',
		'/docs'          => 'Support',
	);
	foreach ( $items as $url => $label ) {
		echo '<li class="menu-item"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}
