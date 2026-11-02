<?php

declare(strict_types=1);

namespace Siteunfold\Conversion;

/**
 * Checks whether the installation can be converted to a multisite network.
 */
final class PrerequisiteChecker {

	/**
	 * Memoized results for the current request.
	 *
	 * @var list<PrerequisiteResult>|null
	 */
	private ?array $results = null;

	/**
	 * Constructor.
	 *
	 * @param Environment $environment Installation facts.
	 */
	public function __construct( private readonly Environment $environment ) {
	}

	/**
	 * Returns the installation facts this checker evaluates.
	 *
	 * @return Environment
	 */
	public function environment(): Environment {
		return $this->environment;
	}

	/**
	 * Runs all prerequisite checks.
	 *
	 * @return list<PrerequisiteResult>
	 */
	public function check(): array {
		return $this->results ??= array(
			$this->checkSingleSite(),
			$this->checkWpConfigWritable(),
			$this->checkFilesystemMethod(),
			$this->checkServerAddress(),
			$this->checkNetworkType(),
			$this->checkRewriteRules(),
		);
	}

	/**
	 * Returns true when no check blocks the conversion (warnings are allowed).
	 *
	 * @return bool
	 */
	public function canConvert(): bool {
		foreach ( $this->check() as $result ) {
			if ( $result->blocks() ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks that the site is not already a multisite network.
	 *
	 * @return PrerequisiteResult
	 */
	private function checkSingleSite(): PrerequisiteResult {
		$label = __( 'Single site installation', 'siteunfold' );

		if ( is_multisite() ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'This site is already running as a multisite network.', 'siteunfold' )
			);
		}

		return new PrerequisiteResult( $label, true );
	}

	/**
	 * Checks that wp-config.php exists and is writable.
	 *
	 * @return PrerequisiteResult
	 */
	private function checkWpConfigWritable(): PrerequisiteResult {
		$label  = __( 'wp-config.php is writable', 'siteunfold' );
		$config = $this->environment->wpConfigPath();

		if ( null === $config ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'wp-config.php could not be located.', 'siteunfold' )
			);
		}

		if ( ! wp_is_writable( $config ) ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'wp-config.php is not writable. Please check the file permissions.', 'siteunfold' )
			);
		}

		return new PrerequisiteResult( $label, true );
	}

	/**
	 * Checks that WordPress can write files directly, without asking for FTP/SSH credentials.
	 *
	 * @return PrerequisiteResult
	 */
	private function checkFilesystemMethod(): PrerequisiteResult {
		$label = __( 'Direct file system access', 'siteunfold' );

		if ( ! $this->environment->hasDirectFilesystem() ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'WordPress cannot write files directly on this server (it would need FTP or SSH credentials). Follow the manual Network Setup steps instead.', 'siteunfold' )
			);
		}

		return new PrerequisiteResult( $label, true );
	}

	/**
	 * Checks that the site address does not use a non-standard port.
	 *
	 * @return PrerequisiteResult
	 */
	private function checkServerAddress(): PrerequisiteResult {
		$label = __( 'Supported site address', 'siteunfold' );

		if ( $this->environment->hasNonStandardPort() ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'WordPress cannot create a network on an address with a port number other than 80 or 443.', 'siteunfold' )
			);
		}

		return new PrerequisiteResult( $label, true );
	}

	/**
	 * Checks that at least one network type is possible.
	 *
	 * @return PrerequisiteResult
	 */
	private function checkNetworkType(): PrerequisiteResult {
		$label        = __( 'Network type available', 'siteunfold' );
		$subdomain    = $this->environment->subdomainAllowed();
		$subdirectory = $this->environment->subdirectoryAllowed();

		if ( ! $subdomain && ! $subdirectory ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'Sub-domains are not possible on localhost, an IP address or a site in a sub-folder, and sub-directories are disabled because this site has posts older than a month.', 'siteunfold' )
			);
		}

		if ( ! $subdomain ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'Only sub-directories are possible: sub-domains need a real domain, not localhost, an IP address or a site in a sub-folder.', 'siteunfold' ),
				false
			);
		}

		if ( ! $subdirectory ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'Only sub-domains are possible: WordPress disables sub-directories once a site has posts older than a month, because their permalinks could collide with new sites.', 'siteunfold' ),
				false
			);
		}

		return new PrerequisiteResult( $label, true );
	}

	/**
	 * Checks that the multisite rewrite rules can be written.
	 *
	 * On Apache, .htaccess must be writable. Other servers (nginx, IIS) ignore
	 * .htaccess, so the admin has to add the rules to the server config by hand:
	 * that is a warning, not a blocker.
	 *
	 * @return PrerequisiteResult
	 */
	private function checkRewriteRules(): PrerequisiteResult {
		$label = __( 'Rewrite rules can be written', 'siteunfold' );

		if ( ! $this->environment->usesHtaccess() ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'This server does not use .htaccess (for example nginx). After converting, add the multisite rewrite rules to your server configuration by hand.', 'siteunfold' ),
				false
			);
		}

		$htaccess = $this->environment->htaccessPath();

		if ( file_exists( $htaccess ) && ! wp_is_writable( $htaccess ) ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( '.htaccess exists but is not writable. Please check the file permissions.', 'siteunfold' )
			);
		}

		if ( ! file_exists( $htaccess ) && ! wp_is_writable( dirname( $htaccess ) ) ) {
			return new PrerequisiteResult(
				$label,
				false,
				__( 'The WordPress root directory is not writable, so .htaccess cannot be created.', 'siteunfold' )
			);
		}

		return new PrerequisiteResult( $label, true );
	}
}
