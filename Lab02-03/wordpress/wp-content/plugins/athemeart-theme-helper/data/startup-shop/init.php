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
    'demo_name' => 'Startup shop Free',
    'demo_url' => 'https://athemeart.dev/wp/startup/demo-1/?post_type=product',
    'categories' => array('WooCommerce'),
    'xml_file' => ATA_DEMO_URL.'data/temp.xml',
    'widgets_file' => $url . '/default-widgets.wie',
    'screenshot' => 'https://i0.wp.com/themes.svn.wordpress.org/startup-shop/1.0.8/screenshot.png',
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

$data['Fashion'] = array(
    'demo_name' => 'Fashion Store',
    'demo_url' => 'https://athemeart.dev/wp/startup/demo-1/',
    'categories' => array('Premium'),
    'screenshot' => 'https://demo.athemeart.com/demo-import/startup-shop/screenshot.png',
    'pro' => 'https://athemeart.com/downloads/startup-shop/', 
);

$data['Cosmetics'] = array(
    'demo_name' => 'Cosmetics Store',
    'demo_url' => 'https://athemeart.dev/wp/startup/demo-2/',
    'categories' => array('Premium'),
    'screenshot' => 'https://demo.athemeart.com/demo-import/startup-shop/screenshot-2.png',
    'pro' => 'https://athemeart.com/downloads/startup-shop/', 
);

