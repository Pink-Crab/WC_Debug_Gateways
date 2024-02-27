// /**
//  * Registers a new block provided a unique name and an object defining its behavior.
//  *
//  * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
//  */
// import { registerBlockType } from '@wordpress/blocks';


// /**
//  * Internal dependencies
//  */
// import Edit from './edit';
// import save from './save';
// import metadata from './block.json';

// /**
//  * Every block starts by registering a new block type definition.
//  *
//  * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
//  */
// registerBlockType( metadata.name, {
// 	/**
// 	 * @see ./edit.js
// 	 */
// 	edit: Edit,

// 	/**
// 	 * @see ./save.js
// 	 */
// 	save,
// } );

/**
 * External dependencies
 */
// import {
// 	registerPaymentMethod,
// 	registerExpressPaymentMethod,
// } from wc.wcBlocksRegistry;
import {
    registerPaymentMethod,
    registerExpressPaymentMethod,
} from '@woocommerce/blocks-registry';

import { getSetting } from '@woocommerce/settings';
import { __ } from '@wordpress/i18n';
import { PaymentMeta } from '../utils/payment-meta';

const gatewayDetails = () => {
    return getSetting('pc_always_confirm_data', {});
}
console.log(gatewayDetails());

const Always_Confirm_Block_Gateway = {
    name: 'pc_always_confirm',
    label: gatewayDetails().title,
    content: <PaymentMeta />,
    edit: <PaymentMeta />,
    canMakePayment: () => true,
    ariaLabel: gatewayDetails().title,
    supports: {
        features: gatewayDetails().supports,
    },
};
registerPaymentMethod(Always_Confirm_Block_Gateway);