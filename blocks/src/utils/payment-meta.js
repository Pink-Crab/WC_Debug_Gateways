/**
 * External dependencies
 */
import { useState } from '@wordpress/element';
import { Button, TextControl, Flex, FlexBlock, FlexItem } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';


/**
 * Payment Meta Component
 *
 * @param {eventRegistration} eventRegistration Event registration object.
 * @param {emitResponse} emitResponse Emit response object.
 * @returns {WPElement} Payment component.
 */
const PaymentMetaComponent = ({
    eventRegistration,
    emitResponse,
}) => {
    const [paymentMeta, setPaymentMeta] = useState({});

    const [newMetaKey, setNewMetaKey] = useState('');
    const [newMetaValue, setNewMetaValue] = useState('');

    const { onPaymentSetup } = eventRegistration;

    useEffect(() => {
        // Handle form submission.
        const unsubscribe = onPaymentSetup(async () => {
            // Pass all meta to the server.
            return {
                type: emitResponse.responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        "pc_always_confirm_meta": JSON.stringify(paymentMeta)
                    }
                },
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
        paymentMeta
    ]);

    /**
     * Adds an key and value to the payment meta object.
     * 
     * @param {string} key Key to add
     * @param {string} value Value to add
     * @return {void}
     */
    const addPaymentMeta = (key, value) => {
        setPaymentMeta({ ...paymentMeta, [key]: value });
    }

    /**
     * Renders the payment meta in a list.
     * 
     * @return {WPElement} List of payment meta.
     */
    const renderPaymentMeta = () => {
        return (
            <div>
                <ul>
                    {Object.keys(paymentMeta).map((key) => {
                        return (
                            <li key={key} id={key} className='payment-meta-row'>
                                <Flex>
                                    <FlexItem>
                                        <strong>{key}:</strong>
                                    </FlexItem>
                                    <FlexBlock>
                                        {paymentMeta[key]}
                                    </FlexBlock>
                                    <FlexItem>
                                        <Button onClick={() => {

                                            const newMeta = { ...paymentMeta };
                                            delete newMeta[key];
                                            setPaymentMeta(newMeta);
                                        }
                                        }>{__("Remove", "pinkcrab-debug-gateways")}</Button>
                                    </FlexItem>
                                </Flex>
                            </li>
                        );
                    })}
                </ul>
                <p>{__('Add Meta Key/Value', 'pinkcrab-debug-gateways')}</p>
                <Flex>
                    <FlexItem>
                        <TextControl
                            placeholder={__("Meta Key", "pinkcrab-debug-gateways")}
                            value={newMetaKey}
                            onChange={(value) => setNewMetaKey(value)}
                        />
                    </FlexItem>
                    <FlexBlock>
                        <TextControl
                            placeholder={__("Meta Value", "pinkcrab-debug-gateways")}
                            value={newMetaValue}
                            onChange={(value) => setNewMetaValue(value)}
                            style={{ width: '100%' }}
                        />
                    </FlexBlock>
                    <FlexItem>
                        <Button
                            onClick={() => {
                                // If meta key or value is empty, return.
                                if (newMetaKey === '' || newMetaValue === '') {
                                    return;
                                }

                                addPaymentMeta(newMetaKey, newMetaValue);
                                setNewMetaKey('');
                                setNewMetaValue('');
                            }}
                            variant="secondary"
                            sizw="small"
                        >{__("Add", "pinkcrab-debug-gateways")}</Button>
                    </FlexItem>
                </Flex>
            </div >
        );
    }

    // Write meta to order


    return (
        <>
            <div className="wc-block-gateway-container wc-inline-card-element">

                {renderPaymentMeta()}
            </div>
        </>
    );
};

export const PaymentMeta = (props) => {
    return (
        <PaymentMetaComponent {...props} />
    );
};