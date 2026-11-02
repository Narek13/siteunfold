<?php
/**
 * Uninstall — runs only when the plugin is deleted from the WordPress admin.
 *
 * The plugin stores no settings. The only data it leaves behind is the
 * short-lived, per-user result notice, so that is all this removes.
 *
 * The network itself (wp-config.php constants, .htaccess rules and network
 * tables) is deliberately left alone: it is the site's configuration now, not
 * plugin data, and removing it would break every site on the network.
 *
 * @package Siteunfold
 */

declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Current prefix, and the one versions 1.0 and 1.1 used when the plugin was called "One Click Multisite".
foreach ( array( 'siteunfold_notice_', 'one_click_multisite_notice_' ) as $siteunfold_prefix ) {
	$siteunfold_like = $wpdb->esc_like( '_transient_' . $siteunfold_prefix ) . '%';
	$siteunfold_ttl  = $wpdb->esc_like( '_transient_timeout_' . $siteunfold_prefix ) . '%';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one-off cleanup of per-user transients; no API deletes by prefix.
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", $siteunfold_like, $siteunfold_ttl ) );
}
