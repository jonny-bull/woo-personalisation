<?php
/**
 * Most Popular feature class.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton\MostPopular;

use BoxUk\WpPluginSkeleton\FeatureInterface;
use \Automattic\WooCommerce\Admin\API\Reports\Products\Query as ProductsQuery;

final class MostPopular implements FeatureInterface {
	// Half a day cache time to invalidate during stock updates.
	private const CACHE_TTL_43200 = 43200;
	private const CACHE_KEY = 'woo_personalisation_most_popular_products';
	private const CACHE_GROUP = 'woo_personalisation_most_popular';
	private const DAYS_TO_MEASURE = 30;
	private const DATE_TIME_STRING_SUFFIX = ' 00:00:00';
	private const PRODUCT_COUNT_MULTIPLIER = 3;
	private const PRODUCT_ACCEPTED_STATUSES = [ 'instock', 'onbackorder' ];
	private const FEATURE_LABEL = 'woo_personalisation_most_popular';

	/**
	 * String in YYYY-MM-DD HH:MM:SS format.
	 * Denotes the starting point for our search of recent orders.
	 *
	 * @var string
	 */
	private $start_date;

	/**
	 * Most_Popular constructor.
	 */
	public function __construct() {
		$this->start_date = $this->get_days_ago( self::DAYS_TO_MEASURE );
	}

	/**
	 * Initialise class.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'woocommerce_thankyou', [ $this, 'clear_most_popular_cache' ], 10, 0 );
		add_action( 'woocommerce_product_set_stock', [ $this, 'clear_most_popular_cache' ], 10, 0 );
		add_action( 'woocommerce_variation_set_stock', [ $this, 'clear_most_popular_cache' ], 10, 0 );

		if ( $this->is_enabled() ) {
			add_action( 'woocommerce_after_cart', [ $this, 'display_most_popular_products' ], 10 );
			add_action( 'woocommerce_cart_is_empty', [ $this, 'display_most_popular_products' ], 10 );
		}
	}

	/**
	 * Whether the feature is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		if ( '1' === get_option( 'woo_personalisation_settings_most_popular_enable', false ) ) {
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
	 * Returns a date string
	 *
	 * @param integer $days_ago Number of days to progress backwards.
	 * @return string The date of $days_ago, in the YYYY-MM-DD HH:MM:SS format.
	 */
	public function get_days_ago( int $days_ago ): string {
		$days_ago_date = gmdate( 'Y-m-d', strtotime( '-' . $days_ago . ' day' ) );

		return $days_ago_date . self::DATE_TIME_STRING_SUFFIX;
	}

	/**
	 * Gets an array of products, ordered by most orders, followed by total items sold.
	 *
	 * @param integer $product_count Number of products to return. Defaults to 4.
	 * @return array Products that match our criteria.
	 */
	public function get_products( int $product_count = 4 ): array {
		// If something exists in the cache, use the cached values.
		if ( ! is_admin() ) {
			$cached_purchased_together_products = wp_cache_get( self::CACHE_KEY, self::CACHE_GROUP );

			if ( $cached_purchased_together_products ) {
				return $cached_purchased_together_products;
			}
		}

		// Otherwise, get new data from the database.
		$args = [
			'after' => $this->start_date,
			'orderby' => 'orders_count',
			'extended_info' => [
				'stock_status',
			],
			'per_page' => ( $product_count * self::PRODUCT_COUNT_MULTIPLIER ),
		];

		$report = new ProductsQuery( $args );
		$product_data_object = $report->get_data();
		$popular_products = [];

		// process the result set.
		foreach ( $product_data_object->data as $product ) {
			if (
				in_array( $product['extended_info']['stock_status'], self::PRODUCT_ACCEPTED_STATUSES, true ) &&
				count( $popular_products ) < $product_count
			) {
				// convert it into the format we need.
				$popular_products[] = $product['product_id'];
				continue;
			}
			// have what we need?
			if ( count( $popular_products ) === $product_count ) {
				break;
			}
		}

		// Cache the new data.
		if ( ! is_admin() ) {
			// phpcs:ignore WordPressVIPMinimum.Performance.LowExpiryCacheTime.LowCacheTime
			wp_cache_set( self::CACHE_KEY, $popular_products, self::CACHE_GROUP, self::CACHE_TTL_43200 );
		}

		return $popular_products;
	}

	/**
	 * Clears the current cache
	 *
	 * @return void
	 */
	public function clear_most_popular_cache(): void {
		wp_cache_delete( self::CACHE_KEY, self::CACHE_GROUP );
	}

	/**
	 * Uses a WooCommerce shortcode to output an array of products in the order sent to this function.
	 * Arguments acceptable as part of $args:
	 * - container_class (string) - class(es) to be applied to the container. Not required.
	 * - show_hr (bool) - Whether or not to render a HR. Defaults to true.
	 * - title (string) - the title of the block.
	 * - wrapper_class (string)  - class(es) to be applied to the wrapper. Not required.
	 * - wrapper_id (bool) - Whether or not to use a lowercase version of the title as an 'id'. Defaults to false.
	 *
	 * @param array $products_to_render Returns early if there are no products.
	 * @param array $args Arguments to be passed to the renderer.
	 * @return void
	 */
	public function product_shortcode_render( array $products_to_render = [], array $args = [] ): void {
		$default_args = [
			'container_class' => '',
			'show_hr' => true,
			'title' => '',
			'wrapper_class' => '',
			'wrapper_id' => false,
		];

		$function_args = wp_parse_args( $args, $default_args );

		$wrapper_class_render = 'woo-personalisation-most-popular ' . $function_args['wrapper_class'];

		if ( [] === $products_to_render ) {
			return;
		}

		?>
		<div class="<?php echo esc_attr( $function_args['container_class'] ); ?>">
		<?php if ( true === $function_args['show_hr'] ) : ?>
			<hr>
		<?php endif; ?>
			<div
				<?php if ( true === $function_args['wrapper_id'] ) : ?>
					id="<?php echo esc_attr( rawurlencode( $function_args['title'] ) ); ?>"
				<?php endif; ?>
				<?php if ( array_key_exists( 'product_listing_type', $function_args ) ) : ?>
					data-product-listing-type="<?php echo esc_attr( $function_args['product_listing_type'] ); ?>"
				<?php endif; ?>
				class="<?php echo esc_attr( $wrapper_class_render ); ?>">
				<h2><?php echo esc_html( $function_args['title'] ); ?></h2>
				<?php
				echo do_shortcode( '[products orderby="post__in" ids="' . implode( ',', $products_to_render ) . '"]' );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Displays the current most popular in stock products.
	 * 'Most popular' is defined as the most individual orders of an item over the past 30 days.
	 *
	 * @return void
	 */
	public function display_most_popular_products(): void {
		$products_to_render = $this->get_products();

		if ( count( $products_to_render ) === 0 ) {
			return;
		}

		$most_popular_args = [
			'container_class' => '',
			'title' => __( 'Most popular', 'woocommerce' ),
			'wrapper_class' => 'woo-personalisation-most-popular-products-block',
			'show_hr' => true,
			'wrapper_id' => true,
		];

		$this->product_shortcode_render( $products_to_render, $most_popular_args );
	}

	/**
	 * Uninstall method to remove any data created by the feature.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		( new MostPopularUninstaller() )->uninstall();
	}
}
