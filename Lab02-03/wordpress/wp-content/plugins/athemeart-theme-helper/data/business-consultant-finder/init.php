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
    'demo_name' => 'BCF Free',
    'demo_url' => 'https://athemeart.dev/demo/bcf/bcf-business/?page_id=703',
    'xml_file' => $url . '/one-page-demo.xml',
    'widgets_file' => $url . '/one-page-demo-widgets.wie',
    'screenshot' => $url . '/screenshot.png',
    'home_title' => 'Home',
    'blog_title' => 'Blog',
    'posts_to_show' => '6',
    'is_shop' => false,
    'required_plugins' => array(
        'free' => array(
            array(
                'slug' => 'elementor',
                'init' => 'elementor/elementor.php',
                'name' => 'Elementor',
            ),
            
        ),
    )
);

$data['Creative'] = array(
    'demo_name' => 'Creative Business',
    'demo_url' => 'https://athemeart.dev/demo/bcf/bcf-creative/',
    'categories' => array('Premium'),
    'screenshot' => 'https://demo.athemeart.com/demo-import/business-consultant-finder/bcf-creative.png',
    'pro' => 'https://athemeart.com/downloads/business-consultant-finder/', 
);

$data['multipurpose'] = array(
    'demo_name' => 'Multipurpose Business',
    'demo_url' => 'https://athemeart.dev/demo/bcf/bcf-business/',
    'categories' => array('Premium'),
    'screenshot' => 'https://demo.athemeart.com/demo-import/business-consultant-finder/bcf-business.png',
    'pro' => 'https://athemeart.com/downloads/business-consultant-finder/', 
);

$data['Woocommerce'] = array(
    'demo_name' => 'WooCommerce',
    'demo_url' => 'https://athemeart.dev/demo/bcf/bcf-shop/',
    'categories' => array('Premium'),
    'screenshot' => 'https://demo.athemeart.com/demo-import/business-consultant-finder/bcf-shop.png',
    'pro' => 'https://athemeart.com/downloads/business-consultant-finder/', 
);