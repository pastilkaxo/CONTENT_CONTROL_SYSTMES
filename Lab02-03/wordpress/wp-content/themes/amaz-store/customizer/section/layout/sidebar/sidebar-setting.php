<?php
/**
 *Sidebar Options for Amaz Store Theme.
 *
 * @package     Amaz Store
 * @author      Amaz Store
 * @copyright   Copyright (c) 2021, Amaz Store
 * @since       Amaz Store 1.0.0
 */
/********************/
// default layout
/********************/
$wp_customize->add_setting('amaz_store_sidebar_default_layout', array(
        'default'        => 'no-sidebar',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'esc_attr',
    ));
    $wp_customize->add_control( 'amaz_store_sidebar_default_layout', array(
        'settings' => 'amaz_store_sidebar_default_layout',
        'label'   => __('Default Layout','amaz-store'),
        'section' => 'amaz-store-section-sidebar-group',
        'type'    => 'select',
        'choices'    => array(
        'no-sidebar'   => __('No Sidebar','amaz-store'),
        'left' => __('Left Sidebar','amaz-store'),
        'right'=> __('Right Sidebar','amaz-store'),    
        ),
        'priority'   => 1,
));
/********************/
// page layout
/********************/
$wp_customize->add_setting('amaz_store_sidebar_page_layout', array(
        'default'        => 'default',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'esc_attr',
    ));
$wp_customize->add_control( 'amaz_store_sidebar_page_layout', array(
        'settings' => 'amaz_store_sidebar_page_layout',
        'label'   => __('Page Layout','amaz-store'),
        'section' => 'amaz-store-section-sidebar-group',
        'type'    => 'select',
        'choices'    => array(
        'default'   => __('Default','amaz-store'),
        'no-sidebar'   => __('No Sidebar','amaz-store'),
        'left' => __('Left Sidebar','amaz-store'),
        'right'=> __('Right Sidebar','amaz-store'),    
        ),
        'priority'   => 3,
));
/********************/
// blog page layout
/********************/
$wp_customize->add_setting('amaz_store_sidebar_blog_layout', array(
        'default'        => 'default',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'esc_attr',
    ));
$wp_customize->add_control( 'amaz_store_sidebar_blog_layout', array(
        'settings' => 'amaz_store_sidebar_blog_layout',
        'label'   => __('Blog Layout','amaz-store'),
        'section' => 'amaz-store-section-sidebar-group',
        'type'    => 'select',
        'choices'    => array(
        'default'   => __('Default','amaz-store'),
        'no-sidebar'   => __('No Sidebar','amaz-store'),
        'left' => __('Left Sidebar','amaz-store'),
        'right'=> __('Right Sidebar','amaz-store'),    
        ),
        'priority'   => 4,
));
/********************/
// blog single page layout
/********************/
$wp_customize->add_setting('amaz_store_sidebar_archive_layout', array(
        'default'        => 'default',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'esc_attr',
    ));
$wp_customize->add_control( 'amaz_store_sidebar_archive_layout', array(
        'settings' => 'amaz_store_sidebar_archive_layout',
        'label'   => __('Blog Post Archives','amaz-store'),
        'section' => 'amaz-store-section-sidebar-group',
        'type'    => 'select',
        'choices'    => array(
        'default'   => __('Default','amaz-store'),
        'no-sidebar'   => __('No Sidebar','amaz-store'),
        'left' => __('Left Sidebar','amaz-store'),
        'right'=> __('Right Sidebar','amaz-store'),    
        ),
        'priority'   => 5,
));
/********************/
// Woo page layout
/********************/
$wp_customize->add_setting('amaz_store_sidebar_woo_layout', array(
        'default'        => 'default',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'esc_attr',
        

    ));
$wp_customize->add_control( 'amaz_store_sidebar_woo_layout', array(
        'settings' => 'amaz_store_sidebar_woo_layout',
        'label'    => __('WooCommerce','amaz-store'),
        'section'  => 'amaz-store-section-sidebar-group',
        'type'     => 'select',
        'choices'      => array(
        'default'      => __('Default','amaz-store'),
        'no-sidebar'   => __('No Sidebar','amaz-store'),
        'left'         => __('Left Sidebar','amaz-store'),
        'right'        => __('Right Sidebar','amaz-store'),    
        ),
        'priority'   => 6,
));