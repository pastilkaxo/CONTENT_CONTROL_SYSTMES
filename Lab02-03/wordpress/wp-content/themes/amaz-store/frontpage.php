<?php 
/**
 * Template Name: Homepage Template
 * @package ThemeHunk
 * @subpackage Amaz Store
 * @since 1.0.0
 */
get_header();
$amaz_store_sidebar = get_post_meta( $post->ID, 'amaz_store_sidebar_dyn', true ); 
$page_post_meta_set         = get_post_meta( $post->ID, 'amaz_store_sidebar_dyn', true );?>
   <div id="content" class="<?php echo esc_attr(amaz_store_sidebar_layout($page_post_meta_set,''));?>">
    <div class="container">
          <div class="content-wrap">
              <div class="main-area">
                <div id="primary" class="primary-content-area">
                  <div class="primary-content-wrap">
                        <?php
                          if( shortcode_exists( 'amaz-store' ) ){
                             do_shortcode("[amaz-store section='amaz_store_show_frontpage']");
                          }
                        ?>
                  </div>  <!-- end primary-content-wrap-->
                </div>  <!-- end primary primary-content-area-->
                <?php if(amaz_store_sidebar_layout($page_post_meta_set,'')!=='no-sidebar'): get_sidebar(); endif; ?><!-- end sidebar-primary  sidebar-content-area-->
              </div> <!-- end main-area -->
          </div> <!-- end content-wrap -->
        </div> <!-- end content page-content -->
      </div>
<?php get_footer();