<?php 
/**
 * all customizer setting includeed
 *
 * @param  
 * @return mixed|string
 */
function amaz_store_customize_register( $wp_customize ){
//view pro feature
//Registered panel and section
require AMAZ_STORE_THEME_DIR . 'customizer/register-panels-and-sections.php';	
//site identity
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/header/set-identity.php';
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/header/header.php';	
//Header
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/header/above-header.php';	
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/header/main-header.php';
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/header/loader.php';
//Footer
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/footer/above-footer.php';
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/footer/widget-footer.php';
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/footer/bottom-footer.php';

//section ordering
require AMAZ_STORE_THEME_DIR . 'customizer/section-ordering.php';
//social Icon
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/social-icon/social-icon.php';
//sidebar option
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/sidebar/sidebar-setting.php';
//Blog
require AMAZ_STORE_THEME_DIR . 'customizer/section/layout/blog/blog.php';
//Color Option
require AMAZ_STORE_THEME_DIR . 'customizer/section/color/global-color.php';


//woo-product
require AMAZ_STORE_THEME_DIR . 'customizer/section/woo/product.php';
require AMAZ_STORE_THEME_DIR . 'customizer/section/woo/single-product.php';
require AMAZ_STORE_THEME_DIR . 'customizer/section/woo/cart.php';
require AMAZ_STORE_THEME_DIR . 'customizer/section/woo/shop.php';

// scroller
if ( class_exists('amaz_store_Customize_Control_Scroll')){
      $scroller = new amaz_store_Customize_Control_Scroll();
  }
}
add_action('customize_register','amaz_store_customize_register');
function amaz_store_is_json( $string ){
    return is_string( $string ) && is_array( json_decode( $string, true ) ) ? true : false;
}