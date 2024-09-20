<?php 
/**
 * all file includeed
 *
 * @param  
 * @return mixed|string
 */
get_template_part( 'inc/admin-function');
get_template_part( 'inc/header-function');
get_template_part( 'inc/footer-function');
get_template_part( 'inc/blog-function');
// theme-option
include_once(ABSPATH.'wp-admin/includes/plugin.php');
if ( !is_plugin_active('amaz-store-pro/amaz-store-pro.php') ) {
get_template_part( 'lib/th-option/th-option');
//CHILD THEME 
// get_template_part( 'lib/th-option/child-notify');
get_template_part( 'lib/welcome-bar/welcome-bar');
}
//breadcrumbs
get_template_part( 'lib/breadcrumbs/breadcrumbs');
//page-post-meta
get_template_part( 'lib/page-meta-box/amaz-store-page-meta-box');
get_template_part('inc/sidebar-function');
//custom-style
get_template_part( 'inc/amaz-store-custom-style');
// customizer
get_template_part('customizer/models/class-amaz-store-singleton');
get_template_part('customizer/models/class-amaz-store-defaults-models');
get_template_part('customizer/repeater/class-amaz-store-repeater');
get_template_part('customizer/extend-customizer/class-amaz-store-wp-customize-panel');
get_template_part('customizer/extend-customizer/class-amaz-store-wp-customize-section');
get_template_part('customizer/customizer-radio-image/class/class-amaz-store-customize-control-radio-image');
get_template_part('customizer/customizer-range-value/class/class-amaz-store-customizer-range-value-control');
get_template_part('customizer/customizer-scroll/class/class-amaz-store-customize-control-scroll');
get_template_part('customizer/customize-focus-section/amaz-store-focus-section');
get_template_part('customizer/color/class-control-color');
get_template_part('customizer/customize-buttonset/class-control-buttonset');
get_template_part('customizer/sortable/class-open-control-sortable');
get_template_part('customizer/background/class-amaz-store-background-image-control');
get_template_part('customizer/customizer-tabs/class-amaz-store-customize-control-tabs');
get_template_part('customizer/customizer-toggle/class-amaz-store-toggle-control');
get_template_part('customizer/pro-button/class-customize');

get_template_part('customizer/custom-customizer');
get_template_part('customizer/customizer-constant');
get_template_part('customizer/customizer');
/******************************/
// woocommerce
/******************************/
get_template_part( 'inc/woocommerce/woo-core');
get_template_part( 'inc/woocommerce/woo-function');
get_template_part('inc/woocommerce/woocommerce-ajax');

