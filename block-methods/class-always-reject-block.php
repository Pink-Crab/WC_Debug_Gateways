<?php

/**
 * Block Payment Method for Always Confirm
 *
 * @author  Glynn Quelch<glynn@pinkcrab.co.uk>
 */

namespace PinkCrab\Debug_Gateway\Block_Method;

use PinkCrab\Debug_Gateway\Gateway\PinkCrab_Always_Reject_Gateway;
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Always rejected block payment.
*/
class PinkCrab_Always_Reject_Block extends AbstractPaymentMethodType {
	private $gateway;
	protected $name = 'pc_always_reject';

	public function initialize() {
		$this->settings = array();
		$this->gateway  = new PinkCrab_Always_Reject_Gateway();
	}

	public function is_active() {
		return true;
	}

	public function get_payment_method_script_handles() {

		// open assets file.
        $asset_file = include PCGW_DEBUG_GATEWAYS_PATH . '/blocks/build/always-reject/index.asset.php';
        
        wp_register_script(
			'pc_always_reject-blocks-integration',
			PCGW_DEBUG_GATEWAYS_URL . '/blocks/build/always-reject/index.js',
			\array_key_exists( 'dependencies', $asset_file ) ? $asset_file['dependencies'] : array(),
			\array_key_exists( 'version', $asset_file ) ? $asset_file['version'] :
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'pc_always_reject-blocks-integration' );
		}
		return array( 'pc_always_reject-blocks-integration' );
	}

	public function get_payment_method_data() {
		return array(
			'title'       => $this->gateway->title,
			'description' => $this->gateway->description,
		);
	}
}
