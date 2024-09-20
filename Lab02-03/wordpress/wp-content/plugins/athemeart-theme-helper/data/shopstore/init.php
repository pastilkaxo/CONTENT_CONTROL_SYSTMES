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
    'demo_name' => 'Shopstore Free',
    'demo_url' => 'https://athemeart.dev/demo/shopstore/?page_id=2564',
    
    'categories' => array('WooCommerce'),
    'xml_file' => ATA_DEMO_URL.'data/temp.xml',
    'widgets_file' => $url . '/default-widgets.wie',
    'screenshot' => $url . '/screenshot.png',
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

$data['regular'] = array(
    'demo_name' => 'Regular Version',
    'demo_url' => 'https://athemeart.dev/demo/shopstore/',
    'categories' => array('Premium'),
    'screenshot' => 'https://athemeart.dev/wp/shopstore/img/shopstore-1.png',
    'pro' => 'https://athemeart.com/downloads/shopstore/', 
);

$data['center'] = array(
    'demo_name' => 'Center Version',
    'demo_url' => 'https://athemeart.dev/demo/shopstore2nd/',
    'categories' => array('Premium'),
    'screenshot' => 'https://athemeart.dev/wp/shopstore/img/shopstore-2.png',
    'pro' => 'https://athemeart.com/downloads/shopstore/', 
);

$data['dark'] = array(
    'demo_name' => 'Dark version',
    'demo_url' => 'https://athemeart.dev/wp/shopstore/shopstore/',
    'categories' => array('Premium'),
    'screenshot' => 'https://athemeart.dev/wp/shopstore/img/shopstore-3.png',
    'pro' => 'https://athemeart.com/downloads/shopstore/', 
);