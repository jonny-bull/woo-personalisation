<?php
/**
 * Controller for the plugin Admin App.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton;

use BoxUk\WpPluginSkeleton\Admin\PluginSettings;

class PluginAdmin {
	/**
	 * Main run method for the plugin Admin app.
	 *
	 * @return void
	 */
	public function init(): void {
		( new PluginSettings() )->init();

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_scripts' ] );
	}

	/**
	 * Enqueue any CSS needed for the admin.
	 *
	 * @return void
	 */
	public static function enqueue_admin_styles(): void {
		wp_enqueue_style( WOO_PERSONALISATION_FILENAME_PREFIX . '-admin-styles', WOO_PERSONALISATION_PLUGIN_URL . 'assets/css/' . WOO_PERSONALISATION_FILENAME_PREFIX . '-admin.css', [], WOO_PERSONALISATION_VERSION );
	}

	/**
	 * Enqueue any JS needed for the admin.
	 *
	 * @return void
	 */
	public static function enqueue_admin_scripts(): void {
		wp_enqueue_script( WOO_PERSONALISATION_FILENAME_PREFIX . '-admin-script', WOO_PERSONALISATION_PLUGIN_URL . 'assets/js/' . WOO_PERSONALISATION_FILENAME_PREFIX . '-admin.js', [], WOO_PERSONALISATION_VERSION, true );
	}
}
