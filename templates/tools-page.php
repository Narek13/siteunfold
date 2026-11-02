<?php
/**
 * Tools > Convert to Multisite.
 *
 * @package Siteunfold
 *
 * @var list<\Siteunfold\Conversion\PrerequisiteResult> $prerequisites
 * @var bool $canConvert
 * @var bool $subdomainAllowed
 * @var bool $subdirectoryAllowed
 * @var array{success: bool, message: string, warnings: list<string>}|null $notice
 * @var array{success: bool, message: string, warnings: list<string>}|null $revertNotice
 * @var string $action
 */

declare(strict_types=1);

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables scoped to include context.

defined( 'ABSPATH' ) || exit;

// Pre-select whichever network type is allowed; sub-directories when both are.
$preferSubdomain = $subdomainAllowed && ! $subdirectoryAllowed;
?>
<div class="wrap siteunfold-wrap">
	<h1><?php esc_html_e( 'Convert to Multisite', 'siteunfold' ); ?></h1>
	<p class="siteunfold-subtitle"><?php esc_html_e( 'Convert this single-site WordPress installation into a multisite network.', 'siteunfold' ); ?></p>

	<?php if ( null !== $notice && ! $notice['success'] ) : ?>
		<div class="notice notice-error siteunfold-notice">
			<p>
				<strong><?php esc_html_e( 'The conversion did not complete.', 'siteunfold' ); ?></strong>
				<?php echo esc_html( $notice['message'] ); ?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( null !== $revertNotice && $revertNotice['success'] ) : ?>
		<div class="notice notice-success siteunfold-notice">
			<p>
				<strong><?php esc_html_e( 'Reverted to a single site.', 'siteunfold' ); ?></strong>
				<?php esc_html_e( 'The network settings and tables have been removed. You can convert again at any time.', 'siteunfold' ); ?>
			</p>
		</div>
		<?php foreach ( $revertNotice['warnings'] as $warning ) : ?>
			<div class="notice notice-warning siteunfold-notice"><p><?php echo esc_html( $warning ); ?></p></div>
		<?php endforeach; ?>
	<?php endif; ?>

	<div class="siteunfold-cards">

		<?php
		$ready = $canConvert;
		require __DIR__ . '/partials/prerequisites.php';
		?>

		<div class="siteunfold-card">
			<div class="siteunfold-card__header">
				<h2 id="siteunfold-network-type"><?php esc_html_e( 'Network Type', 'siteunfold' ); ?></h2>
			</div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( $action ); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">

				<div class="siteunfold-card__body">
					<fieldset class="siteunfold-options" aria-labelledby="siteunfold-network-type">
						<label class="siteunfold-option<?php echo $subdirectoryAllowed ? '' : ' siteunfold-option--disabled'; ?>">
							<input type="radio" name="subdomain_install" value="0" <?php checked( ! $preferSubdomain ); ?> <?php disabled( ! $subdirectoryAllowed ); ?>>
							<span class="siteunfold-option__title"><?php esc_html_e( 'Sub-directories', 'siteunfold' ); ?></span>
							<span class="siteunfold-option__example"><?php esc_html_e( 'example.com/site1', 'siteunfold' ); ?></span>
						</label>
						<label class="siteunfold-option<?php echo $subdomainAllowed ? '' : ' siteunfold-option--disabled'; ?>">
							<input type="radio" name="subdomain_install" value="1" <?php checked( $preferSubdomain ); ?> <?php disabled( ! $subdomainAllowed ); ?>>
							<span class="siteunfold-option__title"><?php esc_html_e( 'Sub-domains', 'siteunfold' ); ?></span>
							<span class="siteunfold-option__example"><?php esc_html_e( 'site1.example.com', 'siteunfold' ); ?></span>
							<span class="siteunfold-option__note"><?php esc_html_e( 'Requires wildcard DNS.', 'siteunfold' ); ?></span>
						</label>
					</fieldset>
				</div>

				<div class="siteunfold-card__footer">
					<p class="siteunfold-warning">
						<?php esc_html_e( 'This changes wp-config.php and .htaccess and creates the network database tables. Back up your files and database before converting.', 'siteunfold' ); ?>
					</p>
					<button type="submit" class="button button-primary siteunfold-btn" <?php disabled( ! $canConvert ); ?>>
						<?php esc_html_e( 'Convert to Multisite', 'siteunfold' ); ?>
					</button>
					<?php if ( ! $canConvert ) : ?>
						<span class="siteunfold-btn-note"><?php esc_html_e( 'Resolve the failed prerequisites above to enable the conversion.', 'siteunfold' ); ?></span>
					<?php endif; ?>
				</div>
			</form>
		</div>

	</div>
</div>
