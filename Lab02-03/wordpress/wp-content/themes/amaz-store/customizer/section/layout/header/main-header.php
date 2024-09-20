<?php
// main header
// choose col layout
if(class_exists('amaz_store_WP_Customize_Control_Radio_Image')){
        $wp_customize->add_setting(
            'amaz_store_main_header_layout', array(
                'default'           => 'mhdrthree',
                'sanitize_callback' => 'amaz_store_sanitize_radio',
            )
        );
$wp_customize->add_control(
            new amaz_store_WP_Customize_Control_Radio_Image(
                $wp_customize, 'amaz_store_main_header_layout', array(
                    'label'    => esc_html__( 'Header Layout', 'amaz-store' ),
                    'section'  => 'amaz-store-main-header',
                    'choices'  => array(
                        'mhdrthree' => array(
                            'url' => amaz_store_MAIN_HEADER_LAYOUT_ONE,
                        ),                      
                                     
                    ),
                    'priority'   => 1,
                )
            )
        );
} 
//Main menu option
$wp_customize->add_setting('amaz_store_main_header_option', array(
        'default'        => 'none',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_select',
    ));
$wp_customize->add_control( 'amaz_store_main_header_option', array(
        'settings' => 'amaz_store_main_header_option',
        'label'    => __('Right Column','amaz-store'),
        'section'  => 'amaz-store-main-header',
        'type'     => 'select',
        'choices'    => array(
        'none'       => __('None','amaz-store'),
        'callto'     => __('Call-To','amaz-store'),
        'button'     => __('Button (Pro)','amaz-store'),
        
        'widget'     => __('Widget (Pro)','amaz-store'),  
        'text'     => __('Text (Pro)','amaz-store'),     
        ),
        'priority'   => 3,
    ));
//**************/
// BUTTON TEXT //
//**************/
$wp_customize->add_setting('amaz_store_main_hdr_btn_txt', array(
        'default' => __('Button Text','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        'transport'         => 'postMessage',
));
$wp_customize->add_control( 'amaz_store_main_hdr_btn_txt', array(
        'label'    => __('Button Text', 'amaz-store'),
        'section'  => 'amaz-store-main-header',
         'type'    => 'text',
         'priority'   => 4,
));

$wp_customize->add_setting('amaz_store_main_hdr_btn_lnk', array(
        'default' => __('#','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        
));
$wp_customize->add_control( 'amaz_store_main_hdr_btn_lnk', array(
        'label'    => __('Button Link', 'amaz-store'),
        'section'  => 'amaz-store-main-header',
         'type'    => 'text',
         'priority'   => 5,
));
/*****************/
// Call-to
/*****************/
$wp_customize->add_setting('amaz_store_main_hdr_calto_txt', array(
        'default' => __('Call To','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        'transport'         => 'postMessage',
));
$wp_customize->add_control( 'amaz_store_main_hdr_calto_txt', array(
        'label'    => __('Call To Text', 'amaz-store'),
        'section'  => 'amaz-store-main-header',
         'type'    => 'text',
         'priority'   => 6,
));

$wp_customize->add_setting('amaz_store_main_hdr_calto_nub', array(
        'default' => __('+1800090098','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_text',
        'transport'         => 'postMessage',
));
$wp_customize->add_control( 'amaz_store_main_hdr_calto_nub', array(
        'label'    => __('Call To Number', 'amaz-store'),
        'section'  => 'amaz-store-main-header',
         'type'    => 'text',
         'priority'   => 7,
));

// col1 widget redirection
if (class_exists('amaz_store_Widegt_Redirect')){ 
$wp_customize->add_setting(
            'amaz_store_main_header_widget_redirect', array(
            'sanitize_callback' => 'sanitize_text_field',
     )
);
$wp_customize->add_control(
            new amaz_store_Widegt_Redirect(
                $wp_customize, 'amaz_store_main_header_widget_redirect', array(
                    'section'      => 'amaz-store-main-header',
                    'button_text'  => esc_html__( 'Go To Widget', 'amaz-store' ),
                    'button_class' => 'focus-customizer-widget-redirect',  
                    'priority'   => 8,
                )
            )
        );
} 
// col3-text/html
$wp_customize->add_setting('amaz_store_main_col3_texthtml', array(
        'default'           => __('Add your content here','amaz-store'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_textarea',
        'transport'         => 'postMessage',
        
    ));
$wp_customize->add_control('amaz_store_main_col3_texthtml', array(
        'label'    => __('Text', 'amaz-store'),
        'section'  => 'amaz-store-main-header',
        'settings' => 'amaz_store_main_col3_texthtml',
         'type'    => 'textarea',
    ));
/***********************************/  
// menu alignment
/***********************************/ 
$wp_customize->add_setting('amaz_store_menu_alignment', array(
                'default'               => 'center',
                'sanitize_callback'     => 'amaz_store_sanitize_select',
            ) );
$wp_customize->add_control( new amaz_store_Customizer_Buttonset_Control( $wp_customize, 'amaz_store_menu_alignment', array(
                'label'                 => esc_html__( 'Menu Alignment', 'amaz-store' ),
                'section'               => 'amaz-store-main-header',
                'settings'              => 'amaz_store_menu_alignment',
                'choices'               => array(
                    'left'              => esc_html__( 'Left', 'amaz-store' ),
                    'center'            => esc_html__( 'center', 'amaz-store' ),
                    'right'             => esc_html__( 'Right', 'amaz-store' ),
                ),
                'priority'   => 2,
        ) ) );
/***********************************/  
// menu alignment
/***********************************/ 
$wp_customize->add_setting('amaz_store_mobile_menu_open', array(
                'default'               => 'left',
                'sanitize_callback'     => 'amaz_store_sanitize_select',
            ) );
$wp_customize->add_control( new amaz_store_Customizer_Buttonset_Control( $wp_customize, 'amaz_store_mobile_menu_open', array(
                'label'                 => esc_html__( 'Mobile Menu', 'amaz-store' ),
                'section'               => 'amaz-store-main-header',
                'settings'              => 'amaz_store_mobile_menu_open',
                'choices'               => array(
                    'left'              => esc_html__( 'Left', 'amaz-store' ),
                    // 'overcenter'        => esc_html__( 'center', 'amaz-store' ),
                    'right'             => esc_html__( 'Right', 'amaz-store' ),
                ),
                'priority'   => 9,
        ) ) );

/***********************************/  
// Sticky Header
/***********************************/ 
  $wp_customize->add_setting( 'amaz_store_sticky_header', array(
    'default'           => false,
    'sanitize_callback' => 'amaz_store_sanitize_checkbox',
  ) );
  $wp_customize->add_control( new amaz_store_Toggle_Control( $wp_customize, 'amaz_store_sticky_header', array(
    'label'       => esc_html__( 'Sticky Header Pro', 'amaz-store' ),
    'section'     => 'amaz-store-main-header',
    'type'        => 'toggle',
    'settings'    => 'amaz_store_sticky_header',
    'priority'   => 10,
  ) ) );

  $wp_customize->add_setting('amaz_store_sticky_header_effect', array(
        'default'        => 'scrldwmn',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'amaz_store_sanitize_select',
    ));
$wp_customize->add_control( 'amaz_store_sticky_header_effect', array(
        'settings' => 'amaz_store_sticky_header_effect',
        'label'    => __('Sticky Header Effect','amaz-store'),
        'section'  => 'amaz-store-main-header',
        'type'     => 'select',
        'choices'    => array(
        'scrltop'     => __('Effect One','amaz-store'),
        'scrldwmn'    => __('Effect Two','amaz-store'),
        
        ),
        'priority'   => 11,
    ));
/******************/
// Disable in Mobile
/******************/
$wp_customize->add_setting( 'amaz_store_whislist_mobile_disable', array(
                'default'               => false,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'amaz_store_whislist_mobile_disable', array(
                'label'                 => esc_html__('Check to disable whislist icon in mobile device', 'amaz-store'),
                'type'                  => 'checkbox',
                'section'               => 'amaz-store-main-header',
                'settings'              => 'amaz_store_whislist_mobile_disable',
                'priority'   => 12,
            ) ) );

$wp_customize->add_setting( 'amaz_store_account_mobile_disable', array(
                'default'               => '',
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'amaz_store_account_mobile_disable', array(
                'label'                 => esc_html__('Check to disable account icon in mobile device', 'amaz-store'),
                'type'                  => 'checkbox',
                'section'               => 'amaz-store-main-header',
                'settings'              => 'amaz_store_account_mobile_disable',
                'priority'   => 13,
            ) ) );

$wp_customize->add_setting( 'amaz_store_cart_mobile_disable', array(
                'default'               => false,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'amaz_store_cart_mobile_disable', array(
                'label'                 => esc_html__('Check to disable cart icon in mobile device', 'amaz-store'),
                'type'                  => 'checkbox',
                'section'               => 'amaz-store-main-header',
                'settings'              => 'amaz_store_cart_mobile_disable',
                'priority'   => 14,
            ) ) );

/****************/
//doc link
/****************/
$wp_customize->add_setting('amaz_store_main_header_doc_learn_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_main_header_doc_learn_more',
            array(
        'section'    => 'amaz-store-main-header',
        'type'      => 'doc-link',
        'url'       => 'https://themehunk.com/docs/amaz-store/#main-header',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));

// exclude header category
function amaz_store_get_category_id($arr='',$all=true){
    $cats = array();
    foreach ( get_categories($arr) as $categories => $category ){
       
        $cats[$category->term_id] = $category->name;
     }
     return $cats;
  }
// $wp_customize->add_setting('amaz_store_main_hdr_cat_txt', array(
//         'default' => __('All Departments','amaz-store'),
//         'capability'        => 'edit_theme_options',
//         'sanitize_callback' => 'amaz_store_sanitize_text',
//         'transport'         => 'postMessage',
// ));
// $wp_customize->add_control( 'amaz_store_main_hdr_cat_txt', array(
//         'label'    => __('Category Text', 'amaz-store'),
//         'section'  => 'amaz_store_exclde_cat_header',
//          'type'    => 'text',
// ));
 if (class_exists( 'amaz_store_Customize_Control_Checkbox_Multiple')) {
   $wp_customize->add_setting('amaz_store_exclde_category', array(
        'default'           => '',
        'sanitize_callback' => 'amaz_store_checkbox_explode'
    ));
    $wp_customize->add_control(new amaz_store_Customize_Control_Checkbox_Multiple(
            $wp_customize,'amaz_store_exclde_category', array(
        'settings'=> 'amaz_store_exclde_category',
        'label'   => __( 'Choose Categories To Exclude', 'amaz-store' ),
        'section' => 'amaz_store_exclde_cat_header',
        'choices' => amaz_store_get_category_id(array('taxonomy' =>'product_cat'),true),
        ) 
    ));

}  
$wp_customize->add_setting('amaz_store_exclde_doc', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_exclde_doc',
            array(
        'section'     => 'amaz_store_exclde_cat_header',
        'type'        => 'doc-link',
        'url'         => 'https://themehunk.com/docs/amaz-store/#exclude-category',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));