<?php
/**
 * Plugin Settings.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

namespace BoxUk\WpPluginSkeleton\Admin;

use BoxUk\WpPluginSkeleton\PluginSettingsManager;

class PluginSettings {
	private const MENU_POSITION = 80;
	private const OPTION_GROUP_NAME = WOO_PERSONALISATION_PREFIX . '_settings';
	private const PAGE = WOO_PERSONALISATION_PREFIX;
	private const PAGE_SLUG = WOO_PERSONALISATION_FILENAME_PREFIX . '-settings';
	private const MOST_POPULAR_PREFIX = 'most_popular';
	private const RECENTLY_VIEWED_PREFIX = 'recently_viewed';
	private const FREQUENTLY_BOUGHT_TOGETHER_PREFIX = 'frequently_bought_together';
	private const OUT_OF_STOCK_ALTERNATIVES = 'out_of_stock_alternatives';

	/**
	 * PluginSettings Initialization.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( false === PluginSettingsManager::is_woocommerce_activated() ) {
			return;
		}

		add_action( 'admin_menu', [ __CLASS__, 'add_settings_menu_page' ] );
		add_action( 'admin_init', [ __CLASS__, 'add_register_setting' ] );
		add_action( 'admin_notices', [ __CLASS__, 'display_notices' ] );
	}

	/**
	 * Create plugin menu page.
	 *
	 * @return void
	 */
	public static function add_settings_menu_page(): void {
		add_menu_page(
			__( 'Woo Personalisation', 'woo-personalisation' ),
			__( 'Woo Personalisation', 'woo-personalisation' ),
			'manage_options',
			self::PAGE_SLUG,
			[ __CLASS__, 'add_menu_page_content' ],
			'dashicons-megaphone',
			self::MENU_POSITION
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public static function add_menu_page_content(): void {
		$active_tab = array_key_exists( 'tab', $_GET ) ? sanitize_text_field( $_GET['tab'] ) : self::MOST_POPULAR_PREFIX;
		?>
		<div class="wrap">
			<div class="woo-personalisation-settings-header">
				<h1 class="wp-heading-inline">
					<?php esc_html_e( 'WooCommerce Personalisation Blocks', 'woo-personalisation' ); ?>
				</h1>
			</div>
			<hr class="wp-header-end" />

			<div class="nav-tab-wrapper">
				<a
					href="?page=<?php echo esc_attr( self::PAGE_SLUG ); ?>&tab=<?php echo esc_attr( self::MOST_POPULAR_PREFIX ); ?>"
					class="nav-tab <?php echo self::MOST_POPULAR_PREFIX === $active_tab ? 'nav-tab-active' : ''; ?>">
						<?php esc_html_e( 'Most Popular', 'woo-personalisation' ); ?>
				</a>
				<a
					href="?page=<?php echo esc_attr( self::PAGE_SLUG ); ?>&tab=<?php echo esc_attr( self::RECENTLY_VIEWED_PREFIX ); ?>"
					class="nav-tab <?php echo self::RECENTLY_VIEWED_PREFIX === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Recently Viewed', 'woo-personalisation' ); ?>
				</a>
				<a
					href="?page=<?php echo esc_attr( self::PAGE_SLUG ); ?>&tab=<?php echo esc_attr( self::FREQUENTLY_BOUGHT_TOGETHER_PREFIX ); ?>"
					class="nav-tab <?php echo self::FREQUENTLY_BOUGHT_TOGETHER_PREFIX === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Frequently Bought Together', 'woo-personalisation' ); ?>
				</a>
				<a
					href="?page=<?php echo esc_attr( self::PAGE_SLUG ); ?>&tab=<?php echo esc_attr( self::OUT_OF_STOCK_ALTERNATIVES ); ?>"
					class="nav-tab <?php echo self::OUT_OF_STOCK_ALTERNATIVES === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Out of Stock Alternatives', 'woo-personalisation' ); ?>
				</a>
			</div>

			<form method="post" action="options.php">
				<?php
				settings_fields( self::OPTION_GROUP_NAME . '_' . $active_tab );
				do_settings_sections( WOO_PERSONALISATION_PREFIX . '_' . $active_tab );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public static function add_register_setting(): void {
		$most_popular_settings = [
			'enable' => [
				'label' => __( 'Enable Most Popular block', 'woo-personalisation' ),
				'type' => 'checkbox',
				'default' => '1',
			],
			'to_display' => [
				'label' => __( 'Number of products to display', 'woo-personalisation' ),
				'type' => 'input_number',
				'default' => 4,
			],
			'purchase_threshold' => [
				'label' => __( 'Number of days of sales to include', 'woo-personalisation' ),
				'type' => 'input_number',
				'default' => 30,
				'description' => __( 'This allows you to reflect recent trends in sales', 'woo-personalisation' ),
			],
			'include_out_of_stock' => [
				'label' => __( 'Include out of stock products', 'woo-personalisation' ),
				'type' => 'checkbox',
				'default' => '0',
			],
		];

		$recently_viewed_settings = [
			'enable' => [
				'label' => __( 'Enable Recently Viewed block', 'woo-personalisation' ),
				'type' => 'checkbox',
				'default' => '1',
			],
			'to_display' => [
				'label' => __( 'Number of products to display', 'woo-personalisation' ),
				'type' => 'input_number',
				'default' => 4,
			],
			'include_out_of_stock' => [
				'label' => __( 'Include out of stock products', 'woo-personalisation' ),
				'type' => 'checkbox',
				'default' => '0',
			],
		];

		$fbt_settings = [
			'enable' => [
				'label' => __( 'Enable Frequently Bought Together block', 'woo-personalisation' ),
				'type' => 'checkbox',
				'default' => '1',
			],
			'block_title' => [
				'label' => __( 'Title', 'woo-personalisation' ),
				'type' => 'input_text',
				'class' => 'regular-text',
				'default' => 'Frequently bought together products',
			],
			'block_subtitle' => [
				'label' => __( 'Subtitle', 'woo-personalisation' ),
				'type' => 'input_text',
				'class' => 'regular-text',
				'default' => 'Other customers regularly buy these products together:',
			],
			'include_out_of_stock' => [
				'label' => __( 'Include out of stock products', 'woo-personalisation' ),
				'type' => 'checkbox',
				'default' => '0',
			],
			'purchase_threshold' => [
				'label' => __( 'Purchase threshold', 'woo-personalisation' ),
				'type' => 'input_number',
				'default' => 2,
				'description' => __( 'Number of times a product must be purchased before being considered frequently bought with a product', 'woo-personalisation' ),
			],
			'items_to_display' => [
				'label' => __( 'Maximum items to display', 'woo-personalisation' ),
				'type' => 'input_number',
				'default' => 2,
				'description' => __( 'Maximum number of products to display alongside the original product', 'woo-personalisation' ),
			],
			'incompatible_filter' => [
				'label' => __( 'Allow filtering of incompatible items', 'woo-personalisation' ),
				'type' => 'checkbox',
				'default' => '1',
			],
		];

		$oos_settings = [
			'enable' => [
				'label' => __( 'Enable Out of Stock Alternatives block', 'woo-personalisation' ),
				'type' => 'checkbox',
				'default' => '1',
			],
			'block_title' => [
				'label' => __( 'Title', 'woo-personalisation' ),
				'type' => 'input_text',
				'class' => 'regular-text',
				'default' => 'Oh no! We\'re out of stock. Try these other products instead',
			],
			'block_subtitle' => [
				'label' => __( 'Subtitle', 'woo-personalisation' ),
				'type' => 'input_text',
				'class' => 'regular-text',
				'default' => '',
			],
			'items_to_display' => [
				'label' => __( 'Maximum items to display', 'woo-personalisation' ),
				'type' => 'input_number',
				'default' => 5,
				'description' => __( 'Maximum number of products to display as alternatives to the original product', 'woo-personalisation' ),
			],
		];

		self::add_section_settings( self::MOST_POPULAR_PREFIX, 'Most popular', $most_popular_settings );
		self::add_section_settings( self::RECENTLY_VIEWED_PREFIX, 'Recently viewed', $recently_viewed_settings );
		self::add_section_settings( self::FREQUENTLY_BOUGHT_TOGETHER_PREFIX, 'Frequently bought together', $fbt_settings );
		self::add_section_settings( self::OUT_OF_STOCK_ALTERNATIVES, 'Out of Stock Alternatives', $oos_settings );
	}

	/**
	 * Registers a section. Registers and adds settings to the section.
	 *
	 * @param string $prefix Section prefix.
	 * @param string $title Section display title.
	 * @param array  $settings Array of settings.
	 * @return void
	 */
	public static function add_section_settings( string $prefix, string $title, array $settings ): void {
		$section_name = self::OPTION_GROUP_NAME . '_' . $prefix;
		$page_name = WOO_PERSONALISATION_PREFIX . '_' . $prefix;

		add_settings_section(
			$section_name,
			$title,
			'',
			$page_name
		);

		foreach ( $settings as $setting => $setting_vals ) {
			$option_name = $section_name . '_' . $setting;

			register_setting(
				$section_name,
				$option_name
			);

			$setting_vals['option_name'] = $option_name;
			$setting_vals['label_for'] = $option_name;

			add_settings_field(
				$option_name,
				$setting_vals['label'],
				[ __CLASS__, 'woo_personalisation_' . $setting_vals['type'] . '_html' ],
				$page_name,
				$section_name,
				$setting_vals
			);
		}
	}

	/**
	 * Checks the array of arguments passed through exists and contains the an option name key.
	 *
	 * @param array $args
	 * @return boolean
	 */
	public static function woo_personalisation_args_exist( array $args = [] ): bool {
		if ( [] !== $args && array_key_exists( 'option_name', $args ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Print a populated description span if arguments contain a 'description' key.
	 *
	 * @param array $args
	 * @return void
	 */
	public static function woo_personalisation_display_description_if_exists( array $args = [] ): void {
		if ( array_key_exists( 'description', $args ) ) :
			?>
			<span class="description"><?php echo esc_html( $args['description'] ); ?></span>
			<?php
		endif;
	}

	/**
	 * Render checkbox fields.
	 *
	 * @param array $args Arguments passed to the callback.
	 * @return void
	 */
	public static function woo_personalisation_checkbox_html( array $args = [] ): void {
		if ( false === self::woo_personalisation_args_exist( $args ) ) {
			return;
		}
		?>
		<input
			type="checkbox"
			<?php if ( array_key_exists( 'class', $args ) ) : ?>
				class="<?php echo esc_attr( $args['class'] ); ?>"
			<?php endif; ?>
			name="<?php echo esc_attr( $args['option_name'] ); ?>"
			id="<?php echo esc_attr( $args['option_name'] ); ?>"
			value="1"
			<?php checked( true, get_option( $args['option_name'], $args['default'] ), true ); ?>
		/>
		<?php
		self::woo_personalisation_display_description_if_exists( $args );
	}

	/**
	 * Render text fields.
	 *
	 * @param array $args Arguments passed to the callback.
	 * @return void
	 */
	public static function woo_personalisation_input_text_html( array $args = [] ): void {
		if ( [] === $args || ! array_key_exists( 'option_name', $args ) ) {
			return;
		}
		?>
		<input
			type="text"
			<?php if ( array_key_exists( 'class', $args ) ) : ?>
				class="<?php echo esc_attr( $args['class'] ); ?>"
			<?php endif; ?>
			value="<?php echo esc_attr( get_option( $args['option_name'], $args['default'] ) ); ?>"
			name="<?php echo esc_attr( $args['option_name'] ); ?>"
			id="<?php echo esc_attr( $args['option_name'] ); ?>"
		/>
		<?php
		self::woo_personalisation_display_description_if_exists( $args );
	}

	/**
	 * Render number fields.
	 *
	 * @param array $args Arguments passed to the callback.
	 * @return void
	 */
	public static function woo_personalisation_input_number_html( array $args = [] ): void {
		if ( [] === $args || ! array_key_exists( 'option_name', $args ) ) {
			return;
		}
		?>
		<input
			type="number"
			min="1"
			<?php if ( array_key_exists( 'class', $args ) ) : ?>
				class="<?php echo esc_attr( $args['class'] ); ?>"
			<?php endif; ?>
			value="<?php echo esc_attr( get_option( $args['option_name'], $args['default'] ) ); ?>"
			name="<?php echo esc_attr( $args['option_name'] ); ?>"
			id="<?php echo esc_attr( $args['option_name'] ); ?>"
		/>
		<?php
		self::woo_personalisation_display_description_if_exists( $args );
	}

	/**
	 * Display notices to ensure both success and error notices are shown.
	 *
	 * @return void
	 */
	public static function display_notices(): void {
		settings_errors();
	}
}
