<?php 
/**
 * Custom Style for Amaz Store Theme.
 * @package     Amaz Store
 * @author      ThemeHunk
 * @copyright   Copyright (c) 2021, Amaz Store
 * @since       Amaz Store 1.0.0
 */
function amaz_store_custom_style(){
$amaz_store_style=""; 
$amaz_store_style.= amaz_store_responsive_slider_funct( 'amaz_store_logo_width', 'amaz_store_logo_width_responsive');

/**************************/
// Above Header
/**************************/

    $amaz_store_above_brdr_clr = esc_html(get_theme_mod('amaz_store_above_brdr_clr','#fff'));  
    $amaz_store_style.=".top-header,body.amaz-store-dark .top-header{border-bottom-color:{$amaz_store_above_brdr_clr}}";

    $amaz_store_style.= amaz_store_responsive_slider_funct( 'amaz_store_front_above_header_gap', 'amaz_store_front_above_header_gap_responsive');

    $amaz_store_style.= amaz_store_responsive_slider_funct( 'amaz_store_inner_above_header_gap', 'amaz_store_inner_above_header_gap_responsive');

    $amaz_store_style.= amaz_store_responsive_slider_funct( 'amaz_store_ribbon_margin', 'amaz_store_ribbon_margin_responsive');

  
/**************************/
// Above Fooetr
/**************************/
if (get_theme_mod('amaz_store_above_footer_layout','ft-abv-none') != 'ft-abv-none') {
    $amaz_store_above_frt_brdr_clr = esc_html(get_theme_mod('amaz_store_above_frt_brdr_clr','#fff'));  
    $amaz_store_style.=".top-footer,body.amaz-store-dark .top-footer{border-bottom-color:{$amaz_store_above_frt_brdr_clr}}";
    $amaz_store_style.= amaz_store_responsive_slider_funct( 'amaz_store_above_ftr_hgt', 'amaz_store_top_footer_height_responsive');
    $amaz_store_style.= amaz_store_responsive_slider_funct( 'amaz_store_abv_ftr_botm_brd', 'amaz_store_top_footer_border_responsive');
  }

/**************************/
// Below Footer
/**************************/
if (get_theme_mod('amaz_store_bottom_footer_layout','ft-btm-none') != 'ft-btm-none') {
    $amaz_store_bottom_frt_brdr_clr = esc_html(get_theme_mod('amaz_store_bottom_frt_brdr_clr'));  
    $amaz_store_style.=".below-footer,body.amaz-store-dark .below-footer{border-top-color:{$amaz_store_bottom_frt_brdr_clr}}";
    $amaz_store_style.= amaz_store_responsive_slider_funct( 'amaz_store_btm_ftr_hgt', 'amaz_store_below_footer_height_responsive');
    $amaz_store_style.= amaz_store_responsive_slider_funct( 'amaz_store_btm_ftr_botm_brd', 'amaz_store_below_footer_border_responsive');
  }
/*********************/
// Global Color Option
/*********************/ 
  $amaz_store_theme_clr = esc_html(get_theme_mod('amaz_store_theme_clr','#ff3377'));
  $amaz_store_style.="a:hover, .amaz-store-menu li a:hover, .amaz-store-menu .current-menu-item a,.top-header .top-header-bar .amaz-store-menu li a:hover, .top-header .top-header-bar  .amaz-store-menu .current-menu-item a,.summary .yith-wcwl-add-to-wishlist.show .add_to_wishlist::before, .summary .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse.show a::before, .summary .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse.show a::before,.woocommerce .entry-summary a.compare.button.added:before,.header-icon a:hover,.thunk-related-links .nav-links a:hover,.woocommerce .thunk-list-view ul.products li.product.thunk-woo-product-list .price,.woocommerce .woocommerce-error .button, .woocommerce .woocommerce-info .button, .woocommerce .woocommerce-message .button,article.thunk-post-article .thunk-readmore.button,.thunk-wishlist a:hover, .thunk-compare a:hover,.woocommerce ul.cart_list li .woocommerce-Price-amount, .woocommerce ul.product_list_widget li .woocommerce-Price-amount,.amaz-store-load-more button,.page-contact .leadform-show-form label,.thunk-contact-col .fa,.summary .yith-wcwl-wishlistaddedbrowse a, .summary .yith-wcwl-wishlistexistsbrowse a,.thunk-title .title:before,.thunk-hglt-icon,.woocommerce .thunk-product-content .star-rating,.thunk-product-cat-list.slider a:hover, .thunk-product-cat-list li a:hover,.site-title span a:hover,.cart-icon a span:hover,.thunk-product-list-section .thunk-list .thunk-product-content .woocommerce-LoopProduct-title:hover, .thunk-product-tab-list-section .thunk-list .thunk-product-content .woocommerce-LoopProduct-title:hover,.thunk-woo-product-list .woocommerce-loop-product__title a:hover,.mobile-nav-tab-category ul[data-menu-style='accordion'] li a:hover, .amaz-store-menu > li > a:hover, .top-header-bar .amaz-store-menu > li > a:hover, .bottom-header-bar .amaz-store-menu > li > a:hover, .amaz-store-menu li ul.sub-menu li a:hover,.header-support-content i,.slider-cat-title a:before,[type='submit'],.header-support-content a:hover,.mhdrthree .site-title span a:hover,.mobile-nav-bar .amaz-store-menu > li > a:hover,.woocommerce .widget_rating_filter ul li .star-rating,.woocommerce .star-rating::before,.woocommerce .widget_rating_filter ul li a,.search-close-btn,.woocommerce .thunk-single-product-summary-wrap .woocommerce-product-rating .star-rating,.woocommerce #alm-quick-view-modal .woocommerce-product-rating .star-rating,.summary .woosw-added:before,.thunk-product .woosw-btn.woosw-added,.thunk-icon .cart-icon a.cart-contents:hover{color:{$amaz_store_theme_clr};}  .woocommerce a.remove:hover,.thunk-vertical-cat-tab .thunk-heading-wrap:before,.slide-layout-1 .slider-content-caption a.slide-btn,.above-header-content .desktop-main-header .taiowc-cart-item,.above-header-content .desktop-main-header .taiowcp-cart-item{background:{$amaz_store_theme_clr}!important;} 

    .widget_amaz_store_tabbed_product_widget .thunk-woo-product-list:hover .thunk-product{border-color:{$amaz_store_theme_clr};}";

  $amaz_store_style.=".single_add_to_cart_button.button.alt, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit, .woocommerce button.button, .woocommerce input.button,.cat-list a:after,.tagcloud a:hover, .thunk-tags-wrapper a:hover,.btn-main-header,.page-contact .leadform-show-form input[type='submit'],.woocommerce .widget_price_filter .amaz-store-widget-content .ui-slider .ui-slider-range,
.woocommerce .widget_price_filter .amaz-store-widget-content .ui-slider .ui-slider-handle,.entry-content form.post-password-form input[type='submit'],#amazstore-mobile-bar,.post-slide-widget .owl-carousel .owl-nav button:hover,.woocommerce div.product form.cart .button,#search-button,#search-button:hover,.slider-content-caption a.slide-btn,.page-template-frontpage .owl-carousel button.owl-dot, .woocommerce #alm-quick-view-modal .alm-qv-image-slider .flex-control-paging li a,.button.return.wc-backward,.button.return.wc-backward:hover,#alm-quick-view-modal .alm-qv-image-slider .flex-control-paging li a.flex-active,.menu-close-btn:hover:before, .menu-close-btn:hover:after,.cart-close-btn:hover:after,.cart-close-btn:hover:before,.cart-contents .count-item,[type='submit']:hover,.comment-list .reply a,.nav-links .page-numbers.current, .nav-links .page-numbers:hover,.woocommerce .thunk-product-image-tab-section .thunk-product-hover a.add_to_cart_button:hover,.woosw-copy-btn input,.thunk-hglt-icon:before,.woocommerce .thunk-woo-product-list span.onsale,.product-type-grouped .add-to-cart .button,.cat-icon,#search-box #search-button,.woocommerce a.product_type_variable:hover,
.woocommerce #respond input#submit:hover,.woocommerce a.button:hover,.woocommerce button.button:hover,.woocommerce input.button:hover,#move-to-top,.widget.th-about-me a.read-more:hover,#page.amazstore-site .owl-nav  button.owl-prev:hover,
#page.amazstore-site .owl-nav  button.owl-next:hover,.woocommerce .thunk-product .th-add-to-cart a,.woocommerce .thunk-product .th-add-to-cart a:hover,.top-header-col3 .cart-contents{background:{$amaz_store_theme_clr}}
  .open-cart p.buttons a:hover,
  .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce #respond input#submit:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,.thunk-slide .owl-nav button.owl-prev:hover, .thunk-slide .owl-nav button.owl-next:hover, .amaz-store-slide-post .owl-nav button.owl-prev:hover, .amaz-store-slide-post .owl-nav button.owl-next:hover,.thunk-list-grid-switcher a.selected, .thunk-list-grid-switcher a:hover,.woocommerce .woocommerce-error .button:hover, .woocommerce .woocommerce-info .button:hover, .woocommerce .woocommerce-message .button:hover,#searchform [type='submit']:hover,article.thunk-post-article .thunk-readmore.button:hover,.amaz-store-load-more button:hover,.woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current,.thunk-top2-slide.owl-carousel .owl-nav button:hover,.product-slide-widget .owl-carousel .owl-nav button:hover, .thunk-slide.thunk-brand .owl-nav button:hover,.thunk-heading-wrap:before{background-color:{$amaz_store_theme_clr};} 
  .open-cart p.buttons a:hover,.thunk-slide .owl-nav button.owl-prev:hover, .thunk-slide .owl-nav button.owl-next:hover, .amaz-store-slide-post .owl-nav button.owl-prev:hover, .amaz-store-slide-post .owl-nav button.owl-next:hover,body .woocommerce-tabs .tabs li a::before,.thunk-list-grid-switcher a.selected, .thunk-list-grid-switcher a:hover,.woocommerce .woocommerce-error .button, .woocommerce .woocommerce-info .button, .woocommerce .woocommerce-message .button,#searchform [type='submit']:hover,article.thunk-post-article .thunk-readmore.button,.amaz-store-load-more button,.thunk-top2-slide.owl-carousel .owl-nav button:hover,.product-slide-widget .owl-carousel .owl-nav button:hover, .thunk-slide.thunk-brand .owl-nav button:hover,.page-contact .leadform-show-form input[type='submit'],.post-slide-widget .owl-carousel .owl-nav button:hover,#page.amazstore-site .owl-nav  button.owl-prev:hover,
#page.amazstore-site .owl-nav  button.owl-next:hover{border-color:{$amaz_store_theme_clr}} .loader {
    border-right: 4px solid {$amaz_store_theme_clr};
    border-bottom: 4px solid {$amaz_store_theme_clr};
    border-left: 4px solid {$amaz_store_theme_clr};}
    .woocommerce .thunk-product-image-cat-slide .thunk-woo-product-list:hover .thunk-product,.woocommerce .thunk-product-image-cat-slide .thunk-woo-product-list:hover .thunk-product,[type='submit']{border-color:{$amaz_store_theme_clr}} .amaz-store-off-canvas-sidebar-wrapper .menu-close-btn:hover,.main-header .cart-close-btn:hover{color:{$amaz_store_theme_clr};}";

    $amaz_store_style.=".woocommerce .thunk-product-hover .thunk-wishlist a.add_to_wishlist:hover, .thunk-wishlist .yith-wcwl-wishlistaddedbrowse, .thunk-wishlist .yith-wcwl-wishlistexistsbrowse, .thunk-compare .compare-button a.compare.button:hover,.thunk-product-hover .thunk-quickview a:hover,.thunk-cat-tab .tab-link li a.active, .thunk-cat-tab .tab-link li a:hover{color:{$amaz_store_theme_clr};} .thunk-single-product-summary-wrap a.th-product-compare-btn.th-added-compare{color:{$amaz_store_theme_clr}!important}";

   //text
   $amaz_store_text_clr = esc_html(get_theme_mod('amaz_store_text_clr'));
   $amaz_store_style.="body,.woocommerce-error, .woocommerce-info, .woocommerce-message {color: {$amaz_store_text_clr}}";
   //title
   $amaz_store_title_clr = esc_html(get_theme_mod('amaz_store_title_clr'));
   $amaz_store_style.=".site-title span a,.sprt-tel b,.widget.woocommerce .widget-title, .open-widget-content .widget-title, .widget-title,.wp-block-group h2,h2.thunk-post-title a, h1.thunk-post-title ,#reply-title,h4.author-header,.page-head h1,.woocommerce div.product .product_title, section.related.products h2, section.upsells.products h2, .woocommerce #reviews #comments h2,.woocommerce table.shop_table thead th, .cart-subtotal, .order-total,.cross-sells h2, .cart_totals h2,.woocommerce-billing-fields h3,.page-head h1 a,.widget.woocommerce .widget-title, .amaz-store-widget-content .widget-title, .widget-title, .wp-block-group h2, .amaz-store-widget-content > h2{color: {$amaz_store_title_clr}}";
   //link
   $amaz_store_link_clr = esc_html(get_theme_mod('amaz_store_link_clr'));
   $amaz_store_link_hvr_clr = esc_html(get_theme_mod('amaz_store_link_hvr_clr'));
   $amaz_store_style.="a,#open-above-menu.amaz-store-menu > li > a,.thunk-cat-tab .tab-link li a{color:{$amaz_store_link_clr}} #open-above-menu.amaz-store-menu > li > a:hover,#open-above-menu.amaz-store-menu li a:hover,.thunk-cat-tab .tab-link li a.active, .thunk-cat-tab .tab-link li a:hover{color:{$amaz_store_link_hvr_clr}}";

  // loader
   $amaz_store_loader_bg_clr = esc_html(get_theme_mod('amaz_store_loader_bg_clr','#9c9c9'));
   $amaz_store_style.=".amaz_store_overlayloader{background-color:{$amaz_store_loader_bg_clr}}";
  

//Move to top 
$amaz_store_move_to_top_bg_clr      = esc_html(get_theme_mod('amaz_store_move_to_top_bg_clr',''));
$amaz_store_move_to_top_icon_clr    = esc_html(get_theme_mod('amaz_store_move_to_top_icon_clr'));
$amaz_store_style.="#move-to-top{background:{$amaz_store_move_to_top_bg_clr};color:{$amaz_store_move_to_top_icon_clr}}";

 // ribbon
$amaz_store_style.="
.amazstore-site section.thunk-ribbon-section .content-wrap:before{
  background:{$amaz_store_theme_clr};
}";

  /**************************/
  //Above Header Color Option
  /**************************/
   $amaz_store_abv_header_background_image = esc_html( get_theme_mod('header_image'));
   $amaz_store_style.= "header.inner{background-image:url($amaz_store_abv_header_background_image);
   }";

//Product title in single line
   /*******************/
    /*text ellips css start*/
  /*******************/
$amaz_store_color_scheme = (bool)get_theme_mod('amaz_store_prdct_single',true);
if($amaz_store_color_scheme==true){
   $amaz_store_style.=".thunk-woo-product-list .woocommerce-loop-product__title {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
  line-height: 24px;
  max-height: 24px;
}";
}
  return $amaz_store_style;
}

//start logo width
function amaz_store_logo_width_responsive( $value, $dimension = 'desktop' ){
    $custom_css = '';
    switch ( $dimension ){
    case 'desktop':
      $v3 = $value;
      break;
    case 'tablet':
      $v3 = $value;
      break;
    case 'mobile':
      $v3 = $value;
      break;
  }
  $custom_css .= '.thunk-logo img,.sticky-header .logo-content img{
    max-width: ' . $v3 . 'px;
  }';
  $custom_css = amaz_store_add_media_query( $dimension, $custom_css );
  return $custom_css;
}

// top footer height
function amaz_store_top_footer_height_responsive( $value, $dimension = 'desktop' ){
    $custom_css = '';
    switch ( $dimension ){
    case 'desktop':
      $v3 = $value;
      break;
    case 'tablet':
      $v3 = $value;
      break;
    case 'mobile':
      $v3 = $value;
      break;
  }
  $custom_css .= '.top-footer .top-footer-bar{
    line-height: ' . $v3 . 'px;
  }';
  $custom_css = amaz_store_add_media_query( $dimension, $custom_css );
  return $custom_css;
}
function amaz_store_top_footer_border_responsive( $value, $dimension = 'desktop' ){
    $custom_css = '';
    switch ( $dimension ){
    case 'desktop':
      $v3 = $value;
      break;
    case 'tablet':
      $v3 = $value;
      break;
    case 'mobile':
      $v3 = $value;
      break;
  }
  $custom_css .= '.top-footer{
    border-bottom-width: ' . $v3 . 'px;
  }';
  $custom_css = amaz_store_add_media_query( $dimension, $custom_css );
  return $custom_css;
}

// below footer height
function amaz_store_below_footer_height_responsive( $value, $dimension = 'desktop' ){
    $custom_css = '';
    switch ( $dimension ){
    case 'desktop':
      $v3 = $value;
      break;
    case 'tablet':
      $v3 = $value;
      break;
    case 'mobile':
      $v3 = $value;
      break;
  }
  $custom_css .= '.below-footer .below-footer-bar{
    line-height: ' . $v3 . 'px;
  }';
  $custom_css = amaz_store_add_media_query( $dimension, $custom_css );
  return $custom_css;
}
function amaz_store_below_footer_border_responsive( $value, $dimension = 'desktop' ){
    $custom_css = '';
    switch ( $dimension ){
    case 'desktop':
      $v3 = $value;
      break;
    case 'tablet':
      $v3 = $value;
      break;
    case 'mobile':
      $v3 = $value;
      break;
  }
  $custom_css .= '.below-footer{
    border-top-width: ' . $v3 . 'px;
  }';
  $custom_css = amaz_store_add_media_query( $dimension, $custom_css );
  return $custom_css;
}

function amaz_store_front_above_header_gap_responsive( $value, $dimension = 'desktop' ){
    $custom_css = '';
    switch ( $dimension ){
    case 'desktop':
      $v3 = $value;
      break;
    case 'tablet':
      $v3 = $value;
      break;
    case 'mobile':
      $v3 = $value;
      break;
  }
  $custom_css .= 'header.front{
    margin-top: ' . $v3 . 'px;
  }';
  $custom_css = amaz_store_add_media_query( $dimension, $custom_css );
  return $custom_css;
}

function amaz_store_inner_above_header_gap_responsive( $value, $dimension = 'desktop' ){
    $custom_css = '';
    switch ( $dimension ){
    case 'desktop':
      $v3 = $value;
      break;
    case 'tablet':
      $v3 = $value;
      break;
    case 'mobile':
      $v3 = $value;
      break;
  }
  $custom_css .= 'header.inner{
    margin-top: ' . $v3 . 'px;
  }';
  $custom_css = amaz_store_add_media_query( $dimension, $custom_css );
  return $custom_css;
}

function amaz_store_ribbon_margin_responsive( $value, $dimension = 'desktop' ){
    $custom_css = '';
    switch ( $dimension ){
    case 'desktop':
      $v3 = $value;
      break;
    case 'tablet':
      $v3 = $value;
      break;
    case 'mobile':
      $v3 = $value;
      break;
  }
  $custom_css .= '.thunk-ribbon-content{
    padding-top: ' . $v3 . 'px;padding-bottom: ' . $v3 . 'px;
  }';
  $custom_css = amaz_store_add_media_query( $dimension, $custom_css );
  return $custom_css;
}