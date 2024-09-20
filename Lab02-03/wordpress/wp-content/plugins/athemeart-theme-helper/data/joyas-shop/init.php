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

$data['free'] = array(
    'demo_name' => 'Joyas Shop Free',
    'categories' => array('free'),
    'demo_url' => 'https://demo.athemeart.com/joyas/',
    'xml_file' => ATA_DEMO_URL.'data/temp.xml',
    
    'widgets_file' => 'https://demo.athemeart.com/demo-import/startup-shop/default-widgets.wie',
    'screenshot' => 'https://i0.wp.com/themes.svn.wordpress.org/joyas-shop/1.0.1/screenshot.png',
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
    'demo_name' => 'Jewelry Store',
    'demo_url' => 'https://demo.athemeart.com/joyas/demo-1/',
    'categories' => array('Premium'),
    'screenshot' => 'https://i0.wp.com/demo.athemeart.com/joyas/img/demo-1.png',
    'pro' => 'https://athemeart.com/downloads/joyas/', 
);

$data['Cosmetics'] = array(
    'demo_name' => 'Multipurpose( With Reveltion slider ) ',
    'demo_url' => 'https://demo.athemeart.com/joyas/demo-2/',
    'categories' => array('Premium'),
    'screenshot' => 'https://i0.wp.com/demo.athemeart.com/joyas/img/demo-2.png',
    'pro' => 'https://athemeart.com/downloads/joyas/', 
);
