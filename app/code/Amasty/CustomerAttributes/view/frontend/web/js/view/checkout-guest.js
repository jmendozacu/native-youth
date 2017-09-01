define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'uiRegistry',
    ],
    function (
        $,
        ko,
        Component,
        quote,
        registry

    ) {
        return Component.extend({
            isVisible: ko.observable(false),

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();
                var self = this;

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    checkoutProvider.on('shippingAddress.custom_attributes', function (customerAttributes) {
                        self.saveCustomerAttributesToQuote(customerAttributes);
                    });

                });

                return this;
            },

            saveCustomerAttributesToQuote: function (customerAttributes) {
                var shippingAddress = quote.shippingAddress();
                if (shippingAddress) {
                    $.each(customerAttributes, function(index, value) {
                        if (typeof(value) == 'object') {
                            customerAttributes[index] = customerAttributes[index].join(',');
                        }


                    });
                    shippingAddress.custom_attributes = $.extend(
                        {}, shippingAddress.custom_attributes, customerAttributes
                    );
                    quote.shippingAddress(shippingAddress);
                }
            }
        });
    }
);
