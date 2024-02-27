<?php

/**
 * Gateway that always gives a confirmation of payment made
 *
 * @author  Glynn Quelch<glynn@pinkcrab.co.uk>
 */

namespace PinkCrab\Debug_Gateway\Gateway;

/**
 * Always confirmed gateways.
*/
class PinkCrab_Always_Confirm_Gateway extends \WC_Payment_Gateway {


	public function __construct() {

		// details
		$this->id                 = 'pc_always_confirm';
		$this->icon               = false;
		$this->has_fields         = true;
		$this->method_title       = '[DEBUG] Always Confirm';
		$this->method_description = 'Debugging gateway that always confirms as payment made.';
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
		$this->title       = '[DEBUG] Always Confirm';
		$this->description = 'Debugging gateway that always confirms as payment made.';
	}

	/**
	 * Builds our payment fields area - including tokenization fields for logged
	 * in users, and the actual payment fields.
	 *
	 * @since 2.6.0
	 */
	public function payment_fields() {

		// Hidden fiend used to pass meta to the order.
		echo '<input type="hidden" name="pc_always_confirm_meta" id="pc_always_confirm_meta" value="" />';

		echo '<p>THIS IS A DEBUGGING GATEWAY AND SHOULD NEVER BE USED IN PRODUCTION!</p>';

		// Renders a list of all meta.
		?>
		<div id="pc_always_confirm_meta">
			<h3>Pass custom order meta with order</h3>
			<ul id="pc_always_confirm_meta_list">	
				<li>Custom meta will show here</li>
			</ul>

			<input type="text" id="pc_always_confirm_meta_key" placeholder="Meta Key" />
			<input type="text" id="pc_always_confirm_meta_value" placeholder="Meta Value" />

			<button class="button" id="pc_always_confirm_meta_add">Add Meta</button>

		</div>
		<script type="text/javascript">

				// Add meta to list.
				document.getElementById('pc_always_confirm_meta_add').addEventListener('click', function(event) {
					console.log('Add meta2',event);
					const meta = document.getElementById('pc_always_confirm_meta');
					const metaList = document.getElementById('pc_always_confirm_meta_list');
					const metaInput = document.getElementById('pc_always_confirm_meta');
					const metaKey = document.getElementById('pc_always_confirm_meta_key');
					const metaValue = document.getElementById('pc_always_confirm_meta_value');

					// Prevent default.
					event.preventDefault();
					const key = metaKey.value;
					const value = metaValue.value;

					// If value or key is empty, return.
					if (key === '' || value === '') {
						return;
					}

					const li = document.createElement('li');
					// Add content as key:value with button to remove.
					li.innerHTML = `<span class="key">${key}</span>: <span class="value">${value}</span> <button class="button" onclick="this.parentElement.remove()">Remove</button>`;

					// ?li.innerHTML = `${key}: ${value}`;
					metaList.appendChild(li);

					// Clear input fields.
					metaKey.value = '';
					metaValue.value = '';

					// Update the hidden field with an object of all values in json
					const metaListItems = metaList.querySelectorAll('li');
					const metaObject = {};
					metaListItems.forEach((item) => {
						// Get key from span class="key"
						const key = item.querySelector('.key').innerText;

						// Get value from span class="value"
						const value = item.querySelector('.value').innerText;
						metaObject[key] = value;
					});
					metaInput.value = JSON.stringify(metaObject);
				});


			// Get JSON value from hidden field and popualte the pc_always_confirm_meta_list in native JS
			document.addEventListener('DOMContentLoaded', function() {

				
			});
		</script>
		<?php
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
		$order->payment_complete();
		wc_reduce_stock_levels( $order_id );
		$order->add_order_note( 'Order paid with [DEBUG] Always Confirm gateway.', true );

		// Check if we have custom order meta in post.
		if ( array_key_exists( 'pc_always_confirm_meta', $_POST ) ) {
			$meta = $_POST['pc_always_confirm_meta'];
			// Decode as json.
			$meta = json_decode( stripslashes( $meta ), true );

			// If we have an array, iterate and add to order.
			if ( is_array( $meta ) ) {
				foreach ( $meta as $key => $value ) {
					$order->update_meta_data(
						esc_attr( $key ),
						esc_html( $value )
					);
				}
			}
			$order->save();
		}

		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
