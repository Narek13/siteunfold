<?php
/**
 * Prerequisites card, shared by the convert and revert screens.
 *
 * @package Siteunfold
 *
 * @var list<\Siteunfold\Conversion\PrerequisiteResult> $prerequisites
 * @var bool $ready Whether no check blocks the action.
 */

declare(strict_types=1);

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- template variables scoped to include context.

defined( 'ABSPATH' ) || exit;

$hasWarnings = false;
foreach ( $prerequisites as $prerequisite ) {
	$hasWarnings = $hasWarnings || $prerequisite->isWarning();
}
?>
<div class="siteunfold-card">
	<div class="siteunfold-card__header">
		<h2><?php esc_html_e( 'Prerequisites', 'siteunfold' ); ?></h2>
		<?php if ( ! $ready ) : ?>
			<span class="siteunfold-badge siteunfold-badge--fail"><?php esc_html_e( 'Action required', 'siteunfold' ); ?></span>
		<?php elseif ( $hasWarnings ) : ?>
			<span class="siteunfold-badge siteunfold-badge--warn"><?php esc_html_e( 'Ready, with notes', 'siteunfold' ); ?></span>
		<?php else : ?>
			<span class="siteunfold-badge siteunfold-badge--pass"><?php esc_html_e( 'All checks passed', 'siteunfold' ); ?></span>
		<?php endif; ?>
	</div>
	<ul class="siteunfold-card__rows">
		<?php foreach ( $prerequisites as $prerequisite ) : ?>
			<?php
			if ( $prerequisite->passes() ) {
				$rowClass    = 'siteunfold-row--pass';
				$icon        = '✓';
				$statusLabel = __( 'Passed:', 'siteunfold' );
			} elseif ( $prerequisite->isWarning() ) {
				$rowClass    = 'siteunfold-row--warn';
				$icon        = '!';
				$statusLabel = __( 'Note:', 'siteunfold' );
			} else {
				$rowClass    = 'siteunfold-row--fail';
				$icon        = '✗';
				$statusLabel = __( 'Failed:', 'siteunfold' );
			}
			?>
			<li class="siteunfold-row <?php echo esc_attr( $rowClass ); ?>">
				<span class="siteunfold-row__icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
				<div class="siteunfold-row__body">
					<span class="siteunfold-row__label">
						<span class="screen-reader-text"><?php echo esc_html( $statusLabel ); ?></span>
						<?php echo esc_html( $prerequisite->label() ); ?>
					</span>
					<?php if ( ! $prerequisite->passes() && '' !== $prerequisite->message() ) : ?>
						<span class="siteunfold-row__message"><?php echo esc_html( $prerequisite->message() ); ?></span>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
