<?php
/**
 * Register WooCommerce Single Product Page
 */

if ( ! class_exists( 'WooCommerce' ) ){
    return;
}
$wp_customize->add_setting( 'amaz_store_product_single_sidebar_disable', array(
                'default'               => false,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'amaz_store_product_single_sidebar_disable', array(
                'label'                 => esc_html__('Force to disable sidebar in product page.', 'amaz-store'),
                'type'                  => 'checkbox',
                'section'               => 'amaz-store-woo-single-product',
                'settings'              => 'amaz_store_product_single_sidebar_disable',
 ) ) );
/******************************/
// Up Sell Product
/******************************/
$wp_customize->add_setting('amaz_store_single_upsell_product_divide', array(
        'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control( new amaz_store_Misc_Control( $wp_customize, 'amaz_store_single_upsell_product_divide',
            array(
        'section'     => 'amaz-store-woo-single-product',
        'type'        => 'custom_message',
        'description' => __('Up Sell Product','amaz-store' ),
)));
// display upsell
$wp_customize->add_setting('amaz_store_upsell_product_display', array(
                'default'               => true,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'amaz_store_upsell_product_display', array(
                'label'         => __('Display up sell product', 'amaz-store'),
                'type'          => 'checkbox',
                'section'       => 'amaz-store-woo-single-product',
                'settings'      => 'amaz_store_upsell_product_display',
            ) ) );
// up sell product column
if ( class_exists( 'amaz_store_WP_Customizer_Range_Value_Control' ) ){
$wp_customize->add_setting(
            'amaz_store_upsale_num_col_shw', array(
                'sanitize_callback' => 'amaz_store_sanitize_range_value',
                'default' => '4',  
            )
        );
$wp_customize->add_control(
            new amaz_store_WP_Customizer_Range_Value_Control(
                $wp_customize, 'amaz_store_upsale_num_col_shw', array(
                    'label'       => __( 'Number Of Column To Show', 'amaz-store' ),
                    'section'     => 'amaz-store-woo-single-product',
                    'type'        => 'range-value',
                    'input_attr'  => array(
                        'min'  => 1,
                        'max'  => 6,
                        'step' => 1,
                    ),
                    
                )
        )
);
// no.of product to show
$wp_customize->add_setting(
            'amaz_store_upsale_num_product_shw', array(
                'sanitize_callback' => 'amaz_store_sanitize_range_value',
                'default' => '4',
                
                
            )
        );
$wp_customize->add_control(
            new amaz_store_WP_Customizer_Range_Value_Control(
                $wp_customize, 'amaz_store_upsale_num_product_shw', array(
                    'label'       => __( 'Number Of Product To Show', 'amaz-store' ),
                    'section'     => 'amaz-store-woo-single-product',
                    'type'        => 'range-value',
                    'input_attr'  => array(
                        'min'  => 1,
                        'max'  => 100,
                        'step' => 1,
                    ),
                    
                )
        )
);
}
/******************************/
// Related Product
/******************************/
$wp_customize->add_setting('amaz_store_single_related_product_divide', array(
        'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control( new amaz_store_Misc_Control( $wp_customize, 'amaz_store_single_related_product_divide',
            array(
        'section'     => 'amaz-store-woo-single-product',
        'type'        => 'custom_message',
        'description' => __('Related Product','amaz-store' ),
)));
// display upsell
$wp_customize->add_setting('amaz_store_related_product_display', array(
                'default'               => true,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'amaz_store_related_product_display', array(
                'label'         => __('Display Related product', 'amaz-store'),
                'type'          => 'checkbox',
                'section'       => 'amaz-store-woo-single-product',
                'settings'      => 'amaz_store_related_product_display',
            ) ) );
// up sell product column
if ( class_exists( 'amaz_store_WP_Customizer_Range_Value_Control' ) ){
$wp_customize->add_setting(
            'amaz_store_related_num_col_shw', array(
                'sanitize_callback' => 'amaz_store_sanitize_range_value',
                'default' => '4',
                
                
            )
        );
$wp_customize->add_control(
            new amaz_store_WP_Customizer_Range_Value_Control(
                $wp_customize, 'amaz_store_related_num_col_shw', array(
                    'label'       => __( 'Number Of Column To Show', 'amaz-store' ),
                    'section'     => 'amaz-store-woo-single-product',
                    'type'        => 'range-value',
                    'input_attr'  => array(
                        'min'  => 1,
                        'max'  => 6,
                        'step' => 1,
                    ),
                    
                )
        )
);
// no.of product to show
$wp_customize->add_setting(
            'amaz_store_related_num_product_shw', array(
                'sanitize_callback' => 'amaz_store_sanitize_range_value',
                'default' => '4',
                
                
            )
        );
$wp_customize->add_control(
            new amaz_store_WP_Customizer_Range_Value_Control(
                $wp_customize, 'amaz_store_related_num_product_shw', array(
                    'label'       => __( 'Number Of Product To Show', 'amaz-store' ),
                    'section'     => 'amaz-store-woo-single-product',
                    'type'        => 'range-value',
                    'input_attr'  => array(
                        'min'  => 1,
                        'max'  => 100,
                        'step' => 1,
                    ),
                    
                )
        )
);
}
/****************/
// doc link
/****************/
$wp_customize->add_setting('amaz_store_single_product_link_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_single_product_link_more',
            array(
        'section'     => 'amaz-store-woo-single-product',
        'type'        => 'doc-link',
        'url'         => 'https://themehunk.com/docs/amaz-store/#single-product',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));