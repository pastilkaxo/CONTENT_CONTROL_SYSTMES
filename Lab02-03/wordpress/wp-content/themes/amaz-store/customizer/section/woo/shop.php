<?php 
$wp_customize->add_setting('amaz_store_prd_view', array(
        'default'        => 'grid-view',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_select',
    ));
    $wp_customize->add_control('amaz_store_prd_view', array(
        'settings' => 'amaz_store_prd_view',
        'label'   => __('Display Product View','amaz-store'),
        'description' => __('(Select layout to display products at shop page.)','amaz-store'),
        'section' => 'amaz-store-woo-shop-page',
        'type'    => 'select',
        'choices' => array(
        'grid-view'   => __('Grid','amaz-store'), 
        'list-view'     => __('List','amaz-store'),
        
        )
    ));
/************************/
//Shop product pagination
/************************/
   $wp_customize->add_setting('amaz_store_pagination', array(
        'default'        => 'num',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_select',
    ));
    $wp_customize->add_control('amaz_store_pagination', array(
        'settings' => 'amaz_store_pagination',
        'label'   => __('Shop Page Pagination','amaz-store'),
        'section' => 'amaz-store-woo-shop-page',
        'type'    => 'select',
        'choices' => array(
        'num'     => __('Numbered','amaz-store'),
        'click'   => __('Load More (Pro)','amaz-store'), 
        'scroll'  => __('Infinite Scroll (Pro)','amaz-store'), 
        )
    ));

  
$wp_customize->add_setting('amaz_store_pagination_loadmore_btn_text', array(
        'default'           => 'Load More',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        'transport'         => 'postMessage',
        
    ));
$wp_customize->add_control('amaz_store_pagination_loadmore_btn_text', array(
        'label'    => __('Load More Text', 'amaz-store'),
        'section'  => 'amaz-store-woo-shop-page',
        'settings' => 'amaz_store_pagination_loadmore_btn_text',
         'type'    => 'text',
    ));
/****************/
// doc link
/****************/
$wp_customize->add_setting('amaz_store_shop_page_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_shop_page_more',
            array(
        'section'     => 'amaz-store-woo-shop-page',
        'type'        => 'doc-link',
        'url'         => 'https://themehunk.com/docs/amaz-store/#shop-page',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>  100,
    )));