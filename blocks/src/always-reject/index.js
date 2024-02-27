import {
    registerPaymentMethod,
    registerExpressPaymentMethod,
} from '@woocommerce/blocks-registry';

import { getSetting } from '@woocommerce/settings';
import { __ } from '@wordpress/i18n';
import { RejectForm } from '../utils/reject-form';

const gatewayDetails = () => {
    return getSetting('pc_always_reject_data', {});
}
console.log(gatewayDetails());

const Always_Reject_Block_Gateway = {
    name: 'pc_always_reject',
    label: gatewayDetails().title,
    content: <RejectForm />,
    edit: <RejectForm />,
    canMakePayment: () => true,
    ariaLabel: gatewayDetails().title,
    supports: {
        features: gatewayDetails().supports,
    },
};
registerPaymentMethod(Always_Reject_Block_Gateway);