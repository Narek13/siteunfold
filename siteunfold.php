<?php
/**
 * Plugin Name:       Siteunfold – Convert Your Site to Multisite
 * Description:       Convert your site to Multisite with one click from Tools > Convert to Multisite, and revert it back to a single site.
 * Version:           1.2.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Narek Zakarian
 * Author URI:        https://profile.wordpress.org/nareknovembit
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       siteunfold
 * Domain Path:       /languages
 *
 * @package Siteunfold
 */

declare(strict_types=1);

// No direct access.
defined( 'ABSPATH' ) || exit;

if ( defined( 'SITEUNFOLD_VERSION' ) ) {
	return;
}

define( 'SITEUNFOLD_VERSION', '1.2.0' );
define( 'SITEUNFOLD_FILE', __FILE__ );
define( 'SITEUNFOLD_DIR', plugin_dir_path( __FILE__ ) );
define( 'SITEUNFOLD_URL', plugin_dir_url( __FILE__ ) );

/*
 * Autoloading.
 *
 * Prefer Composer's autoloader if the plugin has been built (vendor/ present).
 * Otherwise fall back to a tiny PSR-4 autoloader so the plugin runs as soon as
 * it's dropped into wp-content/plugins, no build step required.
 */
if ( is_readable( SITEUNFOLD_DIR . 'vendor/autoload.php' ) ) {
	require SITEUNFOLD_DIR . 'vendor/autoload.php';
} else {
	spl_autoload_register(
		static function ( string $class_name ): void {
			$prefix = 'Siteunfold\\';
			if ( ! str_starts_with( $class_name, $prefix ) ) {
				return;
			}
			$relative = substr( $class_name, strlen( $prefix ) );
			$path     = SITEUNFOLD_DIR . 'src/' . str_replace( '\\', '/', $relative ) . '.php';
			if ( is_readable( $path ) ) {
				require $path;
			}
		}
	);
}

use Siteunfold\Plugin;

// Boot once WordPress and all plugins are loaded.
add_action(
	'plugins_loaded',
	static function (): void {
		Plugin::instance()->boot();
	}
);
