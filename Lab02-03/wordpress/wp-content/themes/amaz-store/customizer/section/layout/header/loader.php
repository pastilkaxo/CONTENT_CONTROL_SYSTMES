<?php
//Enable Loader
$wp_customize->add_setting( 'amaz_store_preloader_enable', array(
                'default'               => false,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'amaz_store_preloader_enable', array(
                'label'                 => esc_html__('Enable Loader', 'amaz-store'),
                'type'                  => 'checkbox',
                'section'               => 'amaz-store-pre-loader',
                'settings'              => 'amaz_store_preloader_enable',
                'priority'   => 1,
            ) ) );
// BG color
 $wp_customize->add_setting('amaz_store_loader_bg_clr', array(
        'default'           => '#9c9c9c',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_color',
        'transport'         => 'postMessage',
    ));
$wp_customize->add_control( 
    new amaz_store_Customizer_Color_Control($wp_customize,'amaz_store_loader_bg_clr', array(
        'label'      => __('Background Color', 'amaz-store' ),
        'section'    => 'amaz-store-pre-loader',
        'settings'   => 'amaz_store_loader_bg_clr',
        'priority'   => 2,
    ) ) 
 ); 
/*******************/ 
// Pre loader Image
/*******************/ 
$wp_customize->add_setting('amaz_store_preloader_image_upload', array(
        'default'       => '',
        'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_upload',
    ));
$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'amaz_store_preloader_image_upload', array(
        'label'          => __('Pre Loader Image', 'amaz-store'),
        'description'    => __('(You can also use GIF image.)', 'amaz-store'),
        'section'        => 'amaz-store-pre-loader',
        'settings'       => 'amaz_store_preloader_image_upload',
 )));

/****************/
// doc link
/****************/
$wp_customize->add_setting('amaz_store_loader_link_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_loader_link_more',
            array(
        'section'     => 'amaz-store-pre-loader',
        'type'        => 'doc-link',
        'url'         => 'https://themehunk.com/docs/amaz-store/#pre-loader',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));
