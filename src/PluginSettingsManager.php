<?php
/**
 * Class for accessing plugin settings.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton;

class PluginSettingsManager {
	/**
	 * Check if the plugin is enabled. Defaults to true.
	 *
	 * @return bool
	 */
	public static function is_enabled(): bool {
		return filter_var( get_option( WOO_PERSONALISATION_PREFIX . '_enable_plugin', '1' ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Checks if WooCommerce is enabled. Defaults to false.
	 *
	 * @return bool
	 */
	public static function is_woocommerce_activated(): bool {
		$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

		if (
			in_array( $plugin_path, wp_get_active_and_valid_plugins(), true )
			|| in_array( $plugin_path, wp_get_active_network_plugins(), true )
		) {
			return true;
		}

		return false;
	}
}
