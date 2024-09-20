/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and 
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ){
/**
 * Dynamic Internal/Embedded Style for a Control
 */
function amaz_store_add_dynamic_css( control, style ){
      control = control.replace( '[', '-' );
      control = control.replace( ']', '' );
      jQuery( 'style#' + control ).remove();

      jQuery( 'head' ).append(
            '<style id="' + control + '">' + style + '</style>'
      );
}
/**
 * Responsive Spacing CSS
 */
function amaz_store_responsive_spacing( control, selector, type, side ){
	wp.customize( control, function( value ){
		value.bind( function( value ){
			var sidesString = "";
			var spacingType = "padding";
			if ( value.desktop.top || value.desktop.right || value.desktop.bottom || value.desktop.left || value.tablet.top || value.tablet.right || value.tablet.bottom || value.tablet.left || value.mobile.top || value.mobile.right || value.mobile.bottom || value.mobile.left ) {
				if ( typeof side != undefined ) {
					sidesString = side + "";
					sidesString = sidesString.replace(/,/g , "-");
				}
				if ( typeof type != undefined ) {
					spacingType = type + "";
				}
				// Remove <style> first!
				control = control.replace( '[', '-' );
				control = control.replace( ']', '' );
				jQuery( 'style#' + control + '-' + spacingType + '-' + sidesString ).remove();

				var desktopPadding = '',
					tabletPadding = '',
					mobilePadding = '';

				var paddingSide = ( typeof side != undefined ) ? side : [ 'top','bottom','right','left' ];

				jQuery.each(paddingSide, function( index, sideValue ){
					if ( '' != value['desktop'][sideValue] ) {
						desktopPadding += spacingType + '-' + sideValue +': ' + value['desktop'][sideValue] + value['desktop-unit'] +';';
					}
				});

				jQuery.each(paddingSide, function( index, sideValue ){
					if ( '' != value['tablet'][sideValue] ) {
						tabletPadding += spacingType + '-' + sideValue +': ' + value['tablet'][sideValue] + value['tablet-unit'] +';';
					}
				});

				jQuery.each(paddingSide, function( index, sideValue ){
					if ( '' != value['mobile'][sideValue] ) {
						mobilePadding += spacingType + '-' + sideValue +': ' + value['mobile'][sideValue] + value['mobile-unit'] +';';
					}
				});

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '-' + spacingType + '-' + sidesString + '">'
					+ selector + '	{ ' + desktopPadding +' }'
					+ '@media (max-width: 768px) {' + selector + '	{ ' + tabletPadding + ' } }'
					+ '@media (max-width: 544px) {' + selector + '	{ ' + mobilePadding + ' } }'
					+ '</style>'
				);

			} else {
				wp.customize.preview.send( 'refresh' );
				jQuery( 'style#' + control + '-' + spacingType + '-' + sidesString ).remove();
			}

		} );
	} );
}
/**
 * Apply CSS for the element
 */
function amaz_store_css( control, css_property, selector, unit ){

	wp.customize( control, function( value ) {
		value.bind( function( new_value ) {

			// Remove <style> first!
			control = control.replace( '[', '-' );
			control = control.replace( ']', '' );

			if ( new_value ){
				/**
				 *	If ( unit == 'url' ) then = url('{VALUE}')
				 *	If ( unit == 'px' ) then = {VALUE}px
				 *	If ( unit == 'em' ) then = {VALUE}em
				 *	If ( unit == 'rem' ) then = {VALUE}rem.
				 */
				if ( 'undefined' != typeof unit) {
					if ( 'url' === unit ) {
						new_value = 'url(' + new_value + ')';
					} else {
						new_value = new_value + unit;
					}
				}

				// Remove old.
				jQuery( 'style#' + control ).remove();

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '">'
					+ selector + '	{ ' + css_property + ': ' + new_value + ' }'
					+ '</style>'
				);

			} else {

				wp.customize.preview.send( 'refresh' );

				// Remove old.
				jQuery( 'style#' + control ).remove();
			}

		} );
	} );
}
/*******************************/
// Range slider live customizer
/*******************************/
function amazStoreGetCss( arraySizes, settings, to ) {
    'use strict';
    var data, desktopVal, tabletVal, mobileVal,
        className = settings.styleClass, i = 1;

    var val = JSON.parse( to );
    if ( typeof( val ) === 'object' && val !== null ) {
        if ('desktop' in val) {
            desktopVal = val.desktop;
        }
        if ('tablet' in val) {
            tabletVal = val.tablet;
        }
        if ('mobile' in val) {
            mobileVal = val.mobile;
        }
    }

    for ( var key in arraySizes ) {
        // skip loop if the property is from prototype
        if ( ! arraySizes.hasOwnProperty( key )) {
            continue;
        }
        var obj = arraySizes[key];
        var limit = 0;
        var correlation = [1,1,1];
        if ( typeof( val ) === 'object' && val !== null ) {

            if( typeof obj.limit !== 'undefined'){
                limit = obj.limit;
            }

            if( typeof obj.correlation !== 'undefined'){
                correlation = obj.correlation;
            }

            data = {
                desktop: ( parseInt(parseFloat( desktopVal ) / correlation[0]) + obj.values[0]) > limit ? ( parseInt(parseFloat( desktopVal ) / correlation[0]) + obj.values[0] ) : limit,
                tablet: ( parseInt(parseFloat( tabletVal ) / correlation[1]) + obj.values[1] ) > limit ? ( parseInt(parseFloat( tabletVal ) / correlation[1]) + obj.values[1] ) : limit,
                mobile: ( parseInt(parseFloat( mobileVal ) / correlation[2]) + obj.values[2] ) > limit ? ( parseInt(parseFloat( mobileVal ) / correlation[2]) + obj.values[2] ) : limit
            };
        } else {
            if( typeof obj.limit !== 'undefined'){
                limit = obj.limit;
            }

            if( typeof obj.correlation !== 'undefined'){
                correlation = obj.correlation;
            }
            data =( parseInt( parseFloat( to ) / correlation[0] ) ) + obj.values[0] > limit ? ( parseInt( parseFloat( to ) / correlation[0] ) ) + obj.values[0] : limit;
        }
        settings.styleClass = className + '-' + i;
        settings.selectors  = obj.selectors;

        amazStoreSetCss( settings, data );
        i++;
    }
}
function amazStoreSetCss( settings, to ){
    'use strict';
    var result     = '';
    var styleClass = jQuery( '.' + settings.styleClass );
    if ( to !== null && typeof to === 'object' ){
        jQuery.each(
            to, function ( key, value ) {
                var style_to_add;
                if ( settings.selectors === '.container' ){
                    style_to_add = settings.selectors + '{ ' + settings.cssProperty + ':' + value + settings.propertyUnit + '; max-width: 100%; }';
                } else {
                    style_to_add = settings.selectors + '{ ' + settings.cssProperty + ':' + value + settings.propertyUnit + '}';
                }
                switch ( key ) {
                    case 'desktop':
                        result += style_to_add;
                        break;
                    case 'tablet':
                        result += '@media (max-width: 767px){' + style_to_add + '}';
                        break;
                    case 'mobile':
                        result += '@media (max-width: 544px){' + style_to_add + '}';
                        break;
                }
            }
        );
        if ( styleClass.length > 0 ) {
            styleClass.text( result );
        } else {
            jQuery( 'head' ).append( '<style type="text/css" class="' + settings.styleClass + '">' + result + '</style>' );
        }
    } else {
        jQuery( settings.selectors ).css( settings.cssProperty, to + 'px' );
    }
}
//*****************************/
// Logo
//*****************************/
wp.customize(
    'amaz_store_logo_width', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'max-width',
                    propertyUnit: 'px',
                    styleClass: 'open-logo-width'
                };

                var arraySizes = {
                    size3: { selectors:'.thunk-logo img,.sticky-header .logo-content img', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);
//top header
wp.customize('amaz_store_col1_texthtml', function(value){
         value.bind(function(to){
             $('.top-header-col1 .content-html').text(to);
         });
     });
 wp.customize('amaz_store_col2_texthtml', function(value){
         value.bind(function(to) {
             $('.top-header-col2 .content-html').text(to);
         });
     });
 wp.customize('amaz_store_col3_texthtml', function(value){
         value.bind(function(to) {
             $('.top-header-col3 .content-html').text(to);
         });
     });
amaz_store_css( 'amaz_store_abv_hdr_botm_brd','border-bottom-width', '.top-header', 'px' );
amaz_store_css( 'amaz_store_above_brdr_clr','border-bottom-color', '.top-header');
wp.customize(
    'amaz_store_abv_hdr_botm_brd', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'border-bottom-width',
                    propertyUnit: 'px',
                    styleClass: 'amaz_store_abv_hdr_botm_brd'
                };

                var arraySizes = {
                    size3: { selectors:'.top-header', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);
wp.customize(
    'amaz_store_abv_hdr_hgt', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'line-height',
                    propertyUnit: 'px',
                    styleClass: 'amaz_store_abv_hdr_hgt'
                };

                var arraySizes = {
                    size3: { selectors:'.top-header .top-header-bar', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);
/***************/
// MAIN HEADER
/***************/
wp.customize('amaz_store_main_hdr_btn_txt', function(value){
         value.bind(function(to){
             $('.btn-main-header').text(to);
         });
});
wp.customize('amaz_store_main_hdr_calto_txt', function(value){
         value.bind(function(to){
             $('.sprt-tel b').text(to);
         });
});
wp.customize('amaz_store_main_hdr_calto_nub', function(value){
         value.bind(function(to){
             $('.sprt-tel a').text(to);
         });
});
wp.customize('amaz_store_main_hdr_calto_nub', function(value){
         value.bind(function(to){
             $('.sprt-tel a').text(to);
         });
});

wp.customize('amaz_store_main_hdr_cat_txt', function(value){
         value.bind(function(to){
             $('.toggle-title').text(to);
         });
});

// blog
wp.customize('amaz_store_blog_read_more_txt', function(value){
         value.bind(function(to){
             $('a.thunk-readmore.button ').text(to);
         });
     });
/****************/
// footer
/****************/
wp.customize('amaz_store_footer_col1_texthtml', function(value){
         value.bind(function(to) {
             $('.top-footer-col1 .content-html').text(to);
         });
     });
 wp.customize('amaz_store_above_footer_col2_texthtml', function(value){
         value.bind(function(to) {
             $('.top-footer-col2 .content-html').text(to);
         });
     });
 wp.customize('amaz_store_above_footer_col3_texthtml', function(value){
         value.bind(function(to) {
             $('.top-footer-col3 .content-html').text(to);
         });
     });
amaz_store_css( 'amaz_store_above_frt_brdr_clr','border-bottom-color', 'body.amaz-store-dark .top-footer,.top-footer');
wp.customize(
    'amaz_store_abv_ftr_botm_brd', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'border-bottom-width',
                    propertyUnit: 'px',
                    styleClass: 'amaz_store_abv_ftr_botm_brd'
                };

                var arraySizes = {
                    size3: { selectors:'.top-footer', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);
wp.customize(
    'amaz_store_above_ftr_hgt', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'line-height',
                    propertyUnit: 'px',
                    styleClass: 'amaz_store_above_ftr_hgt'
                };

                var arraySizes = {
                    size3: { selectors:'.top-footer .top-footer-bar', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);

 wp.customize('amaz_store_footer_bottom_col1_texthtml', function(value){
         value.bind(function(to) {
             $('.below-footer-col1 .content-html').text(to);
         });
     });
 wp.customize('amaz_store_bottom_footer_col2_texthtml', function(value){
         value.bind(function(to) {
             $('.below-footer-col2 .content-html').text(to);
         });
     });
 wp.customize('amaz_store_bottom_footer_col3_texthtml', function(value){
         value.bind(function(to) {
             $('.below-footer-col3 .content-html').text(to);
         });
     });
amaz_store_css( 'amaz_store_bottom_frt_brdr_clr','border-top-color', '.below-footer,body.amaz-store-dark .below-footer');
wp.customize(
    'amaz_store_btm_ftr_botm_brd', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'border-top-width',
                    propertyUnit: 'px',
                    styleClass: 'amaz_store_btm_ftr_botm_brd'
                };

                var arraySizes = {
                    size3: { selectors:'.below-footer', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);
wp.customize(
    'amaz_store_btm_ftr_hgt', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'line-height',
                    propertyUnit: 'px',
                    styleClass: 'amaz_store_btm_ftr_hgt'
                };

                var arraySizes = {
                    size3: { selectors:'.below-footer .below-footer-bar', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);
// loader
amaz_store_css( 'amaz_store_loader_bg_clr','background-color','.amaz_store_overlayloader');
//*****************************/
// Global Color Custom Style
//*****************************/
wp.customize( 'amaz_store_theme_clr', function( setting ){
        setting.bind( function( cssval ){
                var dynamicStyle = '';
                 dynamicStyle += 'a:hover, .amaz-store-menu li a:hover, .amaz-store-menu .current-menu-item a,.summary .yith-wcwl-add-to-wishlist.show .add_to_wishlist::before, .summary .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse.show a::before, .summary .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse.show a::before,.woocommerce .entry-summary a.compare.button.added:before,.header-icon a:hover,.thunk-related-links .nav-links a:hover,.woocommerce .thunk-list-view ul.products li.product.thunk-woo-product-list .price,.woocommerce .woocommerce-error .button, .woocommerce .woocommerce-info .button, .woocommerce .woocommerce-message .button,article.thunk-post-article .thunk-readmore.button,.thunk-wishlist a:hover, .thunk-compare a:hover,.woocommerce ul.cart_list li .woocommerce-Price-amount, .woocommerce ul.product_list_widget li .woocommerce-Price-amount,.amaz-store-load-more button,.page-contact .leadform-show-form label,.thunk-contact-col .fa,.summary .yith-wcwl-wishlistaddedbrowse a, .summary .yith-wcwl-wishlistexistsbrowse a,.thunk-title .title:before,.thunk-hglt-icon:hover,.woocommerce .thunk-product-content .star-rating,.thunk-product-cat-list.slider a:hover, .thunk-product-cat-list li a:hover,.site-title span a:hover,.header-support-icon a:hover span,.cart-icon a span:hover,.header-support-content i,.slider-cat-title a:before,.thunk-icon .cart-icon a.cart-contents:hover{ color: ' + cssval + '} ';
                 dynamicStyle += '.single_add_to_cart_button.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit, .woocommerce button.button, .woocommerce input.button,.cat-list a:after,.tagcloud a:hover, .thunk-tags-wrapper a:hover,.ribbon-btn,.btn-main-header,.page-contact .leadform-show-form input[type="submit"],.woocommerce .widget_price_filter .amaz-store-widget-content .ui-slider .ui-slider-range,.woocommerce .widget_price_filter .amaz-store-widget-content .ui-slider .ui-slider-handle,.entry-content form.post-password-form input[type="submit"],#amazstore-mobile-bar a,#amazstore-mobile-bar,.post-slide-widget .owl-carousel .owl-nav button:hover,.woocommerce div.product form.cart .button,#search-button,#search-button:hover,.cart-contents .count-item, .woocommerce ul.products li.product .button,.slide-layout-1 .slider-content-caption a.slide-btn,.slider-content-caption a.slide-btn,.page-template-frontpage .owl-carousel button.owl-dot, .woocommerce #alm-quick-view-modal .alm-qv-image-slider .flex-control-paging li a,.button.return.wc-backward,.button.return.wc-backward:hover,.woocommerce .thunk-product-hover a.add_to_cart_button:hover,.woocommerce .thunk-product-hover .thunk-wishlist a.add_to_wishlist:hover,.thunk-wishlist .yith-wcwl-wishlistaddedbrowse:hover,.thunk-wishlist .yith-wcwl-wishlistexistsbrowse:hover,.thunk-quickview a:hover, .thunk-compare .compare-button a.compare.button:hover,.thunk-woo-product-list .thunk-quickview a:hover,.thunk-heading-wrap:before,.thunk-hglt-icon:before,.woocommerce .thunk-woo-product-list span.onsale,a.add_to_cart_button.ajax_add_to_cart,a.add_to_cart_button.ajax_add_to_cart:hover,.cat-icon,#search-box #search-button,.woocommerce a.product_type_variable:hover,.woocommerce #respond input#submit:hover,.woocommerce a.button:hover,.woocommerce button.button:hover,.woocommerce input.button:hover,#move-to-top,.product-type-grouped .add-to-cart .button,.product-type-variable .thunk-product .button,.product-type-variable .thunk-product .add-to-cart > a,#page.amazstore-site .owl-nav  button.owl-prev:hover,#page.amazstore-site .owl-nav  button.owl-next:hover{ background: ' + cssval + '} ';
                 dynamicStyle += '.open-cart p.buttons a:hover,.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce #respond input#submit:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,.thunk-slide .owl-nav button.owl-prev:hover, .thunk-slide .owl-nav button.owl-next:hover, .amaz-store-slide-post .owl-nav button.owl-prev:hover, .amaz-store-slide-post .owl-nav button.owl-next:hover,.thunk-list-grid-switcher a.selected, .thunk-list-grid-switcher a:hover,.woocommerce .woocommerce-error .button:hover, .woocommerce .woocommerce-info .button:hover, .woocommerce .woocommerce-message .button:hover,#searchform [type="submit"]:hover,article.thunk-post-article .thunk-readmore.button:hover,.amaz-store-load-more button:hover,.woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current,.thunk-top2-slide.owl-carousel .owl-nav button:hover,.product-slide-widget .owl-carousel .owl-nav button:hover, .thunk-slide.thunk-brand .owl-nav button:hover,.woocommerce .thunk-product-image-cat-slide .thunk-woo-product-list:hover .thunk-product,#page.amazstore-site .owl-nav  button.owl-prev:hover,#page.amazstore-site .owl-nav  button.owl-next:hover{border-color: ' + cssval + '} ';
                amaz_store_add_dynamic_css( 'amaz_store_theme_clr', dynamicStyle );

        } );
    } );

amaz_store_css( 'amaz_store_text_clr','color','body,.woocommerce-error, .woocommerce-info, .woocommerce-message');
amaz_store_css( 'amaz_store_title_clr','color','.site-title span a,.sprt-tel b,.widget.woocommerce .widget-title, .open-widget-content .widget-title, .widget-title,.wp-block-group h2,h2.thunk-post-title a, h1.thunk-post-title ,#reply-title,h4.author-header,.page-head h1,.woocommerce div.product .product_title, section.related.products h2, section.upsells.products h2, .woocommerce #reviews #comments h2,.woocommerce table.shop_table thead th, .cart-subtotal, .order-total,.cross-sells h2, .cart_totals h2,.woocommerce-billing-fields h3,.page-head h1 a,.widget.woocommerce .widget-title, .amaz-store-widget-content .widget-title, .widget-title, .wp-block-group h2, .amaz-store-widget-content > h2');
amaz_store_css( 'amaz_store_link_clr','color','a,#open-above-menu.amaz-store-menu > li > a,.thunk-cat-tab .tab-link li a');
amaz_store_css( 'amaz_store_link_hvr_clr','color','#open-above-menu.amaz-store-menu > li > a:hover,#open-above-menu.amaz-store-menu li a:hover,.thunk-cat-tab .tab-link li a.active, .thunk-cat-tab .tab-link li a:hover');

//Above Header
// above header bg image
wp.customize('header_image', function (value){
    value.bind(function (to){
        $('.top-header').css('background-image', 'url( '+ to +')');
    });
});

//move to top
amaz_store_css( 'amaz_store_move_to_top_bg_clr','background', '#move-to-top');
amaz_store_css( 'amaz_store_move_to_top_icon_clr','color', '#move-to-top');


//cat slider heading
wp.customize('amaz_store_cat_slider_heading', function(value){
         value.bind(function(to) {
             $('.thunk-category-slide-section .thunk-title .title').text(to);
         });
     });
//product slide
wp.customize('amaz_store_product_slider_heading', function(value){
         value.bind(function(to) {
             $('.thunk-product-slide-section .thunk-title .title').text(to);
         });
     });
//product list
wp.customize('amaz_store_product_list_heading', function(value){
         value.bind(function(to) {
             $('.thunk-product-list-section .thunk-title .title').text(to);
         });
     });
//product cat tab 
wp.customize('amaz_store_cat_tab_heading', function(value){
         value.bind(function(to) {
             $('.thunk-product-tab-section .thunk-title .title').text(to);
         });
     });
//product cat tab list
wp.customize('amaz_store_list_cat_tab_heading', function(value){
         value.bind(function(to) {
             $('.thunk-product-tab-list-section .thunk-title .title').text(to);
         });
     });

//Big Featured
wp.customize('amaz_store_feature_product_heading', function(value){
         value.bind(function(to) {
             $('.thunk-feature-product-section .thunk-title .title').text(to);
         });
     });
//Ribbon Text
wp.customize('amaz_store_ribbon_text', function(value){
         value.bind(function(to) {
             $('.thunk-ribbon-content h3').text(to);
         });
     });
wp.customize('amaz_store_ribbon_btn_text', function(value){
         value.bind(function(to) {
             $('.thunk-ribbon-content a.ribbon-btn').text(to);
         });
     });
//Custom section One
wp.customize('amaz_store_custom_1_heading', function(value){
         value.bind(function(to) {
             $('.thunk-custom-one-section > .thunk-heading-wrap .thunk-title .title').text(to);
         });
     });
//Custom section two
wp.customize('amaz_store_custom_2_heading', function(value){
         value.bind(function(to) {
             $('.thunk-custom-two-section > .thunk-heading-wrap .thunk-title .title').text(to);
         });
     });
//Custom section three
wp.customize('amaz_store_custom_3_heading', function(value){
         value.bind(function(to) {
             $('.thunk-custom-three-section > .thunk-heading-wrap .thunk-title .title').text(to);
         });
     });
//Custom section four
wp.customize('amaz_store_custom_4_heading', function(value){
         value.bind(function(to){
             $('.thunk-custom-four-section > .thunk-heading-wrap .thunk-title .title').text(to);
         });
     });
//Inner Page Header
wp.customize(
    'amaz_store_front_above_header_gap', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'margin-top',
                    propertyUnit: 'px',
                    styleClass: 'amaz_store_front_above_header_gap'
                };

                var arraySizes = {
                    size3: { selectors:'header.front', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);
wp.customize(
    'amaz_store_inner_above_header_gap', function (value){
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'margin-top',
                    propertyUnit: 'px',
                    styleClass: 'amaz_store_inner_above_header_gap'
                };

                var arraySizes = {
                    size3: { selectors:'header.inner', values: ['','',''] }
                };
                amazStoreGetCss( arraySizes, settings, to );
            }
        );
    }
);

})( jQuery );