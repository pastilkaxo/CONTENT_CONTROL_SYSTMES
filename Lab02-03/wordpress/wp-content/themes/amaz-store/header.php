<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Amaz Store
 * @since 1.0.0
 * 
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="<?php echo esc_attr(get_theme_mod('amaz_store_mobile_header_clr','#fff')); ?>" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php echo esc_url(get_bloginfo('pingback_url')); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class();?>>

	<?php wp_body_open();?>


	<?php 
$page_post_meta_sidebar = '';
if(!is_404() && !is_search() && is_page()){ 
	$page_post_meta_sidebar = get_post_meta(get_the_ID(), 'amaz_store_sidebar_dyn', true );
		
}elseif(is_single() && (class_exists( 'WooCommerce' ) && !is_product())){
	    $page_post_meta_sidebar = get_post_meta(get_the_ID(), 'amaz_store_sidebar_dyn', true );
	    if(get_theme_mod('amaz_store_blog_single_sidebar_disable')==true){
	     	$page_post_meta_sidebar = 'no-sidebar';
	  }	     
}elseif(amaz_store_is_blog()){
	    $blog_page_id = get_option( 'page_for_posts' );
        $page_post_meta_sidebar = get_post_meta( $blog_page_id, 'amaz_store_sidebar_dyn', true );
}elseif(class_exists( 'WooCommerce' ) && is_shop()){
	    $shop_page_id = get_option( 'woocommerce_shop_page_id' );
        $page_post_meta_sidebar = get_post_meta( $shop_page_id, 'amaz_store_sidebar_dyn', true );
}elseif(class_exists( 'WooCommerce' ) && is_product()){
	    $page_post_meta_sidebar = get_post_meta(get_the_ID(), 'amaz_store_sidebar_dyn', true );
	    if(get_theme_mod('amaz_store_product_single_sidebar_disable',false)==true){
	     	$page_post_meta_sidebar = 'no-sidebar';
	      }
}
?>
<?php do_action('amaz_store_site_preloader'); 
$amaz_store_above_header_col3_set   = get_theme_mod( 'amaz_store_above_header_col3_set','text');
$amaz_store_menu_open = get_theme_mod('amaz_store_mobile_menu_open','left');
$offcanvas = get_theme_mod('amaz_store_canvas_alignment','cnv-none');
$amaz_store_inner_above_header_select = get_theme_mod('amaz_store_inner_above_header_select','homepageslider');
if (is_front_page()) {
	$above_header_page = 'front';
}
else{
	$above_header_page = 'inner';
}
?>
<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'amaz-store' ); ?></a>
<div id="page" class="amazstore-site <?php echo esc_attr($page_post_meta_sidebar);?>">
	<div class="above-bg <?php echo esc_attr($above_header_page); ?>">
		<div class="above-header-content <?php echo esc_attr($offcanvas); ?>">
      		<div class="container">
		<div class="top-header-bar thnk-col-3">
          <div class="top-header-col1"> 
          	<span class="logo-content">
            <?php amaz_store_logo();?> 
            </span>
           <?php if(function_exists('amaz_store_show_off_canvas_sidebar_icon')){amaz_store_show_off_canvas_sidebar_icon();} ?>
          </div>
          <div class="top-header-col2">
          	<?php amaz_store_product_search_box(); ?>
          </div>
          <div class="top-header-col3">
          	<?php amaz_store_top_header_conetnt_col3($amaz_store_above_header_col3_set,$amaz_store_menu_open);
          	if (class_exists('WooCommerce')) {

                            amaz_store_product_cart();
                    }
             ?>
          </div>
        </div>
         <!-- responsive mobile main header-->
        <div class="responsive-main-header">
          <div class="main-header-bar thnk-col-3">
            <div class="main-header-col1">
            <span class="logo-content">
            <?php amaz_store_logo();?> 
          </span>
          
          </div>

           <div class="main-header-col2">
            <?php if ( class_exists( 'WooCommerce' ) ){
              amaz_store_product_search_box();
             } ?>
           </div>

           <div class="main-header-col3">
            <div class="thunk-icon-market">
        <div class="menu-toggle">
            <button type="button" class="menu-btn" id="menu-btn">
                <div class="btn">
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
               </div>
            </button>
        </div>
           <div class="header-support-wrap">
              <div class="header-support-icon">

                 <?php if( class_exists( 'WooCommerce' ) &&get_theme_mod('amaz_store_whislist_mobile_disable',false) != true){
                  amaz_store_whishlist_url(); ?>       
      <?php } ?>
        
        <?php if(class_exists( 'WooCommerce' ) && get_theme_mod('amaz_store_account_mobile_disable',false) != true){ amaz_store_account(); } ?>
               
              </div>
              
             
                <?php if(class_exists( 'WooCommerce' )){ 

                  if(get_theme_mod('amaz_store_cart_mobile_disable',false)==false){

                    
                          
                      ?>
                      <div class="thunk-icon">
                      <div class="cart-icon" > 

                        <?php 

                         amaz_store_product_cart();

                        ?>

                       </div>
                       </div>   

                      <?php  }

                     } ?>  
                     
                  
             
          </div>
          </div>
        </div>
            </div>
          </div> <!-- responsive-main-header END --> 
    	</div>
    </div>
    <?php
    if (is_front_page()) {  ?>
    <div class="above-header-slides front owl-carousel">
		<?php amaz_store_above_header_slider('amaz_store_front_above_header_slider', ''); ?>
	</div>
	<?php }
	elseif($amaz_store_inner_above_header_select  == 'innerpageslider'){ ?>
	<div class="above-header-slides inner owl-carousel">
		<?php  $default_frontpage= amaz_store_Defaults_Models::instance()->get_frontpage_slider_default();
    amaz_store_above_header_slider('amaz_store_inner_above_header_slider', $default); ?>
	</div>
	<?php }
	elseif($amaz_store_inner_above_header_select  == 'homepageslider'){ ?>
	<div class="above-header-slides inner owl-carousel">
		<?php $default_innerpage= amaz_store_Defaults_Models::instance()->get_innerpage_slider_default();
    amaz_store_above_header_slider('amaz_store_front_above_header_slider', $default_innerpage); ?>
	</div>
	<?php }  ?>
	</div> 
	<header class="<?php echo esc_attr($above_header_page); ?>">
		<?php do_action( 'amaz_store_sticky_header' ); ?> 
        <!-- sticky header -->
        <?php do_action( 'amaz_store_main_header' ); ?> 
		<!-- end main-header -->
		
		<!-- end below-header -->
	</header> <!-- end header -->
		