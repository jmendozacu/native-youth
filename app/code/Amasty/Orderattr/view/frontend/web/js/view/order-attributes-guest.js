/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_Orderattr
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'uiRegistry',
        'Amasty_Orderattr/js/action/save-order-attributes-to-quote'
    ],
    function (
        $,
        ko,
        Component,
        _,
        registry,
        saveOrderAttributesToQuote
    ) {

        return Component.extend({
            
            isVisible: ko.observable(false),

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();

                registry.async('checkoutProvider')(function (checkoutProvider) {

                    checkoutProvider.on('shippingAddress.custom_attributes', function (orderAttributes) {
                        saveOrderAttributesToQuote(orderAttributes);
                    });
                    checkoutProvider.on('shippingAddress.custom_attributes_beforemethods', function (orderAttributes) {
                        saveOrderAttributesToQuote(orderAttributes);
                    });/*
                    setTimeout(function() {
                        saveOrderAttributesToQuote(null);
                    }, 2500)*/

                    jQuery('body').on(
                        {'click': function(){
                            var source = registry.get('checkoutProvider')
                            saveOrderAttributesToQuote(source.get('shippingAddress.custom_attributes_beforemethods'));
                        }},
                        "#billing-address-same-as-shipping-checkmo, .action.action-update"
                    );

                });

                return this;
            },

            initObservable: function () {
                this._super()
                    .observe({
                        isBoolean: false
                    });
                return this;
            }
        });
    }
);
