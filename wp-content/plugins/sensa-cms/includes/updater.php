<?php
/**
 * Sensa CMS — self-updater from GitHub.
 *
 * Lets the plugin update itself on every site straight from the GitHub repo. Bump the
 * `Version:` header in sensa-cms.php, push to `main`, and within ~12h (or on a manual
 * "Check for updates") every site offers the update in wp-admin → Plugins, like any plugin.
 *
 * Version source : the `Version:` header in sensa-cms.php on the `main` branch (raw).
 * Download       : the `main` branch zip from codeload.
 * Private repo   : define SENSA_CMS_GH_TOKEN in wp-config.php (a fine-grained read token) and
 *                  the updater will authenticate. PUBLIC repo = no token needed (simplest for client sites).
 *
 * @package sensa-cms
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SENSA_CMS_GH_OWNER', 'sensa-productions' );
define( 'SENSA_CMS_GH_REPO', 'sensa-cms' );
define( 'SENSA_CMS_GH_BRANCH', 'main' );

/** GitHub request args (adds auth header only if a token constant is set). */
function sensa_cms_gh_args() {
	$args = array( 'timeout' => 15, 'headers' => array( 'Accept' => 'application/vnd.github+json', 'User-Agent' => 'sensa-cms-updater' ) );
	if ( defined( 'SENSA_CMS_GH_TOKEN' ) && SENSA_CMS_GH_TOKEN ) {
		$args['headers']['Authorization'] = 'Bearer ' . SENSA_CMS_GH_TOKEN;
	}
	return $args;
}

/** Read the latest version from the plugin header on the main branch (cached 12h). */
function sensa_cms_remote_version() {
	$cached = get_transient( 'sensa_cms_remote_version' );
	if ( false !== $cached ) { return $cached; }
	$url  = sprintf( 'https://raw.githubusercontent.com/%s/%s/%s/sensa-cms.php', SENSA_CMS_GH_OWNER, SENSA_CMS_GH_REPO, SENSA_CMS_GH_BRANCH );
	$resp = wp_remote_get( $url, sensa_cms_gh_args() );
	$ver  = '';
	if ( ! is_wp_error( $resp ) && 200 === wp_remote_retrieve_response_code( $resp ) ) {
		if ( preg_match( '/^\s*\*\s*Version:\s*(.+)$/mi', wp_remote_retrieve_body( $resp ), $m ) ) {
			$ver = trim( $m[1] );
		}
	}
	set_transient( 'sensa_cms_remote_version', $ver, 12 * HOUR_IN_SECONDS );
	return $ver;
}

/** Branch zip download URL (codeload). */
function sensa_cms_download_url() {
	return sprintf( 'https://codeload.github.com/%s/%s/zip/refs/heads/%s', SENSA_CMS_GH_OWNER, SENSA_CMS_GH_REPO, SENSA_CMS_GH_BRANCH );
}

/** Inject an available update into WordPress's plugin update check. */
add_filter( 'pre_set_site_transient_update_plugins', function ( $transient ) {
	if ( empty( $transient ) || ! is_object( $transient ) ) { return $transient; }
	$remote = sensa_cms_remote_version();
	if ( $remote && version_compare( $remote, SENSA_CMS_VERSION, '>' ) ) {
		$item = array(
			'slug'        => 'sensa-cms',
			'plugin'      => SENSA_CMS_SLUG,
			'new_version' => $remote,
			'url'         => 'https://github.com/' . SENSA_CMS_GH_OWNER . '/' . SENSA_CMS_GH_REPO,
			'package'     => sensa_cms_download_url(),
		);
		$transient->response[ SENSA_CMS_SLUG ] = (object) $item;
	}
	return $transient;
} );

/** "View details" popup info. */
add_filter( 'plugins_api', function ( $res, $action, $args ) {
	if ( 'plugin_information' !== $action || empty( $args->slug ) || 'sensa-cms' !== $args->slug ) { return $res; }
	return (object) array(
		'name'          => 'Sensa CMS',
		'slug'          => 'sensa-cms',
		'version'       => sensa_cms_remote_version(),
		'author'        => '<a href="https://sensa.digital">Sensa Productions</a>',
		'homepage'      => 'https://github.com/' . SENSA_CMS_GH_OWNER . '/' . SENSA_CMS_GH_REPO,
		'download_link' => sensa_cms_download_url(),
		'sections'      => array( 'description' => 'In-place content editor for Sensa-family sites. Updates from the GitHub repo.' ),
	);
}, 10, 3 );

/** GitHub zips extract to a folder like "sensa-cms-main"; rename it back to "sensa-cms". */
add_filter( 'upgrader_source_selection', function ( $source, $remote_source, $upgrader, $hook_extra = array() ) {
	if ( empty( $hook_extra['plugin'] ) || SENSA_CMS_SLUG !== $hook_extra['plugin'] ) { return $source; }
	global $wp_filesystem;
	$desired = trailingslashit( $remote_source ) . 'sensa-cms';
	if ( $source !== trailingslashit( $desired ) && $wp_filesystem && $wp_filesystem->move( $source, $desired ) ) {
		return trailingslashit( $desired );
	}
	return $source;
}, 10, 4 );

/** Clear the cached version when WP checks for updates, so a manual check is fresh. */
add_action( 'upgrader_process_complete', function () { delete_transient( 'sensa_cms_remote_version' ); } );
