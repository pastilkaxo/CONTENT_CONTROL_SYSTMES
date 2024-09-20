<?php
//General Section
if ( ! class_exists( 'WooCommerce' ) ){
    return;
}
// product animation
$wp_customize->add_setting('amaz_store_woo_product_animation', array(
        'default'        => 'none',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_select',
    ));
$wp_customize->add_control( 'amaz_store_woo_product_animation', array(
        'settings'=> 'amaz_store_woo_product_animation',
        'label'   => __('Product Image Hover Style','amaz-store'),
        'section' => 'amaz-store-woo-shop',
        'type'    => 'select',
        'choices'    => array(
        'none'            => __('None','amaz-store'),
        'zoom'            => __('Zoom','amaz-store'),
        'swap'            => __('Fade Swap (Pro)','amaz-store'), 
        'slide'           => __('Slide Swap (Pro)','amaz-store'),            
        ),
    ));
/*******************/
//Product Title 
/*******************/
$wp_customize->add_setting('amaz_store_prdct_single', array(
                'default'               => true,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'amaz_store_prdct_single', array(
                'label'         => esc_html__('Enable Product Title in Single line', 'amaz-store'),
                'type'          => 'checkbox',
                'section'       => 'amaz-store-woo-shop',
                'settings'      => 'amaz_store_prdct_single',
            ) ) );
/*******************/
//Quick view
/*******************/
$wp_customize->add_setting('amaz_store_woo_quickview_enable', array(
                'default'               => true,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'amaz_store_woo_quickview_enable', array(
                'label'         => esc_html__('Enable Quick View.', 'amaz-store'),
                'type'          => 'checkbox',
                'section'       => 'amaz-store-woo-shop',
                'settings'      => 'amaz_store_woo_quickview_enable',
            ) ) );
/****************/
// doc link
/****************/
$wp_customize->add_setting('amaz_store_product_style_link_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_product_style_link_more',
            array(
        'section'     => 'amaz-store-woo-shop',
        'type'        => 'doc-link',
        'url'         => 'https://themehunk.com/docs/amaz-store/#style-product',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));