<?php
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package R Shop
 * @since 1.0.0
 */
get_header();
?>
<div id="content" class="page-content archive  <?php echo esc_attr(amaz_store_sidebar_layout());?>">
            <div class="content-wrap" >
                <div class="container">
                    <div class="main-area">
                        <div id="primary" class="primary-content-area">
                            <div class="primary-content-wrap">
                            <div class="page-head">
                   <?php amaz_store_get_page_title();?>
                   <?php amaz_store_breadcrumb_trail();?>
                    </div>
                            <div class="primary-content-wrap">
                                 <?php
            if( have_posts()):
                /* Start the Loop */
                while ( have_posts() ) : the_post();
                    /*
                     * Include the Post-Format-specific template for the content.
                     * If you want to override this in a child theme, then include a file
                     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                     */
                get_template_part( 'template-parts/content', get_post_format() );
                endwhile;
                
            else :
                get_template_part( 'template-parts/content', 'none' );
            endif;

           amaz_store_post_loader();
            ?>
                           </div> <!-- end primary-content-wrap-->
                       </div>
                        </div> <!-- end primary primary-content-area-->
                        <?php if(amaz_store_sidebar_layout()!=='no-sidebar'): get_sidebar(); endif; ?><!-- end sidebar-primary  sidebar-content-area-->
                    </div> <!-- end main-area -->
                </div>
            </div> <!-- end content-wrap -->
        </div> <!-- end content page-content -->
<?php get_footer();?>