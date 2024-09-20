<?php 
/**
 * Header Function for Amaz Store theme.
 * 
 * @package     Amaz Store
 * @author      Amaz Store
 * @copyright   Copyright (c) 2021, Amaz Store
 * @since       Amaz Store 1.0.0
 */
//************************************/
// top header col3 function
//************************************/
if ( ! function_exists( 'amaz_store_top_header_conetnt_col3' ) ){ 
function amaz_store_top_header_conetnt_col3($content,$mobileopen){ ?>
<?php if($content=='text'){?>
<div class='content-html'>
  <?php echo esc_html(get_theme_mod('amaz_store_col3_texthtml',  __( 'Add your content here', 'amaz-store' )));?>
</div>
<?php }elseif($content=='menu'){
  if ( has_nav_menu('amaz-store-above-menu' ) ){?>
<!-- Menu Toggle btn-->
        <nav> 
        <div class="menu-toggle">
            <button type="button" class="menu-btn" id="menu-btn-abv">
                <div class="btn">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </div>
            </button>
        </div>
        <div class="sider above amaz-store-menu-hide <?php echo esc_attr($mobileopen);?>">
        <div class="sider-inner">
        <?php amaz_store_abv_nav_menu();?>
        </div>
      </div>
    </nav>
<?php 
 }else{?>
<a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Assign Above header menu', 'amaz-store' );?></a>
 <?php }
}
elseif($content=='widget'){?>
  <div class="content-widget">
    <?php if( is_active_sidebar('top-header-widget-col2' ) ){
    dynamic_sidebar('top-header-widget-col2' );
     }else{?>
      <a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>"><?php esc_html_e( 'Add Widget', 'amaz-store' );?></a>
     <?php }?>
     </div>
<?php }elseif($content=='social'){?>
<div class="content-social">
<?php echo amaz_store_social_links();?>
</div>
<?php }
if($content =='button'){?>
          <a href="<?php echo esc_url(get_theme_mod('amaz_store_above_hdr_btn_lnk','#')); ?>" class="btn-header"><?php echo esc_html(get_theme_mod('amaz_store_above_hdr_btn_txt','Button Text')); ?></a>
          <?php }elseif($content=='none'){
return true;
}?>
<?php }
}
/**************************************/
//Main Header function
/**************************************/
if ( ! function_exists( 'amaz_store_main_header_markup' ) ){	
function amaz_store_main_header_markup(){ 
$main_header_layout = get_theme_mod('amaz_store_main_header_layout','mhdrthree');
$amaz_store_menu_alignment = get_theme_mod('amaz_store_menu_alignment','center');
$amaz_store_menu_open = get_theme_mod('amaz_store_mobile_menu_open','left');
$offcanvas = get_theme_mod('amaz_store_canvas_alignment','cnv-none');
?>
<div class="main-header <?php echo esc_attr($main_header_layout);?> <?php echo esc_attr($amaz_store_menu_alignment).'-menu';?>  <?php echo esc_attr($offcanvas);?>">
			<div class="container">
        <div class="desktop-main-header">
				<div class="main-header-bar thnk-col-3">
          <?php if ($main_header_layout == 'mhdrthree') {
            
            if ( class_exists( 'WooCommerce' ) ){ ?>
            <div class="menu-category-list toogleclose">
              <div class="toggle-cat-wrap">
                  <p class="cat-toggle" tabindex="0">
                    <span class="cat-icon"> 
                      <span class="cat-top"></span>
                       <span class="cat-mid"></span>
                       <span class="cat-bot"></span>
                     </span>
                    
                  </p>
              </div>
              <?php amaz_store_product_list_categories(); ?>
             </div><!-- menu-category-list -->    
          <div class="main-header-col1">
            <div class="header-icon-column">
              <?php amaz_store_header_icon();  ?>       
           </div>
         </div>
         <?php }?>
           <div class="main-header-col2"> 
               <nav>
        <!-- Menu Toggle btn-->
       <!-- Menu Toggle btn-->
        <div class="menu-toggle">
            <button type="button" class="menu-btn" id="menu-btn">
                <div class="btn">
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
               </div>
            </button>
        </div>
        <div class="sider-inner">
          <?php if(has_nav_menu('amaz-store-main-menu' )){ 
              if (wp_is_mobile()!== true){
                    if(has_nav_menu('amaz-store-above-menu' )){
                                amaz_store_abv_nav_menu();
                       }
                    }  
                   amaz_store_main_nav_menu();
              }else{
                 wp_page_menu(array( 
                 'items_wrap'  => '<ul class="amaz-store-menu" data-menu-style="horizontal">%3$s</ul>',
                 'link_before' => '<span>',
                 'link_after'  => '</span>'));
             }?>
        </div>
        </nav>    
      </div>

      <div class="main-header-col3">
        <?php echo amaz_store_main_header_optn(); ?>
      </div>
       <?php }
       elseif($main_header_layout == 'mhdrfour'){
        if ( class_exists( 'WooCommerce' ) ){ ?>
        <div class="menu-category-list toogleclose">
              <div class="toggle-cat-wrap">
                  <p class="cat-toggle" tabindex="0">
                    <span class="cat-icon"> 
                      <span class="cat-top"></span>
                       <span class="cat-mid"></span>
                       <span class="cat-bot"></span>
                     </span>
                    
                  </p>
              </div>
              <?php amaz_store_product_list_categories(); ?>
             </div><!-- menu-category-list --> 
           <?php  } ?>
           <div class="main-header-col1">
             <nav>
        <!-- Menu Toggle btn-->
       <!-- Menu Toggle btn-->
        <div class="menu-toggle">
            <button type="button" class="menu-btn" id="menu-btn">
                <div class="btn">
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
               </div>
            </button>
        </div>
        <div class="sider-inner">
          <?php if(has_nav_menu('amaz-store-main-menu' )){ 
              if (wp_is_mobile()!== true){
                    if(has_nav_menu('amaz-store-above-menu' )){
                                amaz_store_abv_nav_menu();
                       }
                    }  
                   amaz_store_main_nav_menu();
              }else{
                 wp_page_menu(array( 
                 'items_wrap'  => '<ul class="amaz-store-menu" data-menu-style="horizontal">%3$s</ul>',
                 'link_before' => '<span>',
                 'link_after'  => '</span>'));
             }?>
        </div>
        </nav>
         </div> 

         <div class="main-header-col2"> 
           <?php echo amaz_store_main_header_optn(); ?>        
          </div>

          <div class="main-header-col3">
            <div class="header-icon-column">
              <?php amaz_store_header_icon();

               ?>       
           </div>
        </div>

      <?php } ?>
        </div> <!-- end main-header-bar -->
      </div>
        <!-- end main-header-bar -->
       
			</div>
		</div> 
       <div class="search-wrapper">
                    <div class="container">
                      <div class="search-close"><a class="search-close-btn"></a></div>
                     <?php  if ( class_exists( 'WooCommerce' ) ){
                              amaz_store_product_search_box();
                          } ?>
                    </div>
       </div> 
<?php	}
}
add_action( 'amaz_store_main_header', 'amaz_store_main_header_markup' );
function amaz_store_main_header_optn(){
          $amaz_store_main_header_option = get_theme_mod('amaz_store_main_header_option','none');?>
          <div class="header-support-wrap">
           
         <?php if($amaz_store_main_header_option =='button'){?>

          <a href="<?php echo esc_url(get_theme_mod('amaz_store_main_hdr_btn_lnk','#')); ?>" class="btn-main-header"><?php echo esc_html(get_theme_mod('amaz_store_main_hdr_btn_txt','Button Text')); ?></a>
          <?php }
          elseif($amaz_store_main_header_option =='callto'){ ?>
              <div class="header-support-content">
                 <i class="fa fa-phone" aria-hidden="true"></i>
                <span class="sprt-tel"><b><?php echo esc_html(get_theme_mod('amaz_store_main_hdr_calto_txt','Call To')); ?></b> <a href="tel:<?php echo esc_html(get_theme_mod('amaz_store_main_hdr_calto_nub','+1800090098')); ?>"><?php echo esc_html(get_theme_mod('amaz_store_main_hdr_calto_nub','+1800090098')); ?></a></span>
                
              </div>
         
          <?php }elseif($amaz_store_main_header_option =='widget'){?>
               <div class="header-widget-wrap">
                 <?php
                  if ( is_active_sidebar('main-header-widget') ){
                       dynamic_sidebar('main-header-widget');
                   }
                  ?>
               </div>
         <?php  }
         elseif($amaz_store_main_header_option =='text'){ ?>
          <div class='content-html'>
            <?php echo esc_html(get_theme_mod('amaz_store_main_col3_texthtml',  __( 'Add your content here', 'amaz-store' ))); ?>
          </div>
        <?php }
          ?>
          </div>
<?php }

/**************************************/
//logo & site title function
/**************************************/
if ( ! function_exists( 'amaz_store_logo' ) ){
function amaz_store_logo(){
$title_disable          = get_theme_mod( 'title_disable','enable');
$tagline_disable        = get_theme_mod( 'tagline_disable','enable');
$description            = get_bloginfo( 'description', 'display' );
amaz_store_custom_logo(); 
if($title_disable!='' || $tagline_disable!=''){
if($title_disable!=''){ 
?>
<div class="site-title"><span>
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
</span>
</div>
<?php
}
if($tagline_disable!=''){
if( $description || is_customize_preview() ):?>
<div class="site-description">
   <p><?php echo esc_html($description); ?></p>
</div>
<?php endif;
      }
    } 
  }
}
/***************************/
// Product search
/***************************/
function amaz_store_product_search_box(){

    if (shortcode_exists( 'th-aps' )) {

        echo do_shortcode('[th-aps]');

     }elseif(shortcode_exists( 'tapsp' )){

      echo do_shortcode('[tapsp]');


     }         
}


function amaz_store_product_cart(){

                 if ( shortcode_exists('taiowc') ){

                  echo do_shortcode('[taiowc]');

                }elseif( shortcode_exists('taiowcp') ){

                  echo do_shortcode('[taiowcp]');

                }       
}

/**********************************/
// header icon function
/**********************************/
function amaz_store_header_icon(){
    if ( class_exists( 'WooCommerce' ) ){
?>
<div class="header-icon">
<?php  
amaz_store_account(); 
amaz_store_whishlist_url(); 
?>
</div>
<?php } }

/**************************/
//PRELOADER
/**************************/
if( ! function_exists( 'amaz_store_preloader' ) ){
 function amaz_store_preloader(){
 if (( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) ||
                isset( $_REQUEST['elementor-preview'] )){
      return;
 }else{    
    $amaz_store_preloader_enable = get_theme_mod('amaz_store_preloader_enable',false);
    $amaz_store_preloader_image_upload = get_theme_mod('amaz_store_preloader_image_upload','');
    if($amaz_store_preloader_enable==true){ ?>
    <div class="amaz_store_overlayloader">
    <div class="amaz-store-pre-loader"><img src="<?php echo esc_url($amaz_store_preloader_image_upload);?>"></div>
    </div> 
   <?php }
   }
 }

}
add_action('amaz_store_site_preloader','amaz_store_preloader');

 /**********************/
 // Sticky Header
 /**********************/
 if( ! function_exists( 'amaz_store_sticky_header_markup' )){
 function amaz_store_sticky_header_markup(){ 
 $amaz_store_menu_open = get_theme_mod('amaz_store_mobile_menu_open','left'); ?>
<div class="sticky-header">
   <div class="container">
        <div class="sticky-header-bar thnk-col-3">
           <div class="sticky-header-col1">
               <span class="logo-content">
                  <?php amaz_store_logo();?> 
              </span>
           </div>
           <div class="sticky-header-col2">
             <nav>
        <!-- Menu Toggle btn-->
        <div class="menu-toggle">
            <button type="button" class="menu-btn" id="menu-btn-stk">
                <div class="btn">
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
               </div>
            </button>
        </div>
        <div class="sider main  amaz-store-menu-hide  <?php echo esc_attr($amaz_store_menu_open); ?>">
        <div class="sider-inner">
          <?php if(has_nav_menu('amaz-store-sticky-menu' )){ 
             if (wp_is_mobile()!== false){
                    if(has_nav_menu('amaz-store-above-menu')){
                      amaz_store_abv_nav_menu();
                    }     
                  }  
                amaz_store_stick_nav_menu();
              }else{
                wp_page_menu(array( 
                 'items_wrap'  => '<ul class="amaz-store-menu" data-menu-style="horizontal">%3$s</ul>',
                 'link_before' => '<span>',
                 'link_after'  => '</span>'));
             }?>
        </div>
        </div>
        </nav>
           </div>
            <div class="sticky-header-col3">
              <div class="thunk-icon">
        
                <div class="header-icon">
                  <a class="prd-search" href="#"><span class="th-icon th-icon-vector-search"></span></a>     
                     <?php 
                     if(class_exists( 'WooCommerce' )){

                        amaz_store_whishlist_url();
                        amaz_store_account();
                      }
                       ?>
                </div>
             <?php if(class_exists( 'WooCommerce' )){ ?>
                      <div class="cart-icon" > 
                         <?php 

                         amaz_store_product_cart();

                         ?>
                       </div>
                      <?php  } ?> 
                  </div>
           </div>
        </div>

   </div>
</div>
      <div class="search-wrapper">
                     <div class="container">
                      <div class="search-close"><a class="search-close-btn"></a></div>
                     <?php  if ( class_exists( 'WooCommerce' ) ){
                              amaz_store_product_search_box();
                          } ?>
                       </div>
       </div> 
 <?php }
}
if(get_theme_mod('amaz_store_sticky_header',false)==true):
add_action('amaz_store_sticky_header','amaz_store_sticky_header_markup');
endif;

/*****************/
/*mobile nav bar*/
/*****************/

function amazstore_mobile_navbar(){?>
  <?php if(class_exists( 'WooCommerce' )){?>
<div id="amazstore-mobile-bar">
  <ul>
    
    <li><a class="gethome" href="<?php echo esc_url( get_home_url() ); ?>"><span class="th-icon th-icon-home"></span></a></li>

    <li><?php amaz_store_whishlist_url(); ?>

    </li>

    <li>
      <a href="#" class="menu-btn" id="mob-menu-btn">           
             <button type="button" class="menu-btn" id="menu-btn">
                <div class="btn">
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
               </div>
            </button>   
      </a>
    </li>

    <li><?php amaz_store_account();?></li>
    
    
  </ul>
</div>
<?php }}
add_action( 'wp_footer', 'amazstore_mobile_navbar' );

/// mobile panel
function amaz_store_cart_mobile_panel(){
$amaz_store_mobile_menu_open = get_theme_mod('amaz_store_mobile_menu_open','left');
  ?>
      <div class="mobile-nav-bar sider main  amaz-store-menu-hide <?php echo esc_attr($amaz_store_mobile_menu_open); ?>">
        <div class="sider-inner">
        
          <div class="mobile-tab-wrap">
              <?php if(class_exists( 'WooCommerce' )){?>
            <div class="mobile-nav-tabs">
                <ul>
                  <li class="primary active" data-menu="primary">
                     <a href="#mobile-nav-tab-menu"><?php _e('Menu','amaz-store');?></a>
                  </li>
                  
                  <li class="categories" data-menu="categories">
                    <a href="#mobile-nav-tab-category"><?php _e('Categories','amaz-store');?></a>
                  </li>
                
                </ul>
            </div>
            <?php }?>
            <div id="mobile-nav-tab-menu" class="mobile-nav-tab-menu panel">
          <?php if(has_nav_menu('amaz-store-main-menu' )){ 
                    if(has_nav_menu('amaz-store-above-menu' )){
                         amaz_store_abv_nav_menu();
                       }
                        amaz_store_main_nav_menu();
              }else{
                 wp_page_menu(array( 
                 'items_wrap'  => '<ul class="amaz-store-menu" data-menu-style="horizontal">%3$s</ul>',
                 'link_before' => '<span>',
                 'link_after'  => '</span>'));
             }?>
           </div>
            <?php if(class_exists( 'WooCommerce' )){?>
           <div id="mobile-nav-tab-category" class="mobile-nav-tab-category panel">
             <?php amaz_store_product_list_categories_mobile(); ?>
           </div>
           <?php }?>
          </div>
        </div>
      </div>
<?php 
}
add_action( 'amaz_store_main_header', 'amaz_store_cart_mobile_panel' );