<?php
/**
 * The WooCommerce template file
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#woocommerce
 * @package  Amaz Store
 * @since 1.0.0
 */
if ( ! class_exists( 'WooCommerce' ) ){
    return;
}
if (is_shop()) {
  $shop_page_id = get_option('woocommerce_shop_page_id');
  $postid = $shop_page_id;
} else {
  $postid = $post->ID;
}

$page_woo_post_meta_set     = get_post_meta($postid, 'amaz_store_sidebar_dyn', true);
$page_content_post_meta_set = get_post_meta($postid, 'amaz_store_content_dyn', true);
$amaz_store_sidebar_layout_ = amaz_store_sidebar_layout($page_woo_post_meta_set, '');
get_header();
?>
<div id="content" class="page-content <?php echo esc_attr(amaz_store_sidebar_layout($page_woo_post_meta_set,''));?>">
        	<div class="container">
          <div class="content-wrap" >
        			<div class="main-area">

                 <?php if(amaz_store_sidebar_layout($page_woo_post_meta_set,'')!=='no-sidebar'): get_sidebar(); endif; ?><!-- end sidebar-primary  sidebar-content-area-->
                 <div id="primary" class="primary-content-area">
                  <div class="primary-content-wrap">
                          <div class="page-head">
                   <?php amaz_store_get_page_title();?>
                   <?php amaz_store_breadcrumb_trail();?>
                    </div>
                            <?php woocommerce_content();?>  
                           </div> <!-- end primary-content-wrap-->
                </div> <!-- end primary primary-content-area-->
        			</div> <!-- end main-area -->
        		</div>  <!-- end content-wrap -->
        	</div> 
        </div> <!-- end content page-content -->
<?php get_footer();?>
