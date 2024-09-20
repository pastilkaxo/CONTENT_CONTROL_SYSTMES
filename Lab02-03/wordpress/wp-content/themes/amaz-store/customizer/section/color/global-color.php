<?php
/******************/
//Global Option
/******************/

// theme color
 $wp_customize->add_setting('amaz_store_theme_clr', array(
        'default'        => '#ff3377',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_color',
        'transport'         => 'postMessage',
    ));
$wp_customize->add_control( 
    new WP_Customize_Color_Control($wp_customize,'amaz_store_theme_clr', array(
        'label'      => __('Theme Color', 'amaz-store' ),
        'section'    => 'amaz-store-gloabal-color',
        'settings'   => 'amaz_store_theme_clr',
        'priority' => 1,
    ) ) 
 );  
// link color
 $wp_customize->add_setting('amaz_store_link_clr', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_color',
        'transport'         => 'postMessage',
    ));
$wp_customize->add_control( 
    new WP_Customize_Color_Control($wp_customize,'amaz_store_link_clr', array(
        'label'      => __('Link Color', 'amaz-store' ),
        'section'    => 'amaz-store-gloabal-color',
        'settings'   => 'amaz_store_link_clr',
        'priority' => 2,
    ) ) 
 );  
// link hvr color
 $wp_customize->add_setting('amaz_store_link_hvr_clr', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_color',
        'transport'         => 'postMessage',
    ));
$wp_customize->add_control( 
    new WP_Customize_Color_Control($wp_customize,'amaz_store_link_hvr_clr', array(
        'label'      => __('Link Hover Color', 'amaz-store' ),
        'section'    => 'amaz-store-gloabal-color',
        'settings'   => 'amaz_store_link_hvr_clr',
        'priority' => 3,
    ) ) 
 );

// text color
 $wp_customize->add_setting('amaz_store_text_clr', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_color',
        'transport'         => 'postMessage',
    ));
$wp_customize->add_control( 
    new WP_Customize_Color_Control($wp_customize,'amaz_store_text_clr', array(
        'label'      => __('Text Color', 'amaz-store' ),
        'section'    => 'amaz-store-gloabal-color',
        'settings'   => 'amaz_store_text_clr',
        'priority' => 4,
    ) ) 
 );
 // title color
 $wp_customize->add_setting('amaz_store_title_clr', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_color',
        'transport'         => 'postMessage',
    ));
$wp_customize->add_control( 
    new WP_Customize_Color_Control($wp_customize,'amaz_store_title_clr', array(
        'label'      => __('Sidebar Title Color', 'amaz-store' ),
        'section'    => 'amaz-store-gloabal-color',
        'settings'   => 'amaz_store_title_clr',
        'priority' => 6,
    ) ) 
 );  
// gloabal background option
$wp_customize->get_control( 'background_color' )->section = 'amaz-store-gloabal-color';
$wp_customize->get_control( 'background_color' )->priority = 9;
$wp_customize->get_control( 'background_image' )->section = 'amaz-store-gloabal-color';
$wp_customize->get_control( 'background_image' )->priority = 10;
$wp_customize->get_control( 'background_preset' )->section = 'amaz-store-gloabal-color';
$wp_customize->get_control( 'background_preset' )->priority = 11;
$wp_customize->get_control( 'background_position' )->section = 'amaz-store-gloabal-color';
$wp_customize->get_control( 'background_position' )->priority = 11;
$wp_customize->get_control( 'background_repeat' )->section = 'amaz-store-gloabal-color';
$wp_customize->get_control( 'background_repeat' )->priority = 12;
$wp_customize->get_control( 'background_attachment' )->section = 'amaz-store-gloabal-color';
$wp_customize->get_control( 'background_attachment' )->priority = 13;
$wp_customize->get_control( 'background_size' )->section = 'amaz-store-gloabal-color';
$wp_customize->get_control( 'background_size' )->priority = 14;
/****************/
//doc link
/****************/
$wp_customize->add_setting('amaz_store_global_clr_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_global_clr_more',
            array(
        'section'     => 'amaz-store-gloabal-color',
        'type'        => 'doc-link',
        'url'         => 'https://themehunk.com/docs/amaz-store/#color-background',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));