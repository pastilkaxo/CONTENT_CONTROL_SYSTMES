<?php
/**
 * Demos
 *
 * @package Demo Content for shopstore
 * @author aThemeArt
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
$theme = wp_get_theme();

            // Demos url
$url = 'https://demo.athemeart.com/demo-import/' . $theme->template . '/';

$data['WooCommerce'] = array(
    'demo_name' => 'Fastest shop',
    'demo_url' => 'https://athemeart.dev/demo/fastest-shop/free/',
    'xml_file' => ATA_DEMO_URL.'data/temp.xml',
    'widgets_file' => 'https://demo.athemeart.com/demo-import/fastest-shop/default-widgets.wie',
    'screenshot' => 'https://demo.athemeart.com/demo-import/fastest-shop/screenshot-4.png',
    'home_title' => 'Home',
    'blog_title' => 'Blog',
    'posts_to_show' => '6',
    'elementor_width' => '1400',
    'is_shop' => true,
    'woo_image_size' => '600',
    'woo_thumb_size' => '300',
    'woo_crop_width' => '1',
    'woo_crop_height' => '1',
    'required_plugins' => array(
        'free' => array(
            array(
                'slug' => 'elementor',
                'init' => 'elementor/elementor.php',
                'name' => 'Elementor',
            ),
            array(
                'slug' => 'woocommerce',
                'init' => 'woocommerce/woocommerce.php',
                'name' => 'WooCommerce',
            ),
          
        ),
    )
);

$data['fashion'] = array(
    'demo_name' => 'Fashion shop',
    'demo_url' => 'https://athemeart.dev/demo/fastest-shop/demo-1/',
    'categories' => array('Premium'),
    'screenshot' => 'https://demo.athemeart.com/demo-import/fastest-shop/screenshot-1.png',
    'pro' => 'https://athemeart.com/downloads/fastest-elementor-woocommerce-theme/', 
);

$data['Cosmetics'] = array(
    'demo_name' => 'Cosmetics / Beauty',
    'demo_url' => 'https://athemeart.dev/demo/fastest-shop/demo-2/',
    'categories' => array('Premium'),
    'screenshot' => 'https://demo.athemeart.com/demo-import/fastest-shop/screenshot-2.png',
    'pro' => 'https://athemeart.com/downloads/fastest-elementor-woocommerce-theme/', 
);

$data['dark'] = array(
    'demo_name' => 'Wine Store',
    'demo_url' => 'https://athemeart.dev/demo/fastest-shop/demo-3/',
    'categories' => array('Premium'),
    'screenshot' => 'https://demo.athemeart.com/demo-import/fastest-shop/screenshot-3.png',
    'pro' => 'https://athemeart.com/downloads/fastest-elementor-woocommerce-theme/', 
);