/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'jquery',
    'jquery/ui',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
  /*  'use strict';
    jQuery(document).ready(function(){
        jQuery('.cart-summary').mage('sticky', {
            container: '#maincontent'
        });

        jQuery('.panel.header .header.links').clone().appendTo('#store\\.links');
    });
    keyboardHandler.apply();*/
});
/******************** Init Parallax ***********************/
require([
    'jquery',
    'jquery/ui',
    'js/jquery.stellar.min'
], function ($) {
    jQuery(document).ready(function(){
        jQuery(window).stellar({
            responsive: true,
            scrollProperty: 'scroll',
            parallaxElements: false,
            horizontalScrolling: false,
            horizontalOffset: 0,
            verticalOffset: 0
        });
    });
});

require([
    'jquery',
    'jquery/ui'
], function ($) {
    (function() {
        var ev = new jQuery.Event('classadded'),
            orig = jQuery.fn.addClass;
        jQuery.fn.addClass = function() {
            jQuery(this).trigger(ev, arguments);
            return orig.apply(this, arguments);
        }
    })();
    jQuery.fn.extend({
        scrollToMe: function(){
            if(jQuery(this).length){
                var top = jQuery(this).offset().top - 100;
                jQuery('html,body').animate({scrollTop: top}, 300);
            }
        },
        scrollToJustMe: function(){
            if(jQuery(this).length){
                var top = jQuery(this).offset().top;
                jQuery('html,body').animate({scrollTop: top}, 300);
            }
        }
    });
    jQuery(document).ready(function(){
        var windowScroll_t;
        jQuery(window).scroll(function(){
            clearTimeout(windowScroll_t);
            windowScroll_t = setTimeout(function(){
                if(jQuery(this).scrollTop() > 100){
                    jQuery('#totop').fadeIn();
                }else{
                    jQuery('#totop').fadeOut();
                }
            }, 500);
        });
        jQuery('#totop').off("click").on("click",function(){
            jQuery('html, body').animate({scrollTop: 0}, 600);
        });
        if (jQuery('body').hasClass('checkout-cart-index')) {
            if (jQuery('#co-shipping-method-form .fieldset.rates').length > 0 && jQuery('#co-shipping-method-form .fieldset.rates :checked').length === 0) {
                jQuery('#block-shipping').on('collapsiblecreate', function () {
                    jQuery('#block-shipping').collapsible('forceActivate');
                });
            }
        }
        jQuery(".products-grid .weltpixel-quickview").each(function(){
            jQuery(this).appendTo(jQuery(this).parent().parent().children(".product-item-photo"));
        });
        jQuery(".word-rotate").each(function() {

            var $this = jQuery(this),
                itemsWrapper = jQuery(this).find(".word-rotate-items"),
                items = itemsWrapper.find("> span"),
                firstItem = items.eq(0),
                firstItemClone = firstItem.clone(),
                itemHeight = 0,
                currentItem = 1,
                currentTop = 0;

            itemHeight = firstItem.height();

            itemsWrapper.append(firstItemClone);

            $this
                .height(itemHeight)
                .addClass("active");

            setInterval(function() {
                currentTop = (currentItem * itemHeight);
                
                itemsWrapper.animate({
                    top: -(currentTop) + "px"
                }, 300, function() {
                    currentItem++;
                    if(currentItem > items.length) {
                        itemsWrapper.css("top", 0);
                        currentItem = 1;
                    }
                });
                
            }, 2000);

        });
        jQuery(".top-links-icon").off("click").on("click", function(e){
            if(jQuery(this).parent().children("ul.links").hasClass("show")) {
                jQuery(this).parent().children("ul.links").removeClass("show");
            } else {
                jQuery(this).parent().children("ul.links").addClass("show");
            }
            e.stopPropagation();
        });
        jQuery(".top-links-icon").parent().click(function(e){
            e.stopPropagation();
        });
        jQuery(".search-toggle-icon").click(function(e){
            if(jQuery(this).parent().children(".block-search").hasClass("show")) {
                jQuery(this).parent().children(".block-search").removeClass("show");
            } else {
                jQuery(this).parent().children(".block-search").addClass("show");
            }
            e.stopPropagation();
        });
        jQuery(".search-toggle-icon").parent().click(function(e){
            e.stopPropagation();
        });
        jQuery("html,body").click(function(){
            jQuery(".search-toggle-icon").parent().children(".block-search").removeClass("show");
            jQuery(".top-links-icon").parent().children("ul.links").removeClass("show");
        });
        
        /********************* Qty Holder **************************/
        jQuery(".qty-inc").unbind('click').click(function(){
            if(jQuery(this).parent().parent().children(".control").children("input.input-text.qty").is(':enabled')){
                jQuery(this).parent().parent().children(".control").children("input.input-text.qty").val((+jQuery(this).parent().parent().children(".control").children("input.input-text.qty").val() + 1) || 0);
                jQuery(this).parent().parent().children(".control").children("input.input-text.qty").trigger('change');
                jQuery(this).focus();
            }
        });
        jQuery(".qty-dec").unbind('click').click(function(){
            if(jQuery(this).parent().parent().children(".control").children("input.input-text.qty").is(':enabled')){
                jQuery(this).parent().parent().children(".control").children("input.input-text.qty").val((jQuery(this).parent().parent().children(".control").children("input.input-text.qty").val() - 1 > 0) ? (jQuery(this).parent().parent().children(".control").children("input.input-text.qty").val() - 1) : 0);
                jQuery(this).parent().parent().children(".control").children("input.input-text.qty").trigger('change');
                jQuery(this).focus();
            }
        });
        
        /********** Fullscreen Slider ************/
        var s_width = jQuery(window).innerWidth();
        var s_height = jQuery(window).innerHeight();
        var s_ratio = s_width/s_height;
        var v_width=320;
        var v_height=240;
        var v_ratio = v_width/v_height;
        jQuery(".full-screen-slider div.item").css("position","relative");
        jQuery(".full-screen-slider div.item").css("overflow","hidden");
        jQuery(".full-screen-slider div.item").width(s_width);
        jQuery(".full-screen-slider div.item").height(s_height);
        jQuery(".full-screen-slider div.item > video").css("position","absolute");
        jQuery(".full-screen-slider div.item > video").bind("loadedmetadata",function(){
            v_width = this.videoWidth;
            v_height = this.videoHeight;
            v_ratio = v_width/v_height;
            if(s_ratio>=v_ratio){
                jQuery(this).width(s_width);
                jQuery(this).height("");
                jQuery(this).css("left","0px");
                jQuery(this).css("top",(s_height-s_width/v_width*v_height)/2+"px");
            }else{
                jQuery(this).width("");
                jQuery(this).height(s_height);
                jQuery(this).css("left",(s_width-s_height/v_height*v_width)/2+"px");
                jQuery(this).css("top","0px");
            }
            jQuery(this).get(0).play();
        });
        if(jQuery(".page-header").hasClass("type10")) {
            if(s_width >= 992){
                jQuery(".navigation").addClass("side-megamenu")
            } else {
                jQuery(".navigation").removeClass("side-megamenu")
            }
        }
        
        jQuery(window).resize(function(){
            s_width = jQuery(window).innerWidth();
            s_height = jQuery(window).innerHeight();
            s_ratio = s_width/s_height;
            jQuery(".full-screen-slider div.item").width(s_width);
            jQuery(".full-screen-slider div.item").height(s_height);
            jQuery(".full-screen-slider div.item > video").each(function(){
                if(s_ratio>=v_ratio){
                    jQuery(this).width(s_width);
                    jQuery(this).height("");
                    jQuery(this).css("left","0px");
                    jQuery(this).css("top",(s_height-s_width/v_width*v_height)/2+"px");
                }else{
                    jQuery(this).width("");
                    jQuery(this).height(s_height);
                    jQuery(this).css("left",(s_width-s_height/v_height*v_width)/2+"px");
                    jQuery(this).css("top","0px");
                }
            });
            if(jQuery(".page-header").hasClass("type10")) {
                if(s_width >= 992){
                    jQuery(".navigation").addClass("side-megamenu")
                } else {
                    jQuery(".navigation").removeClass("side-megamenu")
                }
            }
        });
        var breadcrumb_pos_top = 0;
        jQuery(window).scroll(function(){
            if(!jQuery("body").hasClass("cms-index-index")){
                var side_header_height = jQuery(".page-header.type10").innerHeight();
                var window_height = jQuery(window).height();
                if(side_header_height-window_height<jQuery(window).scrollTop()){
                    if(!jQuery(".page-header.type10").hasClass("fixed-bottom"))
                        jQuery(".page-header.type10").addClass("fixed-bottom");
                }
                if(side_header_height-window_height>=jQuery(window).scrollTop()){
                    if(jQuery(".page-header.type10").hasClass("fixed-bottom"))
                        jQuery(".page-header.type10").removeClass("fixed-bottom");
                }
            }
            if(jQuery("body.side-header .page-wrapper > .breadcrumbs").length){
                if(!jQuery("body.side-header .page-wrapper > .breadcrumbs").hasClass("fixed-position")){
                    breadcrumb_pos_top = jQuery("body.side-header .page-wrapper > .breadcrumbs").offset().top;
                    if(jQuery("body.side-header .page-wrapper > .breadcrumbs").offset().top<jQuery(window).scrollTop()){
                        jQuery("body.side-header .page-wrapper > .breadcrumbs").addClass("fixed-position");
                    }
                }else{
                    if(jQuery(window).scrollTop()<=1){
                        jQuery("body.side-header .page-wrapper > .breadcrumbs").removeClass("fixed-position");
                    }
                }
            }
        });
    });
});