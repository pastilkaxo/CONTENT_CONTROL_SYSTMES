<?php
/**
 * Register customizer panels & sections.
 */
/*************************/
/*WordPress Default Panel*/
/*************************/
/**
 * Category Section Customizer Settings
 */
if(!function_exists('amaz_store_get_category_list')){
function amaz_store_get_category_list($arr='',$all=true){
    $cats = array();
    foreach ( get_categories($arr) as $categories => $category ){
       
        $cats[$category->slug] = $category->name;
     }
     return $cats;
  }
}

$amaz_store_shop_panel_default = new amaz_store_WP_Customize_Panel( $wp_customize,'amaz-store-panel-default', array(
    'priority' => 1,
    'title'    => __( 'WordPress Default', 'amaz-store' ),
  ));
$wp_customize->add_panel($amaz_store_shop_panel_default);
$wp_customize->get_section( 'title_tagline' )->panel = 'amaz-store-panel-default';
$wp_customize->get_section( 'static_front_page' )->panel = 'amaz-store-panel-default';
$wp_customize->get_section( 'custom_css' )->panel = 'amaz-store-panel-default';

$wp_customize->add_setting('amaz_store_home_page_setup', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_home_page_setup',
            array(
        'section'    => 'static_front_page',
        'type'      => 'doc-link',
        'url'       => 'https://themehunk.com/docs/amaz-store/#homepage-setting',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));
/************************/
// Background option
/************************/
$amaz_store_shop_bg_option = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-bg-option', array(
    'title' =>  __( 'Background', 'amaz-store' ),
    'panel' => 'amaz-store-panel-default',
    'priority' => 10,
  ));
$wp_customize->add_section($amaz_store_shop_bg_option);

/*************************/
/*Layout Panel*/
/*************************/
$wp_customize->add_panel( 'amaz-store-panel-layout', array(
				'priority' => 3,
				'title'    => __( 'Layout', 'amaz-store' ),
) );

// Header
$amaz_store_section_header_group = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-section-header-group', array(
    'title' =>  __( 'Header', 'amaz-store' ),
    'panel' => 'amaz-store-panel-layout',
    'priority' => 2,
  ));
$wp_customize->add_section( $amaz_store_section_header_group );

// above-header
$amaz_store_above_header = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-above-header', array(
    'title'    => __( 'Above Header', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-layout',
        'section'  => 'amaz-store-section-header-group',
        'priority' => 3,
  ));
$wp_customize->add_section( $amaz_store_above_header );
// main-header
$amaz_store_shop_main_header = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-main-header', array(
    'title'    => __( 'Main Header', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-layout',
    'section'  => 'amaz-store-section-header-group',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_shop_main_header );

// exclude category
$amaz_store_exclde_cat_header = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_exclde_cat_header', array(
    'title'    => __( 'Exclude Category', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-layout',
    'section'  => 'amaz-store-section-header-group',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_exclde_cat_header );

// above-header-frontpage slider
$amaz_store_frontpage_hdrslide = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_frontpage_hdrslide', array(
    'title'    => __( 'Frontpage Header Slider', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-layout',
        'section'  => 'amaz-store-section-header-group',
        'priority' => 3,
  ));
$wp_customize->add_section( $amaz_store_frontpage_hdrslide );
// above-header-innerpage slider
$amaz_store_innerpage_hdrslide = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_innerpage_hdrslide', array(
    'title'    => __( 'Innerpage Header Slider', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-layout',
        'section'  => 'amaz-store-section-header-group',
        'priority' => 3,
  ));
$wp_customize->add_section( $amaz_store_innerpage_hdrslide );

//blog
$amaz_store_section_blog_group = new  amaz_store_WP_Customize_Section( $wp_customize,'amaz-store-section-blog-group', array(
    'title' =>  __( 'Blog', 'amaz-store' ),
    'panel' => 'amaz-store-panel-layout',
    'priority' => 2,
  ));
$wp_customize->add_section($amaz_store_section_blog_group);

$amaz_store_section_footer_group = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-section-footer-group', array(
    'title' =>  __( 'Footer', 'amaz-store' ),
    'panel' => 'amaz-store-panel-layout',
    'priority' => 3,
  ));
$wp_customize->add_section( $amaz_store_section_footer_group);
// sidebar

$amaz_store_section_sidebar_group = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-section-sidebar-group', array(
    'title' =>  __( 'Sidebar', 'amaz-store' ),
    'panel' => 'amaz-store-panel-layout',
    'priority' => 3,
  ));
$wp_customize->add_section($amaz_store_section_sidebar_group);
// Scroll to top
$amaz_store_move_to_top = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-move-to-top', array(
    'title' =>  __( 'Move To Top', 'amaz-store' ),
    'panel' => 'amaz-store-panel-layout',
    'priority' => 3,
  ));
$wp_customize->add_section($amaz_store_move_to_top);
//above-footer
$amaz_store_above_footer = new  amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-above-footer',array(
    'title'    => __( 'Above Footer','amaz-store' ),
    'panel'    => 'amaz-store-panel-layout',
        'section'  => 'amaz-store-section-footer-group',
        'priority' => 1,
));
$wp_customize->add_section( $amaz_store_above_footer);
//widget footer
$amaz_store_shop_widget_footer = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-widget-footer', array(
    'title'    => __('Widget Footer','amaz-store'),
    'panel'    => 'amaz-store-panel-layout',
    'section'  => 'amaz-store-section-footer-group',
    'priority' => 5,
));
$wp_customize->add_section( $amaz_store_shop_widget_footer);
//Bottom footer
$amaz_store_shop_bottom_footer = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-bottom-footer', array(
    'title'    => __('Below Footer (Pro)','amaz-store'),
    'panel'    => 'amaz-store-panel-layout',
    'section'  => 'amaz-store-section-footer-group',
    'priority' => 5,
));
$wp_customize->add_section( $amaz_store_shop_bottom_footer);
/*************************/
/* Preloader */
/*************************/
$wp_customize->add_section( 'amaz-store-pre-loader' , array(
    'title'      => __('Preloader','amaz-store'),
    'priority'   => 30,
) );
/*************************/
/* Social   Icon*/
/*************************/
$amaz_store_social_header = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-social-icon', array(
    'title'    => __( 'Social Icon', 'amaz-store' ),
    'priority' => 30,
  ));
$wp_customize->add_section( $amaz_store_social_header );
/*************************/
/* Frontpage Panel */
/*************************/
$wp_customize->add_panel( 'amaz-store-panel-frontpage', array(
                'priority' => 5,
                'title'    => __( 'Frontpage Sections', 'amaz-store' ),
) );

$amaz_store_top_slider_section = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_top_slider_section', array(
    'title'    => __( 'Top Slider', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_top_slider_section );

$amaz_store_category_tab_section = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_category_tab_section', array(
    'title'    => __( 'Tabbed Product Carousel', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_category_tab_section );

// ribbon
$amaz_store_ribbon = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_ribbon', array(
    'title'    => __( 'Ribbon', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_ribbon );

$amaz_store_cat_slide_section = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_cat_slide_section', array(
    'title'    => __( 'Woo Category', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_cat_slide_section );


$amaz_store_product_slide_section = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_product_slide_section', array(
    'title'    => __( 'Product Carousel', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_product_slide_section );

$amaz_store_banner = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_banner', array(
    'title'    => __( 'Banner', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_banner );

$amaz_store_product_slide_list = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_product_slide_list', array(
    'title'    => __( 'Product List Carousel', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_product_slide_list );

$amaz_store_highlight = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_highlight', array(
    'title'    => __( 'Highlight', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_highlight );


$amaz_store_brand = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_brand', array(
    'title'    => __( 'Brand', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_brand );

$amaz_store_product_tab_image = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_product_tab_image', array(
    'title'    => __( 'Tabbed Image Product Carousel', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_product_tab_image );

$amaz_store_product_big_feature = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_product_big_feature', array(
    'title'    => __( 'Big Featured Product', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_product_big_feature );
$amaz_store_product_cat_list = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_product_cat_list', array(
    'title'    => __( 'Tabbed Product List Carousel', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_product_cat_list );
$amaz_store_1_custom_sec = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_1_custom_sec', array(
    'title'    => __( 'First Custom Section', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_1_custom_sec );

$amaz_store_2_custom_sec = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_2_custom_sec', array(
    'title'    => __( 'Second Custom Section', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_2_custom_sec );

$amaz_store_3_custom_sec = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_3_custom_sec', array(
    'title'    => __( 'Third Custom Section', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_3_custom_sec );

$amaz_store_4_custom_sec = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz_store_4_custom_sec', array(
    'title'    => __( 'Fourth Custom Section', 'amaz-store' ),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 4,
  ));
$wp_customize->add_section( $amaz_store_4_custom_sec );
/******************/
// Color Option
/******************/
$wp_customize->add_panel( 'amaz-store-panel-color-background', array(
        'priority' => 22,
        'title'    => __( 'Total Color & BG Options', 'amaz-store' ),
    ) );
// Section gloab color and background
$wp_customize->add_section('amaz-store-gloabal-color', array(
    'title'    => __('Global Colors', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 1,
));
//header
$amaz_store_header_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-header-color', array(
    'title'    => __('Header', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 1,
));
$wp_customize->add_section( $amaz_store_header_color );
$amaz_store_abv_header_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-abv-header-clr', array(
    'title'    => __('Above Header','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-header-color',
    'priority' => 1,
));
$wp_customize->add_section( $amaz_store_abv_header_clr);

$amaz_store_main_header_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-main-header-clr', array(
    'title'    => __('Main Header','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-header-color',
    'priority' => 2,
));
$wp_customize->add_section( $amaz_store_main_header_clr);

$amaz_store_below_header_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-below-header-clr', array(
    'title'    => __('Below Header','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-header-color',
    'priority' => 3,
));
$wp_customize->add_section( $amaz_store_below_header_clr);

$amaz_store_icon_header_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-icon-header-clr', array(
    'title'    => __('Shop Icons','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-header-color',
    'priority' => 4,
));
$wp_customize->add_section( $amaz_store_icon_header_clr);
$amaz_store_menu_header_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-menu-header-clr', array(
    'title'    => __('Main Menu','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-header-color',
    'priority' => 4,
));
$wp_customize->add_section( $amaz_store_menu_header_clr);

$amaz_store_sticky_header_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-sticky-header-clr', array(
    'title'    => __('Sticky Header','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-header-color',
    'priority' => 2,
));
$wp_customize->add_section($amaz_store_sticky_header_clr);


$amaz_store_mobile_pan_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-mobile-pan-clr', array(
    'title'    => __('Mobile','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-header-color',
    'priority' => 2,
));
$wp_customize->add_section($amaz_store_mobile_pan_clr);

$amaz_store_canvas_pan_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-canvas-pan-clr', array(
    'title'    => __('Off Canvas Sidebar','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-header-color',
    'priority' => 2,
));
$wp_customize->add_section($amaz_store_canvas_pan_clr);

$amaz_store_main_header_header_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-main-header-header-clr', array(
    'title'    => __('Header','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-main-header-clr',
    'priority' => 2,
));
$wp_customize->add_section($amaz_store_main_header_header_clr);

// main-menu
$amaz_store_main_header_menu_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-main-header-menu-clr', array(
    'title'    => __('Main Menu','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-main-header-clr',
    'priority' => 2,
));
$wp_customize->add_section($amaz_store_main_header_menu_clr);

// header category
$amaz_store_main_header_cat_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-main-header-cat-clr', array(
    'title'    => __('Category','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-main-header-clr',
    'priority' => 3,
));
$wp_customize->add_section($amaz_store_main_header_cat_clr);

//Shop Icon
$amaz_store_main_header_shp_icon = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-main-header-shp-icon', array(
    'title'    => __('Shop Icon','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-main-header-clr',
    'priority' => 5,
));
$wp_customize->add_section($amaz_store_main_header_shp_icon);
/****************/
//Sidebar
/****************/
$amaz_store_sidebar_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-sidebar-color', array(
    'title'    => __('Sidebar', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 1,
));
$wp_customize->add_section( $amaz_store_sidebar_color );
/****************/
//footer
/****************/
$amaz_store_footer_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-footer-color', array(
    'title'    => __('Footer', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 1,
));
$wp_customize->add_section( $amaz_store_footer_color );

$amaz_store_abv_footer_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-abv-footer-clr', array(
    'title'    => __('Above Footer','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-footer-color',
    'priority' => 1,
));
$wp_customize->add_section( $amaz_store_abv_footer_clr);

$amaz_store_footer_widget_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-footer-widget-clr', array(
    'title'    => __('Footer Widget','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-footer-color',
    'priority' => 2,
));
$wp_customize->add_section($amaz_store_footer_widget_clr);

$amaz_store_btm_footer_clr = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-btm-footer-clr', array(
    'title'    => __('Bottom Footer','amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-footer-color',
    'priority' => 3,
));
$wp_customize->add_section( $amaz_store_btm_footer_clr);

/****************/
//Woocommerce color
/****************/
$amaz_store_woo_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-woo-color', array(
    'title'    => __('Woocommerce', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 6,
));
$wp_customize->add_section( $amaz_store_woo_color );
// Box Color
$amaz_store_box_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz_store_box_color', array(
    'title'    => __('Box Color', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-woo-color',
    'priority' => 1,
));
$wp_customize->add_section( $amaz_store_box_color );
/****************/
//Mobile Nav Bar
/****************/
$amaz_store_mobile_nav_bar = new  amaz_store_WP_Customize_Section($wp_customize,'amaz_store_mobile_nav_bar', array(
    'title'    => __('Mobile Nav Bar', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 21,
));
$wp_customize->add_section( $amaz_store_mobile_nav_bar );
// product
$amaz_store_woo_prdct_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-woo-prdct-color', array(
    'title'    => __('Product', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-woo-color',
    'priority' => 1,
));
$wp_customize->add_section( $amaz_store_woo_prdct_color );
// shopping cart
$amaz_store_woo_cart_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-woo-cart-color', array(
    'title'    => __('Shopping Cart', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-woo-color',
    'priority' => 1,
));
$wp_customize->add_section( $amaz_store_woo_cart_color );

// sale
$amaz_store_woo_prdct_sale_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-woo-prdct-sale-color', array(
    'title'    => __('Sale Badge', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-woo-color',
    'priority' => 2,
));
$wp_customize->add_section( $amaz_store_woo_prdct_sale_color );
// single product
$amaz_store_woo_prdct_single_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-woo-prdct-single-color', array(
    'title'    => __('Single Product', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-woo-color',
    'priority' => 3,
));
$wp_customize->add_section( $amaz_store_woo_prdct_single_color );

/*************************/
// Frontpage
/*************************/
$amaz_store_front_page_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-front-page-color', array(
    'title'    => __('Frontpage', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 4,
));
$wp_customize->add_section($amaz_store_front_page_color);

$amaz_store_top_slider_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-top-slider-color', array(
    'title'    => __('Top Slider', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 1,
));
$wp_customize->add_section($amaz_store_top_slider_color);

$amaz_store_cat_slider_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-cat-slider-color', array(
    'title'    => __('Woo Category', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 2,
));
$wp_customize->add_section($amaz_store_cat_slider_color);

$amaz_store_ribbon_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-ribbon-color', array(
    'title'    => __('Ribbon', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_ribbon_color);

$amaz_store_product_slider_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-product-slider-color', array(
    'title'    => __('Product Carousel', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 3,
));
$wp_customize->add_section($amaz_store_product_slider_color);

$amaz_store_product_cat_slide_tab_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-product-cat-slide-tab-color', array(
    'title'    => __('Tabbed Product Carousel', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 3,
));
$wp_customize->add_section($amaz_store_product_cat_slide_tab_color);

$amaz_store_product_list_slide_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-product-list-slide-color', array(
    'title'    => __('Product List Carousel', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 4,
));
$wp_customize->add_section($amaz_store_product_list_slide_color);

$amaz_store_product_list_tab_slide_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-product-list-tab-slide-color', array(
    'title'    => __('Tabbed Product List Carousel', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 5,
));
$wp_customize->add_section($amaz_store_product_list_tab_slide_color);

$amaz_store_banner_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-banner-color', array(
    'title'    => __('Banner', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_banner_color);

$amaz_store_ribbon_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-ribbon-color', array(
    'title'    => __('Ribbon', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_ribbon_color);

$amaz_store_brand_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-brand-color', array(
    'title'    => __('Brand', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_brand_color);

$amaz_store_highlight_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-highlight-color', array(
    'title'    => __('Highlight', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_highlight_color);
$amaz_store_tabimgprd_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-tabimgprd-color', array(
    'title'    => __('Product Tab Image', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_tabimgprd_color);
$amaz_store_big_featured_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-big-featured-color', array(
    'title'    => __('Big Featured Product', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_big_featured_color);
/****************/
//custom section
/****************/
$amaz_store_custom_one_color = new amaz_store_WP_Customize_Section($wp_customize,'amaz-store-custom-one-color', array(
    'title'    => __('Custom Section', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_custom_one_color);

$amaz_store_custom_two_color = new amaz_store_WP_Customize_Section($wp_customize,'amaz-store-custom-two-color', array(
    'title'    => __('Custom Section Two', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_custom_two_color);

$amaz_store_custom_three_color = new amaz_store_WP_Customize_Section($wp_customize,'amaz-store-custom-three-color', array(
    'title'    => __('Custom Section Three', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_custom_three_color);


$amaz_store_custom_four_color = new amaz_store_WP_Customize_Section($wp_customize,'amaz-store-custom-four-color', array(
    'title'    => __('Custom Section Four', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'section'  => 'amaz-store-front-page-color',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_custom_four_color);

//section ordering
$amaz_store_section_order = new amaz_store_WP_Customize_Section($wp_customize,'amaz_store_section_order', array(
    'title'    => __('Section Ordering', 'amaz-store'),
    'panel'    => 'amaz-store-panel-frontpage',
    'priority' => 6,
));
$wp_customize->add_section($amaz_store_section_order);

// pan color
$amaz_store_pan_color = new  amaz_store_WP_Customize_Section($wp_customize,'amaz-store-pan-color', array(
    'title'    => __('Pan / Mobile Menu Color', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 5,
));
$wp_customize->add_section( $amaz_store_pan_color);
/*********************/
// Page Content Color
/*********************/
$amaz_store_custom_page_content_color = new amaz_store_WP_Customize_Section($wp_customize,'amaz-store-page-content-color', array(
    'title'    => __('Content Color', 'amaz-store'),
    'panel'    => 'amaz-store-panel-color-background',
    'priority' => 2,
));
$wp_customize->add_section($amaz_store_custom_page_content_color);
/******************/
// Shop Page
/******************/
$amaz_store_woo_shop = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-woo-shop', array(
    'title'    => __( 'Product Style', 'amaz-store' ),
     'panel'    => 'woocommerce',
     'priority' => 2,
));
$wp_customize->add_section( $amaz_store_woo_shop );

$amaz_store_woo_single_product = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-woo-single-product', array(
    'title'    => __( 'Single Product', 'amaz-store' ),
     'panel'    => 'woocommerce',
     'priority' => 3,
));
$wp_customize->add_section($amaz_store_woo_single_product );

$amaz_store_woo_cart_page = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-woo-cart-page', array(
    'title'    => __( 'Cart Page', 'amaz-store' ),
     'panel'    => 'woocommerce',
     'priority' => 4,
));
$wp_customize->add_section($amaz_store_woo_cart_page);

$amaz_store_woo_shop_page = new amaz_store_WP_Customize_Section( $wp_customize, 'amaz-store-woo-shop-page', array(
    'title'    => __( 'Shop Page', 'amaz-store' ),
     'panel'    => 'woocommerce',
     'priority' => 4,
));
$wp_customize->add_section($amaz_store_woo_shop_page);