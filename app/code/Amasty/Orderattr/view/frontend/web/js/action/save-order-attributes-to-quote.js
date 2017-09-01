/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_Orderattr
 */

/*global define,alert*/
define(
    [
        'jquery',
        'ko',
        'underscore',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, ko, _, quote) {
        'use strict';
        return function (orderAttributes) {
            for (var propertyName in orderAttributes) {
                if (_.isArray(orderAttributes[propertyName])) {
                    orderAttributes[propertyName] = orderAttributes[propertyName].join(',');
                }
            }

            /*remove blocker from shipping*/
            var element = $('li#opc-shipping_method.checkout-shipping-method');
            element.find('input:not("._disabled"), select:not("._disabled")').prop('disabled', false);
            element.find('input:disabled, select:disabled').removeClass('_disabled');

            var shippingAddress = quote.shippingAddress();
            if (shippingAddress) {
                if (window.attributesBeforeLoading) {
                    shippingAddress.custom_attributes = $.extend(
                        {}, shippingAddress.custom_attributes, window.attributesBeforeLoading
                    );
                }
                shippingAddress.custom_attributes = $.extend(
                    {}, shippingAddress.custom_attributes, orderAttributes
                );
                quote.shippingAddress(shippingAddress);
            }
            else{
                /*save values before initialization shipping methods*/
                if(!window.attributesBeforeLoading){
                    window.attributesBeforeLoading = [];
                }
                for (var propertyName in orderAttributes) {
                    window.attributesBeforeLoading[propertyName] = orderAttributes[propertyName];
                }
            }

            var billingAddress = quote.billingAddress();
            if (billingAddress) {
                billingAddress.custom_attributes = $.extend(
                    {}, billingAddress.custom_attributes, orderAttributes
                );
                quote.billingAddress(billingAddress);
            }
        };
    }
);
