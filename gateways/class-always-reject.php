<?php

/**
 * Gateway that always gives a rejection of payment made
 *
 * @author  Glynn Quelch<glynn@pinkcrab.co.uk>
 */

namespace PinkCrab\Debug_Gateway\Gateway;

/**
 * Always rejected gateways.
*/
class PinkCrab_Always_Reject_Gateway extends \WC_Payment_Gateway {


	public function __construct() {

		// details
		$this->id                 = 'pc_always_reject';
		$this->icon               = false;
		$this->has_fields         = true;
		$this->method_title       = '[DEBUG] Always Reject';
		$this->method_description = 'Debugging gateway that always rejects as payment made.';
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
		$this->title       = '[DEBUG] Always Reject';
		$this->description = 'Debugging gateway that always rejects as payment made.';
	}

	/**
	 * Builds our payment fields area - including tokenization fields for logged
	 * in users, and the actual payment fields.
	 *
	 * @since 2.6.0
	 */
	public function payment_fields() {

		echo '<p>THIS IS A DEBUGGING GATEWAY AND SHOULD NEVER BE USED IN PRODUCTION!</p>';

	}

	/**
	 * Confirm payemnt.
	 *
	 * @return void
	 */
	public function validate_fields() {
		return false;
	}

	/**
	 * Process order payment
	 *
	 * @param integer $order_id
	 * @return void
	 */
	public function process_payment( $order_id ) {
        throw new \Exception( 'Payment was rejected by the [DEBUG] Always REJECT gateway.' );
        
		$order = wc_get_order( $order_id );
		$order->payment_complete();
		wc_reduce_stock_levels( $order_id );
		$order->add_order_note( 'Order paid with [DEBUG] Always REJECT gateway.', true );

		

		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
