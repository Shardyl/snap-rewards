<?php
/**
 * Snap Rewards theme functions.
 * Faithful migration replica of snap-rewards.com onto WP Engine.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SNAP_VERSION', '1.2.2' );

/* Sensa CMS field config (editable homepage copy/images) + token-render fallbacks. */
require_once get_template_directory() . '/inc/cms-config.php';

/**
 * LIVE-DOMAIN-AWARE CUTOVER.
 * The replica is previewed on the WP Engine temp domain and must stay invisible
 * to search engines there. The moment DNS for snap-rewards.com points at this
 * install, requests arriving on the live host must (a) serve with the correct
 * canonical (no redirect back to the temp domain) and (b) become indexable — so
 * the 1:1 replica seamlessly replaces the currently-indexed live site with no
 * manual flip needed at cutover.
 *
 * Emergency hold: define SNAP_FORCE_NOINDEX = true to noindex every host.
 */
function snap_live_hosts() {
	return array( 'snap-rewards.com', 'www.snap-rewards.com' );
}
function snap_is_live_host() {
	$h = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( $_SERVER['HTTP_HOST'] ) : '';
	return in_array( $h, snap_live_hosts(), true );
}
function snap_should_noindex() {
	if ( defined( 'SNAP_FORCE_NOINDEX' ) && SNAP_FORCE_NOINDEX ) {
		return true;
	}
	return ! snap_is_live_host();
}
/* Serve the right canonical host per request (temp preview + live cutover both work). */
function snap_dynamic_url() {
	return snap_is_live_host() ? 'https://snap-rewards.com' : 'https://snaprewards.wpenginepowered.com';
}
add_filter( 'option_home', 'snap_dynamic_url' );
add_filter( 'option_siteurl', 'snap_dynamic_url' );

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
	// Theme customizer / custom CSS — was inline at the END of <head> on the original,
	// so it must load LAST to preserve the cascade (hero h1 size, blog card borders,
	// single-post-area formatting, menu hover colour, breadcrumbs).
	wp_enqueue_style( 'snap-custom-inline', $css . '/custom-inline.css',      array(), $ver );

	// BetterDocs structural layout CSS — free doesn't enqueue the archive/sidebar
	// layout bundles (e.g. .betterdocs-display-flex two-column, sidebar widths),
	// so load the captured originals on docs pages to restore the navigable layout.
	if ( is_post_type_archive( 'docs' ) || is_singular( 'docs' ) || is_tax( 'doc_category' ) || is_tax( 'doc_tag' ) ) {
		$d = $css . '/docs';
		foreach ( array(
			'betterdocs-breadcrumb', 'betterdocs-pagination', 'betterdocs-category-grid',
			'betterdocs-category-box', 'betterdocs-docs', 'betterdocs-single', 'betterdocs-toc',
			'betterdocs-search-modal', 'betterdocs-reactions', 'betterdocs-social-share',
			'betterdocs-encyclopedia', 'betterdocs-glossaries', 'single-doc-attachments',
			'single-doc-related-articles', 'reading-time', 'simplebar', 'mediaelement', 'wp-mediaelement',
		) as $h ) {
			wp_enqueue_style( 'snap-' . $h, $d . '/' . $h . '.css', array(), $ver );
		}
		// load last so the two-column split is guaranteed
		wp_enqueue_style( 'snap-docs-layout', $css . '/docs-layout.css', array(), $ver );
	}

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
 * Host-aware indexing: noindex everywhere EXCEPT the live snap-rewards.com host.
 * Belt-and-braces — X-Robots-Tag header + Rank Math robots filter (Rank Math
 * owns the <meta robots>, so we steer it rather than printing a second tag).
 * ------------------------------------------------------------------------- */
function snap_noindex_header( $headers ) {
	if ( snap_should_noindex() ) {
		$headers['X-Robots-Tag'] = 'noindex, nofollow';
	}
	return $headers;
}
add_filter( 'wp_headers', 'snap_noindex_header' );

function snap_rankmath_robots( $robots ) {
	if ( snap_should_noindex() ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'nofollow';
	}
	return $robots;
}
add_filter( 'rank_math/frontend/robots', 'snap_rankmath_robots' );

/* Fallback meta only if Rank Math isn't active, so the temp host is never indexable. */
function snap_noindex_meta() {
	if ( snap_should_noindex() && ! class_exists( 'RankMath' ) ) {
		echo "<meta name=\"robots\" content=\"noindex, nofollow\">\n";
	}
}
add_action( 'wp_head', 'snap_noindex_meta', 1 );

/* Google Search Console site verification (URL-prefix property, META method). */
function snap_gsc_verify() {
	echo '<meta name="google-site-verification" content="vNcSvf8creJFwjkd90ilErvgoxDHRdD3ILuraHhzM-g" />' . "\n";
}
add_action( 'wp_head', 'snap_gsc_verify', 1 );

/* Google Analytics 4 (gtag.js) — live host only, so build/staging traffic isn't tracked. */
function snap_ga4() {
	if ( ! snap_is_live_host() ) {
		return;
	}
	echo "<!-- Google tag (gtag.js) -->\n";
	echo '<script async src="https://www.googletagmanager.com/gtag/js?id=G-X6R9LVM5F4"></script>' . "\n";
	echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-X6R9LVM5F4');</script>\n";
}
add_action( 'wp_head', 'snap_ga4', 2 );

/* Migration rule: blog posts NEVER accept comments or pings (kills comment spam at the
 * source, regardless of per-post settings). Site defaults are also set to closed. */
function snap_no_comments( $open, $post_id ) {
	return ( 'post' === get_post_type( $post_id ) ) ? false : $open;
}
add_filter( 'comments_open', 'snap_no_comments', 10, 2 );
add_filter( 'pings_open', 'snap_no_comments', 10, 2 );

/* ---------------------------------------------------------------------------
 * Body classes: the original /blog/ was a posts archive (body class "blog hfeed"),
 * so its CSS is scoped to `.blog ...` (e.g. the post-card Read More spacing
 * `.blog .card .entry-footer{padding:0 20px 25px}`). Our /blog/ is a static page,
 * so add those classes to reproduce the original styling exactly.
 * ------------------------------------------------------------------------- */
function snap_body_class( $classes ) {
	if ( is_page( 'blog' ) ) {
		$classes = array_diff( $classes, array( 'page', 'page-template-default' ) );
		$classes[] = 'blog';
		$classes[] = 'hfeed';
	}
	return $classes;
}
add_filter( 'body_class', 'snap_body_class' );

/* ---------------------------------------------------------------------------
 * Inject the navigable docs sidebar (search + category/doc tree) into the
 * BetterDocs category + single-doc pages. Free doesn't render a sidebar on these
 * (it's a Pro layout), so we output-buffer the page and insert a sidebar as the
 * first child of the existing `.betterdocs-content-wrapper.betterdocs-display-flex`
 * so it becomes the left column and the content sits on the right.
 * ------------------------------------------------------------------------- */
/**
 * Build the docs navigation tree directly (all categories + their docs as links,
 * current item highlighted). More reliable than BetterDocs' free category-list
 * shortcode, which renders incompletely mid-request.
 */
function snap_docs_sidebar_nav() {
	$terms = get_terms( array(
		'taxonomy'   => 'doc_category',
		'hide_empty' => true,
		'orderby'    => 'meta_value_num',
		'meta_key'   => 'doc_category_order',
		'order'      => 'ASC',
	) );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		$terms = get_terms( array( 'taxonomy' => 'doc_category', 'hide_empty' => true ) );
	}
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}
	$qo = get_queried_object();
	$current_cat = 0;
	$current_doc = 0;
	if ( $qo instanceof WP_Term ) {
		$current_cat = $qo->term_id;
	} elseif ( $qo instanceof WP_Post ) {
		$current_doc = $qo->ID;
		$pt = wp_get_post_terms( $qo->ID, 'doc_category' );
		if ( $pt && ! is_wp_error( $pt ) ) { $current_cat = $pt[0]->term_id; }
	}
	$out = '<div class="snap-docs-nav">';
	foreach ( $terms as $term ) {
		$docs = get_posts( array(
			'post_type'   => 'docs',
			'numberposts' => -1,
			'orderby'     => 'menu_order title',
			'order'       => 'ASC',
			'tax_query'   => array( array( 'taxonomy' => 'doc_category', 'field' => 'term_id', 'terms' => $term->term_id ) ),
		) );
		$open = ( $term->term_id === $current_cat ) ? ' is-open' : '';
		$out .= '<div class="snap-docs-nav-cat' . $open . '">';
		$out .= '<a class="snap-docs-nav-head" href="' . esc_url( get_term_link( $term ) ) . '">'
			. '<span class="snap-folder"></span><span class="snap-cat-name">' . esc_html( $term->name ) . '</span>'
			. '<span class="snap-cat-count">' . count( $docs ) . '</span></a>';
		$out .= '<ul class="snap-docs-nav-list">';
		foreach ( $docs as $d ) {
			$cur = ( $d->ID === $current_doc ) ? ' class="current"' : '';
			$out .= '<li' . $cur . '><a href="' . esc_url( get_permalink( $d->ID ) ) . '">' . esc_html( $d->post_title ) . '</a></li>';
		}
		$out .= '</ul></div>';
	}
	$out .= '</div>';
	return $out;
}

/** Breadcrumb: Home / Docs / Category [ / Doc ] — the "where am I" index. */
function snap_docs_breadcrumb() {
	$out = '<nav class="snap-docs-crumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a>'
		. '<span class="sep">/</span><a href="' . esc_url( home_url( '/docs/' ) ) . '">Docs</a>';
	$qo = get_queried_object();
	if ( $qo instanceof WP_Term ) {
		$out .= '<span class="sep">/</span><span class="cur">' . esc_html( $qo->name ) . '</span>';
	} elseif ( $qo instanceof WP_Post ) {
		$t = wp_get_post_terms( $qo->ID, 'doc_category' );
		if ( $t && ! is_wp_error( $t ) ) {
			$out .= '<span class="sep">/</span><a href="' . esc_url( get_term_link( $t[0] ) ) . '">' . esc_html( $t[0]->name ) . '</a>';
		}
		$out .= '<span class="sep">/</span><span class="cur">' . esc_html( get_the_title( $qo->ID ) ) . '</span>';
	}
	return $out . '</nav>';
}

/** Category header band: folder icon + category name + "N Docs" (category pages only). */
function snap_docs_cat_header() {
	$qo = get_queried_object();
	if ( ! ( $qo instanceof WP_Term ) ) {
		return '';
	}
	return '<div class="snap-docs-cathead"><span class="snap-cathead-ico"></span>'
		. '<h1 class="snap-cathead-title">' . esc_html( $qo->name ) . '</h1>'
		. '<div class="snap-cathead-count">' . (int) $qo->count . ' Docs</div></div>';
}

function snap_docs_footer_sidebar() {
	if ( ! ( is_singular( 'docs' ) || is_tax( 'doc_category' ) || is_tax( 'doc_tag' ) ) ) {
		return;
	}
	$inner   = do_shortcode( '[betterdocs_search_form]' ) . snap_docs_sidebar_nav();
	$sidebar = '<div class="snap-docs-sidebar"><div class="snap-docs-sidebar-inner">' . $inner . '</div></div>';
	$header  = snap_docs_breadcrumb() . snap_docs_cat_header();
	echo '<div id="snap-docs-sb-src" hidden>' . $sidebar . '</div>';
	echo '<div id="snap-docs-head-src" hidden>' . $header . '</div>';
	echo '<script>(function(){'
		. 'var w=document.querySelector(".betterdocs-content-wrapper");'
		. 'var s=document.getElementById("snap-docs-sb-src");'
		. 'if(w&&s&&s.firstElementChild){w.insertBefore(s.firstElementChild,w.firstChild);}'
		. 'if(s&&s.parentNode){s.parentNode.removeChild(s);}'
		. 'var h=document.getElementById("snap-docs-head-src");'
		. 'var area=document.querySelector(".betterdocs-content-area")||document.querySelector(".betterdocs-content-inner-area");'
		. 'if(h&&area){area.insertAdjacentHTML("afterbegin",h.innerHTML);}'
		. 'if(h&&h.parentNode){h.parentNode.removeChild(h);}'
		. '})();</script>';
}
add_action( 'wp_footer', 'snap_docs_footer_sidebar', 5 );

/* ---------------------------------------------------------------------------
 * Force our own docs archive template (search hero + category-box grid) for the
 * /docs/ landing, overriding BetterDocs' built-in flat-list layout. Runs late so
 * it wins against BetterDocs' own template_include.
 * ------------------------------------------------------------------------- */
function snap_docs_archive_template( $template ) {
	if ( is_post_type_archive( 'docs' ) ) {
		$custom = get_template_directory() . '/archive-docs.php';
		if ( is_readable( $custom ) ) {
			return $custom;
		}
	}
	// BetterDocs free mis-renders single docs with the archive layout; force ours.
	if ( is_singular( 'docs' ) ) {
		$single = get_template_directory() . '/single-docs.php';
		if ( is_readable( $single ) ) {
			return $single;
		}
	}
	return $template;
}
add_filter( 'template_include', 'snap_docs_archive_template', 9999 );

/* ---------------------------------------------------------------------------
 * Render a captured content partial verbatim (the faithful page body).
 * Partials live in /inc/content/{slug}.html and are the exact <main> region of
 * the original page, with all URLs rewritten to root-relative.
 * (Docs are handled natively by the BetterDocs plugin, not here.)
 * ------------------------------------------------------------------------- */
function snap_render( $slug ) {
	$rel  = sanitize_file_name( $slug );
	$file = get_template_directory() . '/inc/content/' . $rel . '.html';
	if ( is_readable( $file ) ) {
		$html = file_get_contents( $file );
		// Sensa CMS: swap {{T:key}} -> editable text and {{I:key}} -> editable image URL.
		if ( false !== strpos( $html, '{{' ) ) {
			$html = preg_replace_callback(
				'/\{\{(T|I):([a-z0-9_]+)\}\}/',
				function ( $m ) {
					return 'T' === $m[1] ? sc_text( $m[2] ) : esc_url( sc_img( $m[2] ) );
				},
				$html
			);
		}
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
