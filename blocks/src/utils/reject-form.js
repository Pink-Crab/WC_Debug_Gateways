/**
 * External dependencies
 */
import { useState } from '@wordpress/element';
import { RadioControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';


/**
 * Component for the Reject Form.
 *
 * @param {eventRegistration} eventRegistration Event registration object.
 * @param {emitResponse} emitResponse Emit response object.
 * @returns {WPElement} Payment component.
 */
const RejectFormComponent = ({
    eventRegistration,
    emitResponse,
}) => {
    const [rejectClient, setRejectClient] = useState(false);


    const { onPaymentSetup } = eventRegistration;

    useEffect(() => {
        // Handle form submission.
        const unsubscribe = onPaymentSetup(async () => {
            if (rejectClient) {
                return {
                    type: emitResponse.responseTypes.ERROR,
                    error: {
                        message: 'Client side rejection',
                    },
                };
            }



            // Allow payment to proceed, this will still be rejected client side.
            return {
                type: emitResponse.responseTypes.SUCCESS,
            };
        });
        // Unsubscribes when this component is unmounted.
        return () => {
            unsubscribe();
        };
    }, [
        emitResponse.responseTypes.ERROR,
        emitResponse.responseTypes.SUCCESS,
        onPaymentSetup,
        rejectClient
    ]);

    // This exists because native checkout seems to have labels with position: absolute
    const css = `
    .pc-gateway-reject label {
        position: inherit;
    }
    `;

    return (
        <>
            <style>{css}</style>
            <div className="wc-block-gateway-container wc-inline-card-element pc-gateway-reject">
                <RadioControl
                    label="Rejection Type"
                    help="Should the form reject the user on the client side or server side?"
                    selected={rejectClient ? 'c' : 's'}
                    options={[
                        { label: 'Client Side', value: 'c' },
                        { label: 'Server Side', value: 's' },
                    ]}
                    onChange={(value) => setRejectClient(value === 'c')}
                />

            </div>
        </>
    );
};

export const RejectForm = (props) => {
    return (
        <RejectFormComponent {...props} />
    );
};