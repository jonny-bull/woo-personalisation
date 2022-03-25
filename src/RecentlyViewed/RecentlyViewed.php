<?php
/**
 * Recently Viewed feature class.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton\RecentlyViewed;

use BoxUk\WpPluginSkeleton\FeatureInterface;
use BoxUk\WpPluginSkeleton\RecentlyViewed\RecentlyViewedUninstaller;

final class RecentlyViewed implements FeatureInterface {
	private const FEATURE_LABEL = 'woo_personalisation_recently_viewed';

	/**
	 * Recently viewed cookie value.
	 *
	 * @var int
	 */
	private $user_cookie;

	/**
	 * Recently_Viewed constructor.
	 */
	public function __construct() {
		//phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		//phpcs:disable WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE
		if ( ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
			$this->user_cookie = $_COOKIE['woocommerce_recently_viewed'];
		}
		//phpcs:enable WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE
		//phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	/**
	 * Initialise class.
	 *
	 * @return void
	 */
	public function init(): void {
	}

	/**
	 * Whether the feature is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		if ( '1' === get_option( 'woo_personalisation_settings_recently_viewed_enable', false ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Label for this feature.
	 *
	 * @return string
	 */
	public static function get_label(): string {
		return self::FEATURE_LABEL;
	}

	/**
	 * Get the recently viewed products.
	 *
	 * @param integer $product_count The number of products to return. Defaults to 4.
	 * @return array The IDs of the recently viewed products in view order (most recent first).
	 */
	public function get_products( int $product_count = 4 ): array {
		$viewed_products = ! empty( $this->user_cookie ) ? (array) explode( '|', wp_unslash( $this->user_cookie ) ) : [];

		if ( empty( $viewed_products ) ) {
			return [];
		}

		$unique_products = array_unique( array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) ) );

		if ( 'product' === get_post_type() ) {
			// Remove the current product from product pages.
			$unique_products = array_filter(
				$unique_products,
				function( int $v ): bool {
					return get_the_ID() !== $v;
				}
			);
		}

		return array_slice( $unique_products, 0, $product_count );
	}

	/**
	 * Displays a user's four most recently viewed products.
	 *
	 * @param array $args Arguments to effect the display.
	 * @return void
	 */
	public function display_recently_viewed_products( array $args = [] ): void {
		$default_args = [
			'container_class' => 'l-container',
			'title' => __( 'Recently viewed', 'peake' ),
			'wrapper_class' => 'recently-viewed-products-block',
			'wrapper_id' => true,
			'product_count' => 4,
		];

		$recently_viewed_args = wp_parse_args( $args, $default_args );

		$product_count = $args['product_count'] ?? 4;
		$products_to_render = $this->get_products( $product_count );

		if ( count( $products_to_render ) === 0 ) {
			return;
		}

		if ( is_product() ) {
			$recently_viewed_args['container_class'] = 'l-container l-container--white l-container--white-md-neg';
		}

		$this->product_shortcode_render( $products_to_render, $recently_viewed_args );
	}

	/**
	 * Uninstall method to remove any data created by the feature.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		( new RecentlyViewedUninstaller() )->uninstall();
	}
}
