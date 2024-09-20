<?php

/******************/
//Widegt footer
/******************/
if(class_exists('amaz_store_WP_Customize_Control_Radio_Image')){
               $wp_customize->add_setting(
               'amaz_store_bottom_footer_widget_layout', array(
               'default'           => 'ft-wgt-none',
               'sanitize_callback' => 'sanitize_text_field',
            )
        );
$wp_customize->add_control(
            new amaz_store_WP_Customize_Control_Radio_Image(
                $wp_customize, 'amaz_store_bottom_footer_widget_layout', array(
                    'label'    => esc_html__( 'Layout','amaz-store'),
                    'section'  => 'amaz-store-widget-footer',
                    'choices'  => array(
                       'ft-wgt-none'   => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_NONE,
                        ),
                        'ft-wgt-one'   => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_1,
                        ),
                        'ft-wgt-two' => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_2,
                        ),
                        'ft-wgt-three' => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_3,
                        ),
                        'ft-wgt-four' => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_4,
                        ),
                        'ft-wgt-five' => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_5,
                        ),
                        'ft-wgt-six' => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_6,
                        ),
                        'ft-wgt-seven' => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_7,
                        ),
                        'ft-wgt-eight' => array(
                            'url' => amaz_store_FOOTER_WIDGET_LAYOUT_8,
                        ),
                    ),
                )
            )
        );
    } 
/******************************/
/* Widget Redirect
/****************************/
if (class_exists('amaz_store_Widegt_Redirect')){ 
$wp_customize->add_setting(
            'amaz_store_bottom_footer_widget_redirect', array(
            'sanitize_callback' => 'sanitize_text_field',
     )
);
$wp_customize->add_control(
            new amaz_store_Widegt_Redirect(
                $wp_customize, 'amaz_store_bottom_footer_widget_redirect', array(
                    'section'      => 'amaz-store-widget-footer',
                    'button_text'  => esc_html__( 'Go To Widget', 'amaz-store' ),
                    'button_class' => 'focus-customizer-widget-redirect',  
                )
            )
        );
} 
/****************/
//doc link
/****************/
$wp_customize->add_setting('amaz_store_ftr_wdgt_learn_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_ftr_wdgt_learn_more',
            array(
        'section'    => 'amaz-store-widget-footer',
        'type'      => 'doc-link',
        'url'       => 'https://themehunk.com/docs/amaz-store/#widget-footer',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));