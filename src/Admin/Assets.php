<?php

declare(strict_types=1);

namespace Siteunfold\Admin;

/**
 * Enqueues the admin stylesheet, only on the plugin's own screens.
 */
final class Assets {

	/**
	 * Hooks asset enqueueing for the plugin screen.
	 */
	public function register(): void {
		add_action(
			'admin_enqueue_scripts',
			static function ( string $hook ): void {
				if ( ToolsPage::HOOK_SUFFIX !== $hook && RevertPage::HOOK_SUFFIX !== $hook ) {
					return;
				}

				wp_enqueue_style(
					'siteunfold-admin',
					SITEUNFOLD_URL . 'assets/admin.css',
					array(),
					SITEUNFOLD_VERSION
				);
			}
		);
	}
}
