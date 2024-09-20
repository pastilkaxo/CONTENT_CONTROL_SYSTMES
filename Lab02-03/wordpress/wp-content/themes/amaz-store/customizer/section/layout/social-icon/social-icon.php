<?php 
//Enable Loader
$wp_customize->add_setting( 'amaz_store_social_original_color', array(
                'default'               => false,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ));
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'amaz_store_social_original_color', array(
                'label'       => esc_html__('Show Original Color', 'amaz-store'),
                'type'        => 'checkbox',
                'section'     => 'amaz-store-social-icon',
                'settings'    => 'amaz_store_social_original_color',
)));
$wp_customize->add_setting('social_shop_link_facebook', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control('social_shop_link_facebook', array(
        'label'    => __('Facebook URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_facebook',
         'type'       => 'text',
        
        ));

$wp_customize->add_setting('social_shop_link_linkedin', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control('social_shop_link_linkedin', array(
        'label'    => __('LinkedIn URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_linkedin',
         'type'       => 'text',
        
        ));
$wp_customize->add_setting('social_shop_link_pintrest', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control('social_shop_link_pintrest', array(
        'label'    => __('Pinterest URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_pintrest',
         'type'       => 'text',
        
        ));
$wp_customize->add_setting('social_shop_link_twitter', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        
        ));
        $wp_customize->add_control('social_shop_link_twitter', array(
        'label'    => __('Twitter URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_twitter',
         'type'       => 'text',
        ));
$wp_customize->add_setting('social_shop_link_insta', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        
        ));
        $wp_customize->add_control('social_shop_link_insta', array(
        'label'    => __('Instagram URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_insta',
         'type'       => 'text',
        ));
$wp_customize->add_setting('social_shop_link_tumblr', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        
        ));
        $wp_customize->add_control('social_shop_link_tumblr', array(
        'label'    => __('Tumblr URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_tumblr',
         'type'       => 'text',
        ));
$wp_customize->add_setting('social_shop_link_youtube', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        
        ));
        $wp_customize->add_control('social_shop_link_youtube', array(
        'label'    => __('Youtube URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_youtube',
         'type'       => 'text',
        ));
$wp_customize->add_setting('social_shop_link_stumbleupon', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        
        ));
        $wp_customize->add_control('social_shop_link_stumbleupon', array(
        'label'    => __('Stumbleupon URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_stumbleupon',
        'type'     => 'text',
        ));
        $wp_customize->add_setting('social_shop_link_dribble', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        
        ));
        $wp_customize->add_control('social_shop_link_dribble', array(
        'label'    => __('Dribble URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_dribble',
        'type'     => 'text',
        ));

         $wp_customize->add_setting('social_shop_link_skype', array(
        'default'           => '#',
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
        
        ));
        $wp_customize->add_control('social_shop_link_skype', array(
        'label'    => __('Skype URL', 'amaz-store'),
        'section'  => 'amaz-store-social-icon',
        'settings' => 'social_shop_link_skype',
        'type'     => 'text',
        ));

/****************/
//body doc link
/****************/
$wp_customize->add_setting('amaz_store_social_link_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_social_link_more',
            array(
        'section'     => 'amaz-store-social-icon',
        'type'        => 'doc-link',
        'url'         => 'https://themehunk.com/docs/amaz-store/#social-icon',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));