<?php
/**
 * Sensa CMS — editor engine (generic; site content comes from the `sensa_cms_config` filter).
 *
 * Keeps the SAME helper names (sc_text/sc_img/sensa_videos/sensa_photos) and SAME option keys
 * (sensa_text/sensa_images/sensa_content) as the original per-site editor, so existing sites'
 * saved data and template calls keep working with no migration.
 *
 * Each site (its theme) supplies its editable fields via:
 *   add_filter( 'sensa_cms_config', function ( $c ) { return array( ...site config... ); } );
 * See README.md / config-example.php for the shape.
 *
 * @package sensa-cms
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------------------------------------------------------------------------
 * Per-site config (filter-provided), normalised + cached.
 * ------------------------------------------------------------------------- */
function sensa_cms_config() {
	static $c = null;
	if ( null === $c ) {
		$c = apply_filters( 'sensa_cms_config', array() );
		if ( ! is_array( $c ) ) { $c = array(); }
		$c = wp_parse_args( $c, array( 'bespoke_slugs' => array(), 'text' => array(), 'images' => array(), 'galleries' => array() ) );
		$c['text']      = wp_parse_args( (array) $c['text'], array( 'groups' => array(), 'slug_groups' => array(), 'slug_prefixes' => array() ) );
		$c['images']    = wp_parse_args( (array) $c['images'], array( 'groups' => array(), 'slug_groups' => array() ) );
		$c['galleries'] = wp_parse_args( (array) $c['galleries'], array( 'video' => array(), 'photo' => array() ) );
	}
	return $c;
}

/* ---------------------------------------------------------------------------
 * Galleries — accessors used by theme templates.
 * ------------------------------------------------------------------------- */
function sensa_content_store() {
	$o = get_option( 'sensa_content' );
	if ( ! is_array( $o ) ) { $o = array(); }
	if ( empty( $o['videos'] ) || ! is_array( $o['videos'] ) ) { $o['videos'] = array(); }
	if ( empty( $o['photos'] ) || ! is_array( $o['photos'] ) ) { $o['photos'] = array(); }
	return $o;
}
function sensa_videos( $slug, $default ) {
	$o = sensa_content_store();
	return ( ! empty( $o['videos'][ $slug ] ) && is_array( $o['videos'][ $slug ] ) ) ? $o['videos'][ $slug ] : $default;
}
function sensa_photos( $slug, $default ) {
	$o = sensa_content_store();
	return ( ! empty( $o['photos'][ $slug ] ) && is_array( $o['photos'][ $slug ] ) ) ? $o['photos'][ $slug ] : $default;
}

/* ===========================================================================
 * PAGE TEXT — registry from config; templates call sc_text($key).
 * ========================================================================= */
function sensa_text_registry() { return sensa_cms_config()['text']['groups']; }
function sensa_text_defaults() {
	static $f = null;
	if ( null === $f ) {
		$f = array();
		foreach ( sensa_text_registry() as $pg ) {
			if ( empty( $pg['fields'] ) ) { continue; }
			foreach ( $pg['fields'] as $fl ) { $f[ $fl['k'] ] = $fl['d']; }
		}
	}
	return $f;
}
function sc_text( $key ) {
	$o = get_option( 'sensa_text' );
	if ( is_array( $o ) && isset( $o[ $key ] ) && '' !== $o[ $key ] ) { return $o[ $key ]; }
	$d = sensa_text_defaults();
	return isset( $d[ $key ] ) ? $d[ $key ] : '';
}

/* Template-driven pages whose visible content is NOT the block editor body. */
function sensa_bespoke_slugs() { return (array) sensa_cms_config()['bespoke_slugs']; }

/* Which text fields belong to a given page slug (for the on-page edit panel). */
function sensa_text_fields_for_slug( $slug ) {
	$cfg    = sensa_cms_config()['text'];
	$groups = $cfg['slug_groups'];
	$prefix = $cfg['slug_prefixes'];
	$out    = array();
	foreach ( sensa_text_registry() as $gkey => $pg ) {
		if ( empty( $pg['fields'] ) ) { continue; }
		foreach ( $pg['fields'] as $fl ) {
			if ( isset( $groups[ $slug ] ) && $gkey === $groups[ $slug ] ) { $out[] = $fl; }
			elseif ( isset( $prefix[ $slug ] ) && 0 === strpos( $fl['k'], $prefix[ $slug ] ) ) { $out[] = $fl; }
		}
	}
	return $out;
}

/* On-page editor panel: edit this page's copy right on its Edit screen. */
add_action( 'add_meta_boxes', function () {
	$p = get_post();
	if ( ! $p || 'page' !== $p->post_type ) { return; }
	if ( empty( sensa_text_fields_for_slug( $p->post_name ) ) ) { return; }
	add_meta_box( 'sensa_pagetext', '✏️ Sensa CMS — Page Text (edit this page here)', 'sensa_text_metabox', 'page', 'normal', 'high' );
} );

function sensa_text_metabox( $post ) {
	wp_nonce_field( 'sensa_text_mb', 'sensa_text_mb_nonce' );
	$store  = get_option( 'sensa_text' );
	if ( ! is_array( $store ) ) { $store = array(); }
	$fields = sensa_text_fields_for_slug( $post->post_name );
	echo '<p class="description" style="margin:0 0 14px">Edit the live copy for this page below, then click <strong>Update</strong>. Basic HTML is allowed (e.g. <code>&lt;span class="cyan"&gt;word&lt;/span&gt;</code>). Clear a field to restore its original. The main block-editor area above is not used by this page.</p>';
	foreach ( $fields as $fl ) {
		$val = isset( $store[ $fl['k'] ] ) && '' !== $store[ $fl['k'] ] ? $store[ $fl['k'] ] : $fl['d'];
		echo '<p style="margin:0 0 14px"><label style="font-weight:600;display:block;margin-bottom:4px">' . esc_html( $fl['l'] ) . '</label>';
		if ( ! empty( $fl['ta'] ) ) {
			echo '<textarea name="sctext[' . esc_attr( $fl['k'] ) . ']" rows="3" style="width:100%;font-family:inherit">' . esc_textarea( $val ) . '</textarea>';
		} else {
			echo '<input type="text" name="sctext[' . esc_attr( $fl['k'] ) . ']" value="' . esc_attr( $val ) . '" style="width:100%">';
		}
		echo '</p>';
	}
}

add_action( 'save_post_page', function ( $post_id ) {
	if ( ! isset( $_POST['sensa_text_mb_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sensa_text_mb_nonce'] ) ), 'sensa_text_mb' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_page', $post_id ) ) { return; }
	if ( empty( $_POST['sctext'] ) || ! is_array( $_POST['sctext'] ) ) { return; }
	$defaults = sensa_text_defaults();
	$store    = get_option( 'sensa_text' );
	if ( ! is_array( $store ) ) { $store = array(); }
	foreach ( wp_unslash( $_POST['sctext'] ) as $k => $v ) {
		if ( ! isset( $defaults[ $k ] ) ) { continue; }
		$store[ $k ] = wp_kses_post( $v );
	}
	update_option( 'sensa_text', $store );
} );

/* ===========================================================================
 * PAGE IMAGES — registry from config; templates call sc_img($key).
 * ========================================================================= */
function sensa_img_registry() { return sensa_cms_config()['images']['groups']; }
function sensa_img_defaults() {
	static $f = null;
	if ( null === $f ) {
		$f = array();
		foreach ( sensa_img_registry() as $pg ) {
			if ( empty( $pg['fields'] ) ) { continue; }
			foreach ( $pg['fields'] as $fl ) { $f[ $fl['k'] ] = $fl['d']; }
		}
	}
	return $f;
}
function sc_img( $key ) {
	$o = get_option( 'sensa_images' );
	if ( is_array( $o ) && isset( $o[ $key ] ) && '' !== $o[ $key ] ) { return $o[ $key ]; }
	$d = sensa_img_defaults();
	return isset( $d[ $key ] ) ? $d[ $key ] : '';
}
function sensa_img_fields_for_slug( $slug ) {
	$groups = sensa_cms_config()['images']['slug_groups'];
	if ( ! isset( $groups[ $slug ] ) ) { return array(); }
	$reg = sensa_img_registry();
	return isset( $reg[ $groups[ $slug ] ]['fields'] ) ? $reg[ $groups[ $slug ] ]['fields'] : array();
}

add_action( 'add_meta_boxes', function () {
	$p = get_post();
	if ( ! $p || 'page' !== $p->post_type ) { return; }
	if ( empty( sensa_img_fields_for_slug( $p->post_name ) ) ) { return; }
	add_meta_box( 'sensa_pageimg', '🖼️ Sensa CMS — Images (replace images on this page)', 'sensa_img_metabox', 'page', 'normal', 'default' );
} );

/* Load the WP media picker on bespoke page edit screens. */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) { return; }
	$p = get_post();
	if ( ! $p || 'page' !== $p->post_type || empty( sensa_img_fields_for_slug( $p->post_name ) ) ) { return; }
	wp_enqueue_media();
} );

function sensa_img_metabox( $post ) {
	wp_nonce_field( 'sensa_img_mb', 'sensa_img_mb_nonce' );
	$store = get_option( 'sensa_images' );
	if ( ! is_array( $store ) ) { $store = array(); }
	echo '<p class="description" style="margin:0 0 14px">Replace any image below: click <strong>Choose image</strong> to upload or pick from the Media Library. Saves instantly. Use <strong>Reset</strong> to restore the original. Multi-image galleries &amp; the video showreel live in the <a href="' . esc_url( admin_url( 'admin.php?page=sensa-content' ) ) . '">Sensa CMS</a> menu.</p>';
	echo '<div class="sc-imgs" style="display:flex;flex-wrap:wrap;gap:18px">';
	foreach ( sensa_img_fields_for_slug( $post->post_name ) as $fl ) {
		$k    = esc_attr( $fl['k'] );
		$ovr  = isset( $store[ $fl['k'] ] ) ? $store[ $fl['k'] ] : '';
		$show = '' !== $ovr ? $ovr : $fl['d'];
		echo '<div class="sc-img" data-k="' . $k . '" data-default="' . esc_url( $fl['d'] ) . '" style="width:210px">';
		echo '<label style="font-weight:600;display:block;margin-bottom:4px">' . esc_html( $fl['l'] ) . '</label>';
		$ph = 'display:none;width:100%;height:100%;align-items:center;justify-content:center;color:#787c82;font-size:12px;text-align:center;padding:0 10px';
		echo '<div class="sc-img-prev" style="width:210px;height:118px;border:1px dashed #c3c4c7;border-radius:4px;overflow:hidden;background:#f0f0f1">'
			. '<img src="' . esc_url( $show ) . '" style="width:100%;height:100%;object-fit:cover;' . ( '' === $show ? 'display:none' : '' ) . '" alt="" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">'
			. '<span class="sc-img-none" style="' . $ph . ( '' === $show ? ';display:flex' : '' ) . '">None set (optional)</span></div>';
		echo '<input type="hidden" name="scimg[' . $k . ']" value="' . esc_attr( $ovr ) . '">';
		echo '<p style="margin:6px 0 0"><button type="button" class="button sc-img-pick">Choose image</button> <button type="button" class="button-link sc-img-reset" style="color:#b32d2e;margin-left:6px">Reset</button> <span class="sc-img-ok" style="font-size:12px;margin-left:4px"></span></p>';
		echo '</div>';
	}
	echo '</div>';
	?>
	<script>
	jQuery( function ( $ ) {
		var SC_AJAX = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
		var SC_NONCE = '<?php echo esc_js( wp_create_nonce( 'sensa_img_ajax' ) ); ?>';
		function scImgSave( $b, url ) {
			var $ok = $b.find( '.sc-img-ok' );
			$ok.text( 'Saving…' ).css( 'color', '#787c82' );
			$.post( SC_AJAX, { action: 'sensa_img_save', nonce: SC_NONCE, key: $b.attr( 'data-k' ), url: url } )
				.done( function () { $ok.text( url ? 'Saved ✓ (live)' : 'Reset ✓ (live)' ).css( 'color', '#1a7f37' ); } )
				.fail( function () { $ok.text( 'Save failed — click Update' ).css( 'color', '#b32d2e' ); } );
		}
		$( '.sc-img-pick' ).on( 'click', function () {
			var $b = $( this ).closest( '.sc-img' );
			var f = wp.media( { title: 'Choose image', multiple: false, library: { type: 'image' } } );
			f.on( 'select', function () {
				var a = f.state().get( 'selection' ).first().toJSON();
				var url = ( a.sizes && a.sizes.large ) ? a.sizes.large.url : a.url;
				$b.find( 'input' ).val( url );
				$b.find( '.sc-img-prev img' ).attr( 'src', url ).css( 'display', '' );
				$b.find( '.sc-img-none' ).css( 'display', 'none' );
				scImgSave( $b, url );
			} );
			f.open();
		} );
		$( '.sc-img-reset' ).on( 'click', function () {
			var $b = $( this ).closest( '.sc-img' ), def = $b.attr( 'data-default' );
			$b.find( 'input' ).val( '' );
			if ( def ) {
				$b.find( '.sc-img-prev img' ).attr( 'src', def ).css( 'display', '' );
				$b.find( '.sc-img-none' ).css( 'display', 'none' );
			} else {
				$b.find( '.sc-img-prev img' ).attr( 'src', '' ).css( 'display', 'none' );
				$b.find( '.sc-img-none' ).css( 'display', 'flex' );
			}
			scImgSave( $b, '' );
		} );
	} );
	</script>
	<?php
}

add_action( 'save_post_page', function ( $post_id ) {
	if ( ! isset( $_POST['sensa_img_mb_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sensa_img_mb_nonce'] ) ), 'sensa_img_mb' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_page', $post_id ) ) { return; }
	if ( ! isset( $_POST['scimg'] ) || ! is_array( $_POST['scimg'] ) ) { return; }
	$defaults = sensa_img_defaults();
	$store    = get_option( 'sensa_images' );
	if ( ! is_array( $store ) ) { $store = array(); }
	foreach ( wp_unslash( $_POST['scimg'] ) as $k => $v ) {
		if ( ! isset( $defaults[ $k ] ) ) { continue; }
		$v = esc_url_raw( trim( (string) $v ) );
		if ( '' === $v ) { unset( $store[ $k ] ); } else { $store[ $k ] = $v; }
	}
	update_option( 'sensa_images', $store );
} );

/* Purge WP Engine caches so editor option-saves (which don't trigger a post-save purge) show immediately. */
function sensa_purge_caches() {
	if ( class_exists( 'WpeCommon' ) ) {
		if ( method_exists( 'WpeCommon', 'purge_varnish_cache_all' ) ) { @WpeCommon::purge_varnish_cache_all(); }
		if ( method_exists( 'WpeCommon', 'clear_cdn_cache' ) ) { @WpeCommon::clear_cdn_cache(); }
	}
}

/* Instant-save one image slot straight from the picker — no page Update needed, and purge so it shows live. */
add_action( 'wp_ajax_sensa_img_save', function () {
	if ( ! current_user_can( 'edit_pages' ) ) { wp_send_json_error( 'forbidden', 403 ); }
	check_ajax_referer( 'sensa_img_ajax', 'nonce' );
	$key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$url = isset( $_POST['url'] ) ? esc_url_raw( trim( (string) wp_unslash( $_POST['url'] ) ) ) : '';
	$defaults = sensa_img_defaults();
	if ( ! isset( $defaults[ $key ] ) ) { wp_send_json_error( 'bad key' ); }
	$store = get_option( 'sensa_images' );
	if ( ! is_array( $store ) ) { $store = array(); }
	if ( '' === $url ) { unset( $store[ $key ] ); } else { $store[ $key ] = $url; }
	update_option( 'sensa_images', $store );
	sensa_purge_caches();
	wp_send_json_success();
} );

/* Bespoke pages: use the classic editor so the panels are front-and-centre. */
add_filter( 'use_block_editor_for_post', function ( $use, $post ) {
	if ( $post && 'page' === $post->post_type && in_array( $post->post_name, sensa_bespoke_slugs(), true ) ) { return false; }
	return $use;
}, 10, 2 );

/* On those edit screens: hide the unused content/body editor + add a clear helper notice. */
add_action( 'admin_head-post.php', function () {
	$p = get_post();
	if ( ! $p || 'page' !== $p->post_type || ! in_array( $p->post_name, sensa_bespoke_slugs(), true ) ) { return; }
	echo '<style>'
		. '#postdivrich,#wp-content-editor-tools,#postexcerpt,#screen-meta,#screen-meta-links{display:none!important}'
		. '#sensa_pagetext,#sensa_pageimg{border:2px solid #00b3d6}'
		. '#sensa_pagetext .hndle,#sensa_pageimg .hndle{background:#04222f;color:#fff;font-size:15px}'
		. '#sensa_pagetext .hndle:after,#sensa_pageimg .hndle:after{color:#fff}'
		. '</style>';
} );

add_action( 'admin_notices', function () {
	$s = get_current_screen();
	if ( ! $s || 'post' !== $s->base || 'page' !== $s->post_type ) { return; }
	$p = get_post();
	if ( ! $p || ! in_array( $p->post_name, sensa_bespoke_slugs(), true ) ) { return; }
	echo '<div class="notice notice-info" style="border-left-color:#00b3d6"><p><strong>Sensa CMS custom page.</strong> '
		. 'Edit this page&rsquo;s wording in the <strong>Page Text</strong> box below, and swap its pictures in the <strong>Images</strong> box, then click <strong>Update</strong> (top-right). '
		. 'Multi-image galleries &amp; the video showreel live in the <a href="' . esc_url( admin_url( 'admin.php?page=sensa-content' ) ) . '">Sensa CMS</a> menu.</p></div>';
} );

/* Friendly redirect: bare /wp-admin/sensa-content or /wp-admin/sensa-text → the real admin pages. */
add_action( 'template_redirect', function () {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) { return; }
	$path = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
	if ( preg_match( '~/wp-admin/(sensa-content|sensa-text)/?$~i', $path, $m ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=' . $m[1] ) );
		exit;
	}
} );

/* Extract an 11-char YouTube ID from a URL or bare ID. */
function sensa_yt_id( $s ) {
	$s = trim( $s );
	if ( '' === $s ) { return ''; }
	if ( preg_match( '~(?:youtu\.be/|v=|embed/|shorts/|live/)([A-Za-z0-9_-]{11})~', $s, $m ) ) { return $m[1]; }
	if ( preg_match( '~^[A-Za-z0-9_-]{11}$~', $s ) ) { return $s; }
	return '';
}

/* Galleries registry (from config). */
function sensa_content_registry() {
	$g = sensa_cms_config()['galleries'];
	return array( 'video' => (array) $g['video'], 'photo' => (array) $g['photo'] );
}

/* ---------------------------------------------------------------------------
 * Admin: top-level "Sensa CMS" menu (Galleries + Page Text).
 * ------------------------------------------------------------------------- */
add_action( 'admin_menu', function () {
	add_menu_page( 'Sensa CMS', 'Sensa CMS', 'edit_pages', 'sensa-content', 'sensa_content_render', 'dashicons-edit', 3 );
	add_submenu_page( 'sensa-content', 'Galleries', 'Galleries', 'edit_pages', 'sensa-content', 'sensa_content_render' );
	add_submenu_page( 'sensa-content', 'Page Text', 'Page Text', 'edit_pages', 'sensa-text', 'sensa_text_render' );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'toplevel_page_sensa-content' !== $hook ) { return; }
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
} );

add_action( 'admin_post_sensa_text_save', function () {
	if ( ! current_user_can( 'edit_pages' ) ) { wp_die( 'Not allowed' ); }
	check_admin_referer( 'sensa_text_save' );
	$defaults = sensa_text_defaults();
	$store    = array();
	if ( ! empty( $_POST['t'] ) && is_array( $_POST['t'] ) ) {
		foreach ( wp_unslash( $_POST['t'] ) as $k => $v ) {
			if ( ! isset( $defaults[ $k ] ) ) { continue; }
			$store[ $k ] = wp_kses_post( $v );
		}
	}
	update_option( 'sensa_text', $store );
	sensa_purge_caches();
	wp_safe_redirect( add_query_arg( array( 'page' => 'sensa-text', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
	exit;
} );

function sensa_text_render() {
	$reg   = sensa_text_registry();
	$store = get_option( 'sensa_text' );
	if ( ! is_array( $store ) ) { $store = array(); }
	?>
	<div class="wrap">
		<h1>Sensa CMS — Page Text</h1>
		<?php if ( isset( $_GET['updated'] ) ) { echo '<div class="notice notice-success is-dismissible"><p>Saved. Changes are live.</p></div>'; } ?>
		<p class="description">Edit the headlines and copy on the bespoke pages. Basic HTML (like <code>&lt;span class="cyan"&gt;</code> for a coloured word, <code>&lt;em&gt;</code>, <code>&lt;br&gt;</code>) is allowed. Clear a field to restore its original.</p>
		<?php if ( empty( $reg ) ) { echo '<p><em>No editable text configured for this site yet (add a <code>sensa_cms_config</code> filter in the theme).</em></p></div>'; return; } ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="sensa_text_save">
			<?php wp_nonce_field( 'sensa_text_save' ); ?>
			<?php foreach ( $reg as $pg ) : ?>
				<h2 class="sc-sec" style="margin-top:26px;border-bottom:1px solid #dcdcde;padding-bottom:6px"><?php echo esc_html( $pg['label'] ); ?></h2>
				<table class="form-table" role="presentation"><tbody>
				<?php foreach ( $pg['fields'] as $fl ) :
					$val = isset( $store[ $fl['k'] ] ) && '' !== $store[ $fl['k'] ] ? $store[ $fl['k'] ] : $fl['d']; ?>
					<tr>
						<th scope="row" style="width:200px"><label for="t_<?php echo esc_attr( $fl['k'] ); ?>"><?php echo esc_html( $fl['l'] ); ?></label></th>
						<td>
						<?php if ( ! empty( $fl['ta'] ) ) : ?>
							<textarea id="t_<?php echo esc_attr( $fl['k'] ); ?>" name="t[<?php echo esc_attr( $fl['k'] ); ?>]" rows="3" class="large-text" style="font-family:inherit"><?php echo esc_textarea( $val ); ?></textarea>
						<?php else : ?>
							<input type="text" id="t_<?php echo esc_attr( $fl['k'] ); ?>" name="t[<?php echo esc_attr( $fl['k'] ); ?>]" value="<?php echo esc_attr( $val ); ?>" class="large-text">
						<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody></table>
			<?php endforeach; ?>
			<p><button class="button button-primary button-large">Save changes</button></p>
		</form>
	</div>
	<?php
}

add_action( 'admin_post_sensa_content_save', function () {
	if ( ! current_user_can( 'edit_pages' ) ) { wp_die( 'Not allowed' ); }
	check_admin_referer( 'sensa_content_save' );
	$reg   = sensa_content_registry();
	$store = array( 'videos' => array(), 'photos' => array() );
	if ( ! empty( $_POST['video'] ) && is_array( $_POST['video'] ) ) {
		foreach ( wp_unslash( $_POST['video'] ) as $slug => $raw ) {
			if ( ! isset( $reg['video'][ $slug ] ) ) { continue; }
			$ids = array();
			foreach ( preg_split( '~\r?\n~', (string) $raw ) as $ln ) {
				$id = sensa_yt_id( $ln );
				if ( '' !== $id ) { $ids[] = $id; }
			}
			$store['videos'][ $slug ] = $ids;
		}
	}
	if ( ! empty( $_POST['photo'] ) && is_array( $_POST['photo'] ) ) {
		foreach ( wp_unslash( $_POST['photo'] ) as $slug => $raw ) {
			if ( ! isset( $reg['photo'][ $slug ] ) ) { continue; }
			$urls = array();
			foreach ( preg_split( '~\r?\n~', (string) $raw ) as $ln ) {
				$u = esc_url_raw( trim( $ln ) );
				if ( '' !== $u ) { $urls[] = $u; }
			}
			$store['photos'][ $slug ] = $urls;
		}
	}
	update_option( 'sensa_content', $store );
	sensa_purge_caches();
	wp_safe_redirect( add_query_arg( array( 'page' => 'sensa-content', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
	exit;
} );

function sensa_content_render() {
	$reg   = sensa_content_registry();
	$store = sensa_content_store();
	?>
	<div class="wrap sc-wrap">
		<h1>Sensa CMS — Galleries</h1>
		<?php if ( isset( $_GET['updated'] ) ) { echo '<div class="notice notice-success is-dismissible"><p>Saved. Changes are live.</p></div>'; } ?>
		<p class="description">Reorder by dragging. Add a YouTube link or upload images. Remove with ×. <strong>Empty a gallery to restore its original.</strong> Page design stays in the theme.</p>
		<?php if ( empty( $reg['video'] ) && empty( $reg['photo'] ) ) { echo '<p><em>No galleries configured for this site yet (add a <code>sensa_cms_config</code> filter in the theme).</em></p></div>'; return; } ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="sensa_content_save">
			<?php wp_nonce_field( 'sensa_content_save' ); ?>

			<?php if ( ! empty( $reg['video'] ) ) : ?>
			<h2 class="sc-sec">Video galleries</h2>
			<?php foreach ( $reg['video'] as $slug => $info ) :
				$cur = ( ! empty( $store['videos'][ $slug ] ) ) ? $store['videos'][ $slug ] : $info['default']; ?>
				<div class="sc-gal" data-type="video">
					<h3><?php echo esc_html( $info['label'] ); ?> <code><?php echo esc_html( $slug ); ?></code></h3>
					<ul class="sc-list">
						<?php foreach ( $cur as $id ) : ?>
							<li data-val="<?php echo esc_attr( $id ); ?>"><img src="<?php echo esc_url( 'https://img.youtube.com/vi/' . $id . '/default.jpg' ); ?>" alt=""><span><?php echo esc_html( $id ); ?></span><button type="button" class="sc-rm" aria-label="Remove">&times;</button></li>
						<?php endforeach; ?>
					</ul>
					<div class="sc-add"><input type="text" class="regular-text" placeholder="Paste a YouTube link or 11-char ID"><button type="button" class="button sc-addvid">Add video</button></div>
					<textarea class="sc-data" name="video[<?php echo esc_attr( $slug ); ?>]" hidden><?php echo esc_textarea( implode( "\n", $cur ) ); ?></textarea>
				</div>
			<?php endforeach; ?>
			<?php endif; ?>

			<?php if ( ! empty( $reg['photo'] ) ) : ?>
			<h2 class="sc-sec">Photo galleries</h2>
			<?php foreach ( $reg['photo'] as $slug => $info ) :
				$cur = ( ! empty( $store['photos'][ $slug ] ) ) ? $store['photos'][ $slug ] : $info['default']; ?>
				<div class="sc-gal" data-type="photo">
					<h3><?php echo esc_html( $info['label'] ); ?> <code><?php echo esc_html( $slug ); ?></code></h3>
					<ul class="sc-list sc-photos">
						<?php foreach ( $cur as $url ) : ?>
							<li data-val="<?php echo esc_attr( $url ); ?>"><img src="<?php echo esc_url( $url ); ?>" alt=""><button type="button" class="sc-rm" aria-label="Remove">&times;</button></li>
						<?php endforeach; ?>
					</ul>
					<div class="sc-add"><button type="button" class="button sc-addimg">Add / upload images</button></div>
					<textarea class="sc-data" name="photo[<?php echo esc_attr( $slug ); ?>]" hidden><?php echo esc_textarea( implode( "\n", $cur ) ); ?></textarea>
				</div>
			<?php endforeach; ?>
			<?php endif; ?>

			<p><button class="button button-primary button-large">Save changes</button></p>
		</form>
	</div>
	<style>
	.sc-gal{background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:14px 16px;margin:0 0 16px;max-width:1100px}
	.sc-gal h3{margin:0 0 10px}.sc-gal code{font-size:11px;color:#777;font-weight:400}
	.sc-list{display:flex;flex-wrap:wrap;gap:10px;margin:0 0 10px;padding:0;list-style:none;min-height:20px}
	.sc-list li{position:relative;border:1px solid #dcdcde;border-radius:4px;background:#f6f7f7;cursor:grab;width:150px;overflow:hidden}
	.sc-list li img{display:block;width:150px;height:84px;object-fit:cover}
	.sc-list li span{display:block;font:11px/1.4 monospace;padding:4px 6px;color:#444;word-break:break-all}
	.sc-photos li{width:120px}.sc-photos li img{width:120px;height:90px}
	.sc-rm{position:absolute;top:3px;right:3px;width:22px;height:22px;border:0;border-radius:50%;background:rgba(0,0,0,.65);color:#fff;font-size:15px;line-height:1;cursor:pointer}
	.sc-add{display:flex;gap:8px;align-items:center}.sc-sec{margin-top:26px;border-bottom:1px solid #dcdcde;padding-bottom:6px}
	.sc-list .ui-sortable-placeholder{visibility:visible!important;background:#e0f3f8;border:1px dashed #00a0d2;width:150px;height:84px}
	</style>
	<script>
	jQuery(function($){
		function ser($g){var v=[];$g.find('.sc-list li').each(function(){v.push($(this).attr('data-val'));});$g.find('.sc-data').val(v.join('\n'));}
		function ytid(s){var m=s.match(/(?:youtu\.be\/|v=|embed\/|shorts\/|live\/)([A-Za-z0-9_-]{11})/);if(m)return m[1];m=s.match(/^[A-Za-z0-9_-]{11}$/);return m?s.trim():'';}
		$('.sc-list').sortable({items:'> li',tolerance:'pointer',update:function(){ser($(this).closest('.sc-gal'));}});
		$('.sc-addvid').on('click',function(){var $g=$(this).closest('.sc-gal'),$i=$g.find('.sc-add input'),raw=$i.val().trim();if(!raw)return;var id=ytid(raw);if(!id){alert('Could not read a YouTube ID from that.');return;}$g.find('.sc-list').append('<li data-val="'+id+'"><img src="https://img.youtube.com/vi/'+id+'/default.jpg" alt=""><span>'+id+'</span><button type="button" class="sc-rm">&times;</button></li>');$i.val('');ser($g);});
		$('.sc-addimg').on('click',function(){var $g=$(this).closest('.sc-gal');var f=wp.media({title:'Select or upload images',multiple:true,library:{type:'image'}});f.on('select',function(){f.state().get('selection').each(function(a){var u=a.toJSON().sizes&&a.toJSON().sizes.large?a.toJSON().sizes.large.url:a.toJSON().url;$g.find('.sc-list').append('<li data-val="'+u+'"><img src="'+u+'" alt=""><button type="button" class="sc-rm">&times;</button></li>');});ser($g);});f.open();});
		$(document).on('click','.sc-rm',function(){var $g=$(this).closest('.sc-gal');$(this).closest('li').remove();ser($g);});
		$('form').on('submit',function(){$('.sc-gal').each(function(){ser($(this));});});
	});
	</script>
	<?php
}
