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
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'pcgw_register_gateways' );
