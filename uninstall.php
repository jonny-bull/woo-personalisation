<?php
/**
 * Uninstaller for the plugin.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

use BoxUk\WpPluginSkeleton\FeatureManager;
use BoxUk\WpPluginSkeleton\PluginUninstaller;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$autoloader = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
}

if ( ! class_exists( PluginUninstaller::class ) ) {
	return;
}

if ( ! defined( 'WOO_PERSONALISATION_PREFIX' ) ) {
	define( 'WOO_PERSONALISATION_PREFIX', 'woo-personalisation' );
}

add_action(
	WOO_PERSONALISATION_PREFIX . '_plugin_uninstall',
	function () {
		$uninstaller = new PluginUninstaller( new FeatureManager() );
		$uninstaller->uninstall();
	}
);

do_action( WOO_PERSONALISATION_PREFIX . '_plugin_uninstall' );
