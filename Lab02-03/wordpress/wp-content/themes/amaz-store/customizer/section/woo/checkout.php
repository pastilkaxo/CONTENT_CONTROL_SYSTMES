<?php 
/**
 *
 *
 * @package      Amaz Store
 * @author       Amaz Store
 * @copyright    Copyright (c) 2021,  Amaz Store
 * @since        Amaz Store 1.0.0
 */
//General Section
if ( ! class_exists( 'WooCommerce' ) ){
    return;
}
/***************/
// Checkout
/***************/
$wp_customize->add_setting('amaz_store_woo_checkout_distraction_enable', array(
                'default'               => false,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize,'amaz_store_woo_checkout_distraction_enable', array(
                'label'         => esc_html__('Enable Distraction Free Checkout.', 'amaz-store'),
                'type'          => 'checkbox',
                'section'       => 'amaz-store-woo-checkout-page',
                'settings'      => 'amaz_store_woo_checkout_distraction_enable',
            ) ) );

/****************/
// doc link
/****************/
$wp_customize->add_setting('amaz_store_checkout_link_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_checkout_link_more',
            array(
        'section'     => 'amaz-store-woo-checkout-page',
        'type'        => 'custom_message',
        'description' => sprintf( wp_kses(__( 'To know more go with this <a target="_blank" href="%s">Doc</a> !', 'amaz-store' ), array(  'a' => array( 'href' => array(),'target' => array() ) ) ), esc_url('https://themehunk.com/docs/amaz-store-theme/#checkout-page')),
        'priority'   =>30,
    )));