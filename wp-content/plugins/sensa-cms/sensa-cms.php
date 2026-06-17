<?php
/**
 * Plugin Name:       Sensa CMS
 * Plugin URI:        https://github.com/sensa-productions/sensa-cms
 * Description:       Lightweight in-place content editor for Sensa-family sites — edit page text, single images, hero videos/banners and galleries from wp-admin, without touching the design. Each site declares its editable fields via the `sensa_cms_config` filter in its theme.
 * Version:           1.0.0
 * Author:            Sensa Productions
 * Author URI:        https://sensa.digital
 * License:           GPL-2.0-or-later
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * @package sensa-cms
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SENSA_CMS_VERSION', '1.0.0' );
define( 'SENSA_CMS_FILE', __FILE__ );
define( 'SENSA_CMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'SENSA_CMS_SLUG', plugin_basename( __FILE__ ) );

/* Self-update from the GitHub repo (latest Release). */
require_once SENSA_CMS_DIR . 'includes/updater.php';

/*
 * Load the editor engine — but ONLY if a legacy `sensa-content` mu-plugin hasn't already
 * defined these helpers. This guard makes migration safe: with the old mu-plugin still
 * present, the engine stays dormant (no "cannot redeclare" fatal); once that mu-plugin is
 * removed, this engine takes over using the SAME option keys + helper names, so all
 * existing content + template calls keep working unchanged.
 */
if ( ! function_exists( 'sc_text' ) && ! function_exists( 'sc_img' ) ) {
	require_once SENSA_CMS_DIR . 'includes/engine.php';
}
