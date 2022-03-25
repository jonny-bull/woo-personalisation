<?php
/**
 * WooCommerce Personalisation Blocks
 *
 * @package BoxUk\WpPluginSkeleton
 * @author Box UK
 * @copyright 2022 Box UK
 * @license GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: WooCommerce Personalisation Blocks
 * Description: Adds sales driving personalisation features to WooCommerce.
 * Author: Box UK
 * Author URI: https://github.com/boxuk/woo-personalisation/
 * Version: 1.0.0
 * License: GPLv3+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: woo-personalisation
 * Domain Path: /languages/
 * Requires PHP: 7.2
 * Requires at least: 5.0
 * Tested up to: 5.8
 */

declare ( strict_types=1 );

use BoxUk\WpPluginSkeleton\Activation;
use BoxUk\WpPluginSkeleton\FeatureManager;
use BoxUk\WpPluginSkeleton\Plugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'WOO_PERSONALISATION_VERSION', '1.0.0' );
define( 'WOO_PERSONALISATION_PREFIX', 'woo_personalisation' );
define( 'WOO_PERSONALISATION_FILENAME_PREFIX', 'woo-personalisation' );

$plugin_base_url = plugin_dir_url( __FILE__ );
define( 'WOO_PERSONALISATION_PLUGIN_URL', $plugin_base_url );

/**
 * Make sure we can access the autoloader, and it works.
 *
 * @return bool
 */
function woo_personalisation_plugin_autoload(): bool { // phpcs:ignore NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions
	$autoloader = __DIR__ . '/vendor/autoload.php';
	if ( file_exists( $autoloader ) ) {
		require_once $autoloader; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
	}

	return class_exists( Plugin::class );
}

if ( ! woo_personalisation_plugin_autoload() ) {
	return;
}

register_activation_hook( __FILE__, [ Activation::class, 'activate' ] );

$app = new Plugin( new FeatureManager() );
$app->run();
