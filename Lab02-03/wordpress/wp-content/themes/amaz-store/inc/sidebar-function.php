<?php
/**
 *Sidebar Function for Amaz Store Theme
 */
if ( ! function_exists( 'amaz_store_sidebar_layout' ) ){
function amaz_store_sidebar_layout($page_post_meta_set='default', $default='no-sidebar'){
    $default_layout = get_theme_mod('amaz_store_sidebar_default_layout', $default );
    $page_layout = get_theme_mod('amaz_store_sidebar_page_layout','default' );
    $blog_layout = get_theme_mod('amaz_store_sidebar_blog_layout','default');
    $archive_layout = get_theme_mod('amaz_store_sidebar_archive_layout','default' );
    $woo_layout = get_theme_mod('amaz_store_sidebar_woo_layout','default' );
    $singleproduct_layout = get_theme_mod('amaz_store_single_sidebar_disable',true);
    $layout='';
if($page_post_meta_set=='default' || $page_post_meta_set==''){
    if((is_home()) && ($blog_layout!=='default')){
       $layout = $blog_layout;
    }
    elseif(is_page() && $page_layout!=='default'){
     $layout = $page_layout;
    }
    elseif((is_single() || is_archive()) && (class_exists( 'WooCommerce' ) && !is_woocommerce() && !is_product()) && $archive_layout!=='default'){
        $layout = $archive_layout;
    }
    elseif((class_exists( 'WooCommerce' )) && (is_woocommerce() || is_checkout() || is_cart() || is_account_page()) && $woo_layout!=='default'){
        $layout = $woo_layout;
    }
    elseif((class_exists( 'WooCommerce' )) && (is_product()) && ($singleproduct_layout ==true)){
    $layout = 'no-sidebar';
    }
    else{
    $layout = $default_layout;
    }   
    return apply_filters( 'amaz_store_sidebar_layout', $layout, $default ); 
  }else{
   if(is_home() && $blog_layout!=='default'){
       $layout = $blog_layout;
    }
   elseif(is_page()){
       $layout = $page_post_meta_set;
    }
   elseif((is_single() || is_archive())){
       $layout = $page_post_meta_set;
    } 
   elseif((class_exists( 'WooCommerce' )) && (is_woocommerce() || is_checkout() || is_cart() || is_account_page())){
       $layout = $page_post_meta_set;
    }
   
   else{
       $layout = $default_layout;
    }
    return apply_filters( 'amaz_store_sidebar_layout',$layout); 
   }
 }
}