/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (http://www.amasty.com)
 * @package Amasty_Orderattr
 */

define([
    'ko',
    'underscore',
    'mageUtils',
    'uiClass',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/quote',
], function (ko, _, utils, Class, shippingService, quote) {
    'use strict';

    return Class.extend({

        element: null,

        initialize: function (element) {
            this.element = element;
        },

        observeShippingMethods: function () {

            shippingService.getShippingRates().subscribe(this.toggleVisibility, this);
            quote.shippingMethod.subscribe(this.toggleVisibilityForRate, this);

            return this;
        },

        toggleVisibility: function(rates) {
            _.some(rates, function(rate) {
                return this.toggleVisibilityForRate(rate);
            }, this);
        },

        toggleVisibilityForRate: function (rate) {
            if (rate.carrier_code && rate.method_code) {
                var shippingMethodCode = rate.carrier_code +'_' +rate.method_code;
                var visible = this.getShippingMethods() == 0 || this.getShippingMethods().indexOf(shippingMethodCode) > -1;
                this.element.visible(visible);
                return visible;
            }
        },

        getShippingMethods: function() {
            return this.element.shipping_methods;
        }

    });
});
