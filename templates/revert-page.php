<?php
/**
 * Network Admin > Settings > Revert to Single Site.
 *
 * @package Siteunfold
 *
 * @var list<\Siteunfold\Conversion\PrerequisiteResult> $prerequisites
 * @var bool $canRevert
 * @var array{success: bool, message: string, warnings: list<string>}|null $notice
 * @var string $action
 */

declare(strict_types=1);

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables scoped to include context.

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap siteunfold-wrap">
	<h1><?php esc_html_e( 'Revert to Single Site', 'siteunfold' ); ?></h1>
	<p class="siteunfold-subtitle"><?php esc_html_e( 'Turn this network back into a single WordPress site. Possible while the network holds only its main site.', 'siteunfold' ); ?></p>

	<?php if ( null !== $notice && ! $notice['success'] ) : ?>
		<div class="notice notice-error siteunfold-notice">
			<p>
				<strong><?php esc_html_e( 'The revert did not complete.', 'siteunfold' ); ?></strong>
				<?php echo esc_html( $notice['message'] ); ?>
			</p>
		</div>
	<?php endif; ?>

	<div class="siteunfold-cards">

		<?php
		$ready = $canRevert;
		require __DIR__ . '/partials/prerequisites.php';
		?>

		<div class="siteunfold-card">
			<div class="siteunfold-card__header">
				<h2><?php esc_html_e( 'What happens', 'siteunfold' ); ?></h2>
			</div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( $action ); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">

				<div class="siteunfold-card__body">
					<ul class="siteunfold-steps">
						<li><?php esc_html_e( 'The multisite constants are removed from wp-config.php.', 'siteunfold' ); ?></li>
						<li><?php esc_html_e( 'The standard WordPress rewrite rules are restored in .htaccess.', 'siteunfold' ); ?></li>
						<li><?php esc_html_e( 'Network-activated plugins stay active on the site.', 'siteunfold' ); ?></li>
						<li><?php esc_html_e( 'The network tables (sites, network options, sign-ups) are deleted. Network settings are lost.', 'siteunfold' ); ?></li>
						<li><?php esc_html_e( 'Your posts, pages, media, users and site settings are not touched.', 'siteunfold' ); ?></li>
					</ul>
					<label class="siteunfold-confirm">
						<input type="checkbox" name="confirm_backup" value="1" required <?php disabled( ! $canRevert ); ?>>
						<?php esc_html_e( 'I have a backup of my files and database.', 'siteunfold' ); ?>
					</label>
				</div>

				<div class="siteunfold-card__footer">
					<p class="siteunfold-warning">
						<?php esc_html_e( 'You may have to log in again afterwards.', 'siteunfold' ); ?>
					</p>
					<button type="submit" class="button button-primary siteunfold-btn" <?php disabled( ! $canRevert ); ?>>
						<?php esc_html_e( 'Revert to Single Site', 'siteunfold' ); ?>
					</button>
					<?php if ( ! $canRevert ) : ?>
						<span class="siteunfold-btn-note"><?php esc_html_e( 'Resolve the failed checks above to enable the revert.', 'siteunfold' ); ?></span>
					<?php endif; ?>
				</div>
			</form>
		</div>

	</div>
</div>
