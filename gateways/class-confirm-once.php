<?php

/**
 * Gateway that always only confirms on the first payment for an email.
 *
 * @author  Glynn Quelch<glynn@pinkcrab.co.uk>
 */

namespace PinkCrab\Debug_Gateway\Gateway;

/**
 * Confirm Once gateways.
*/
class PinkCrab_Confirm_Once_Gateway extends \WC_Payment_Gateway {

	private const OPTION_KEY = 'pc_confirm_once_use';

	private $uses = array();


	public function __construct() {

		// details
		$this->id                 = 'pc_confirm_once_use';
		$this->icon               = false;
		$this->has_fields         = true;
		$this->method_title       = '[DEBUG] Confirm Once';
		$this->method_description = 'Debugging gateway that only confirms once per user.';
		$this->supports           = array(
			'subscriptions',
			'refunds',
			'products',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'multiple_subscriptions',
		);

		// settings
		$this->title       = '[DEBUG] Confirm Once';
		$this->description = 'Debugging gateway that only confirms once per user';

		// Load the uses from the option.
		$this->uses = get_option( self::OPTION_KEY, array() );
	}

	/**
	 * Checks if a user has already used the gateway.
	 *
	 * @param string $email
	 *
	 * @return boolean
	 */
	private function has_user_used( string $email ): bool {
		return in_array( $email, $this->uses, true );
	}

	/**
	 * Adds a user to the list of users who have used the gateway.
	 *
	 * @param string $email
	 *
	 * @return void
	 */
	private function add_user( string $email ): void {
		$this->uses[] = $email;
		update_option( self::OPTION_KEY, $this->uses );
	}


	/**
	 * Builds our payment fields area - including tokenization fields for logged
	 * in users, and the actual payment fields.
	 *
	 * @since 2.6.0
	 */
	public function payment_fields() {
		echo '<p>THIS IS A DEBUGGING GATEWAY AND SHOULD NEVER BE USED IN PRODUCTION!</p>';
		echo '<p>Only confirms payment once, any recurring payments will fail</p>';
		echo '<p>This is based on email, so attempting a new order with an existing email address will see it fail straight away</p>';
	}

	/**
	 * Confirm payemnt.
	 *
	 * @return void
	 */
	public function validate_fields() {
		return true;
	}

	/**
	 * Process order payment
	 *
	 * @param integer $order_id
	 * @return void
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		// Check if the user has already used the gateway.
		if ( $this->has_user_used( $order->get_billing_email() ) ) {
			throw new \Exception( 'Payment was rejected by the [DEBUG] Always Confirm gateway, as the user has already used it.' );
		}

		$order->payment_complete();
		wc_reduce_stock_levels( $order_id );
		$order->add_order_note( 'Order paid with [DEBUG] Always Confirm gateway.', true );

		// Add the user to the list of users who have used the gateway.
		$this->add_user( $order->get_billing_email() );

		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
