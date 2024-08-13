<?php

/**
 * Plugin Name: PinkCrab Debug Gateways
 * Description: Collection of WC Gateways for testing, with Block Support.
 * Version: 1.0.0
 * Author: PinkCrab\Glynn Quelch
 * Author URI: https://github.com/gin0115
 * Text Domain: pinkcrab-debug-gateways
 *
 * Requires at least: 6.2
 * Tested up to: 6.4
 * WC requires at least: 8.0
 * WC tested up to: 8.6.1
 */

// use RecursiveIteratorIterator;
// use RecursiveDirectoryIterator;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use PinkCrab\Debug_Gateway\Gateway\PinkCrab_Confirm_Once_Gateway;
use PinkCrab\Debug_Gateway\Gateway\PinkCrab_Always_Reject_Gateway;
use PinkCrab\Debug_Gateway\Gateway\PinkCrab_Maybe_Confirm_Gateway;
use PinkCrab\Debug_Gateway\Gateway\PinkCrab_Always_Confirm_Gateway;
use PinkCrab\Debug_Gateway\Block_Method\PinkCrab_Always_Reject_Block;
use PinkCrab\Debug_Gateway\Block_Method\PinkCrab_Always_Confirm_Block;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PCGW_DEBUG_GATEWAYS_URL', plugin_dir_url( __FILE__ ) );
define( 'PCGW_DEBUG_GATEWAYS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Include all files in sub directory.
 *
 * @param string $path The directory to include all files from.
 *
 * @return void
 */
function pcgw_include_all_files( string $path ): void {
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $path, RecursiveDirectoryIterator::SKIP_DOTS )
	);
	foreach ( $iterator as $file ) {
		if ( pathinfo( $file, PATHINFO_EXTENSION ) == 'php' ) {
			require_once $file;
		}
	}
}

/**
 * Include all gateways.
 *
 * @return void
 */
function pcgq_include_gateways() {
	if ( class_exists( 'WC_Payment_Gateways' ) ) {
		pcgw_include_all_files( __DIR__ . DIRECTORY_SEPARATOR . 'gateways' );
	}
}
add_action( 'plugins_loaded', 'pcgq_include_gateways' );

/**
 * Include all inc files.
 *
 * @return void
 */
function pcgw_include_inc_files() {
	pcgw_include_all_files( __DIR__ . DIRECTORY_SEPARATOR . 'inc' );
}
add_action( 'init', 'pcgw_include_inc_files' );

/**
 * Include all Block Payment Methods.
 *
 * @return void
 */
function pcgw_include_block_payment_methods() {
	// Bail if not supported.
	if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry' ) ) {
	}

	// Include all block methods.
	pcgw_include_all_files( __DIR__ . DIRECTORY_SEPARATOR . 'block-methods' );

	// Hook the registration function to the 'woocommerce_blocks_payment_method_type_registration' action
	add_action(
		'woocommerce_blocks_payment_method_type_registration',
		function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
			// Register an instance of My_Custom_Gateway_Blocks
			$payment_method_registry->register( new PinkCrab_Always_Confirm_Block() );
			$payment_method_registry->register( new PinkCrab_Always_Reject_Block() );
		}
	);
}
add_action( 'woocommerce_blocks_loaded', 'pcgw_include_block_payment_methods' );

/**
 * Enable compatibility with modern WC features.
 *
 * @return void
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);

/**
 * Register all gateways.
 *
 * @param array $gateways
 * @return array
 */
function pcgw_register_gateways( array $gateways ): array {
	$gateways[] = PinkCrab_Always_Confirm_Gateway::class;
	$gateways[] = PinkCrab_Always_Reject_Gateway::class;
	$gateways[] = PinkCrab_Confirm_Once_Gateway::class;
	$gateways[] = PinkCrab_Maybe_Confirm_Gateway::class;
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'pcgw_register_gateways' );

// Create a wp option to define if the maybe gateway should pass

/**
 * Register the settings.
 *
 * @return void
 */
function pcgw_register_settings() {
	register_setting( 'pcgw_settings', 'pcgw_settings' );
	add_settings_section(
		'pcgw_settings_section',
		__( 'Debug Gateway Settings', 'pcgw' ),
		'pcgw_settings_section_callback',
		'pcgw_settings'
	);
	add_settings_field(
		'pcgw_settings_field',
		__( 'Should Maybe Payment Complete?', 'pcgw' ),
		'pcgw_settings_field_callback',
		'pcgw_settings',
		'pcgw_settings_section'
	);
}
add_action( 'admin_init', 'pcgw_register_settings' );

/**
 * Add the settings page.
 *
 * @return void
 */
function pcgw_add_settings_page() {
	add_options_page(
		__( 'Debug Gateway Settings', 'pcgw' ),
		__( 'Debug Gateway Settings', 'pcgw' ),
		'manage_options',
		'pcgw_settings',
		'pcgw_settings_page'
	);
}
add_action( 'admin_menu', 'pcgw_add_settings_page' );

/**
 * Render the settings page.
 *
 * @return void
 */
function pcgw_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Debug Gateway Settings', 'pcgw' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'pcgw_settings' );
			do_settings_sections( 'pcgw_settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Render the settings section.
 *
 * @return void
 */
function pcgw_settings_section_callback() {
	echo '<p>' . esc_html__( 'Settings for the Debug Gateways.', 'pcgw' ) . '</p>';
}

/**
 * Render the settings field.
 *
 * @return void
 */
function pcgw_settings_field_callback() {
	?>
	<input type="checkbox" name="pcgw_settings[enable_maybe_gateway]" value="1" <?php checked( pcgw_should_maybe_gateway_confirm(), 1 ); ?> />
	<?php
}

/**
 * Should the maybe gateway confirm.
 *
 * @return boolean
 */
function pcgw_should_maybe_gateway_confirm(): bool {
	$options = get_option( 'pcgw_settings' );
	return isset( $options['enable_maybe_gateway'] ) && $options['enable_maybe_gateway'] == 1;
}