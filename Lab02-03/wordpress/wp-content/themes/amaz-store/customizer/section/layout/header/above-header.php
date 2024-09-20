<?php
// col3
$wp_customize->add_setting('amaz_store_above_header_col3_set', array(
        'default'        => 'none',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_select',
    ));
$wp_customize->add_control( 'amaz_store_above_header_col3_set', array(
        'settings' => 'amaz_store_above_header_col3_set',
        'label'   => __('Area to be customize','amaz-store'),
        'section' => 'amaz-store-above-header',
        'type'    => 'select',
        'choices'    => array(
        'none'       => __('None','amaz-store'),
        'text'       => __('Text','amaz-store'),
        'menu'       => __('Menu','amaz-store'),
        'widget'     => __('Widget (Pro)','amaz-store'),
        'social'     => __('Social Media (Pro)','amaz-store'),  
        'callto'     => __('Call To (Pro)','amaz-store'),
        'button'     => __('Button (Pro)','amaz-store'),
        ),
    ));

// col3-text/html
$wp_customize->add_setting('amaz_store_col3_texthtml', array(
        'default'           => __('Add your content here','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_textarea',
        'transport'         => 'postMessage',
        
    ));
$wp_customize->add_control('amaz_store_col3_texthtml', array(
        'label'    => __('Text', 'amaz-store'),
        'section'  => 'amaz-store-above-header',
        'settings' => 'amaz_store_col3_texthtml',
         'type'    => 'textarea',
    ));
// col3 social media redirection
if (class_exists('amaz_store_Widegt_Redirect')){ 
$wp_customize->add_setting(
            'amaz_store_col3_social_media_redirect', array(
            'sanitize_callback' => 'sanitize_text_field',
     )
);
$wp_customize->add_control(
            new amaz_store_Widegt_Redirect(
                $wp_customize, 'amaz_store_col3_social_media_redirect', array(
                    'section'      => 'amaz-store-above-header',
                    'button_text'  => esc_html__( 'Go To Social Media', 'amaz-store' ),
                    'button_class' => 'focus-customizer-social_media-redirect-col3',  
                )
            )
        );
} 
// col3 menu redirection
if (class_exists('amaz_store_Widegt_Redirect')){ 
$wp_customize->add_setting(
            'amaz_store_col3_menu_redirect', array(
            'sanitize_callback' => 'sanitize_text_field',
     )
);
$wp_customize->add_control(
            new amaz_store_Widegt_Redirect(
                $wp_customize, 'amaz_store_col3_menu_redirect', array(
                    'section'      => 'amaz-store-above-header',
                    'button_text'  => esc_html__( 'Go To Menu', 'amaz-store' ),
                    'button_class' => 'focus-customizer-menu-redirect-col3',  
                )
            )
        );
}
// col3 widget redirection
if (class_exists('amaz_store_Widegt_Redirect')){ 
$wp_customize->add_setting(
            'amaz_store_col3_widget_redirect', array(
            'sanitize_callback' => 'sanitize_text_field',
     ));
$wp_customize->add_control(
            new amaz_store_Widegt_Redirect(
                $wp_customize, 'amaz_store_col3_widget_redirect', array(
                    'section'      => 'amaz-store-above-header',
                    'button_text'  => esc_html__( 'Go To Widget', 'amaz-store' ),
                    'button_class' => 'focus-customizer-widget-redirect-col3',  
                )
            )
        );
}

/*****************/
// Call-to
/*****************/
$wp_customize->add_setting('amaz_store_top_hdr_calto_txt', array(
        'default' => __('Call To','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        'transport'         => 'postMessage',
));
$wp_customize->add_control( 'amaz_store_top_hdr_calto_txt', array(
        'label'    => __('Call To Text', 'amaz-store'),
        'section'  => 'amaz-store-above-header',
         'type'    => 'text',
));

$wp_customize->add_setting('amaz_store_top_hdr_calto_nub', array(
        'default' => __('+1800090098','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        'transport'         => 'postMessage',
));
$wp_customize->add_control( 'amaz_store_top_hdr_calto_nub', array(
        'label'    => __('Call To Number', 'amaz-store'),
        'section'  => 'amaz-store-above-header',
         'type'    => 'text',
));
//**************/
// BUTTON TEXT //
//**************/
$wp_customize->add_setting('amaz_store_above_hdr_btn_txt', array(
        'default' => __('Button Text','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        'transport'         => 'postMessage',
));
$wp_customize->add_control( 'amaz_store_above_hdr_btn_txt', array(
        'label'    => __('Button Text', 'amaz-store'),
        'section'  => 'amaz-store-above-header',
         'type'    => 'text',
         'priority'   => 4,
));

$wp_customize->add_setting('amaz_store_above_hdr_btn_lnk', array(
        'default' => __('#','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        
));
$wp_customize->add_control( 'amaz_store_above_hdr_btn_lnk', array(
        'label'    => __('Button Link', 'amaz-store'),
        'section'  => 'amaz-store-above-header',
         'type'    => 'text',
         'priority'   => 5,
));
//*********************************************
// Front Page Header Slider
//*********************************************
//Slider Content Via Repeater
      if ( class_exists( 'amaz_store_Repeater' ) ){
            $wp_customize->add_setting(
             'amaz_store_front_above_header_slider', array(
             'sanitize_callback' => 'amaz_store_repeater_sanitize',  
             'default'           => amaz_store_Defaults_Models::instance()->get_frontpage_slider_default(),
                )
            );
            $wp_customize->add_control(
                new amaz_store_Repeater(
                    $wp_customize, 'amaz_store_front_above_header_slider', array(
                        'label'                                => esc_html__( 'Slide Content', 'amaz-store' ),
                        'section'                              => 'amaz_store_frontpage_hdrslide',
                        'add_field_label'                      => esc_html__( 'Add new Slide', 'amaz-store' ),
                        'item_name'                            => esc_html__( 'Slide', 'amaz-store' ),
                        
                        'customizer_repeater_title_control'    => false,   
                        'customizer_repeater_subtitle_control'    => false, 
                        'customizer_repeater_text_control'    => false,  
                        'customizer_repeater_image_control'    => true, 
                        'customizer_repeater_logo_image_control'    => false,  
                        'customizer_repeater_link_control'     => true,
                        'customizer_repeater_repeater_control' => false,  
                                         
                        
                    ),'amaz_store_front_above_header_slider'
                )
            );
        }

    if (class_exists('amaz_store_Toggle_Control')) {
    $wp_customize->add_setting( 'amaz_store_above_header_autoplay', array(
    'default'           => false,
    'sanitize_callback' => 'amaz_store_sanitize_checkbox',
  ) );
  $wp_customize->add_control( new amaz_store_Toggle_Control( $wp_customize, 'amaz_store_above_header_autoplay', array(
    'label'       => esc_html__( 'Slide Auto Play', 'amaz-store' ),
    'section'     => 'amaz_store_frontpage_hdrslide',
    'type'        => 'toggle',
    'settings'    => 'amaz_store_above_header_autoplay',
  ) ) );
}

if ( class_exists( 'amaz_store_WP_Customizer_Range_Value_Control' ) ){
$wp_customize->add_setting(
            'amaz_store_front_above_header_gap', array(
                'sanitize_callback' => 'amaz_store_sanitize_range_value',
                'default'           => '178',
                 'transport'         => 'postMessage',
            )
        );
$wp_customize->add_control(
            new amaz_store_WP_Customizer_Range_Value_Control(
                $wp_customize, 'amaz_store_front_above_header_gap', array(
                    'label'       => esc_html__( 'Visible Area', 'amaz-store' ),
                    'section'     => 'amaz_store_frontpage_hdrslide',
                    'type'        => 'range-value',
                    'input_attr'  => array(
                        'min'  => 1,
                        'max'  => 1000,
                        'step' => 1,
                    ),
                      'media_query' => true,
                    'sum_type'    => true,
                )
           )
    );
}
//*********************************************
// Inner Page Header Slider
//*********************************************
$wp_customize->add_setting('amaz_store_inner_above_header_select', array(
        'default'        => 'homepageslider',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_select',
    ));
$wp_customize->add_control( 'amaz_store_inner_above_header_select', array(
        'settings' => 'amaz_store_inner_above_header_select',
        'label'   => __('Choose Option','amaz-store'),
        'section' => 'amaz_store_innerpage_hdrslide',
        'type'    => 'select',
        'choices'    => array(
        'none'     => __('None','amaz-store'),
        'homepageslider'     =>__('Home Page slider','amaz-store'),
        'innerpageslider'     =>__('Inner Page slider (Pro)','amaz-store'),
            
        ),
    ));
//Slider Content Via Repeater
      if ( class_exists( 'amaz_store_Repeater' ) ){
            $wp_customize->add_setting(
             'amaz_store_inner_above_header_slider', array(
             'sanitize_callback' => 'amaz_store_repeater_sanitize',  
             'default'           => '',
                )
            );
            $wp_customize->add_control(
                new amaz_store_Repeater(
                    $wp_customize, 'amaz_store_inner_above_header_slider', array(
                        'label'                                => esc_html__( 'Slide Content', 'amaz-store' ),
                        'section'                              => 'amaz_store_innerpage_hdrslide',
                        'add_field_label'                      => esc_html__( 'Add new Slide', 'amaz-store' ),
                        'item_name'                            => esc_html__( 'Slide', 'amaz-store' ),
                        
                        'customizer_repeater_title_control'    => true,   
                        'customizer_repeater_subtitle_control'    => true, 
                        'customizer_repeater_text_control'    => true,  
                        'customizer_repeater_image_control'    => true, 
                        'customizer_repeater_logo_image_control'    => false,  
                        'customizer_repeater_link_control'     => true,
                        'customizer_repeater_repeater_control' => false,  
                                         
                        
                    ),'amaz_store_inner_above_header_slider'
                )
            );
        }

    if (class_exists('amaz_store_Toggle_Control')) {
    $wp_customize->add_setting( 'amaz_store_inner_above_header_autoplay', array(
    'default'           => false,
    'sanitize_callback' => 'amaz_store_sanitize_checkbox',
  ) );
  $wp_customize->add_control( new amaz_store_Toggle_Control( $wp_customize, 'amaz_store_inner_above_header_autoplay', array(
    'label'       => esc_html__( 'Slide Auto Play', 'amaz-store' ),
    'section'     => 'amaz_store_innerpage_hdrslide',
    'type'        => 'toggle',
    'settings'    => 'amaz_store_inner_above_header_autoplay',
  ) ) );
}

if ( class_exists( 'amaz_store_WP_Customizer_Range_Value_Control' ) ){
$wp_customize->add_setting(
            'amaz_store_inner_above_header_gap', array(
                'sanitize_callback' => 'amaz_store_sanitize_range_value',
                'default'           => '178',
                 'transport'         => 'postMessage',
            )
        );
$wp_customize->add_control(
            new amaz_store_WP_Customizer_Range_Value_Control(
                $wp_customize, 'amaz_store_inner_above_header_gap', array(
                    'label'       => esc_html__( 'Visible Area', 'amaz-store' ),
                    'section'     => 'amaz_store_innerpage_hdrslide',
                    'type'        => 'range-value',
                    'input_attr'  => array(
                        'min'  => 1,
                        'max'  => 1000,
                        'step' => 1,
                    ),
                      'media_query' => true,
                    'sum_type'    => true,
                )
           )
    );
}