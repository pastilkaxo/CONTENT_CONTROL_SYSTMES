<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
if( ! class_exists( 'WP_Customize_Control' ) ){
	return;
}
add_action( 'customize_preview_init', 'amaz_store_focus_section_enqueue');
add_action( 'customize_controls_init', 'amaz_store_focus_section_helper_script_enqueue' );
function amaz_store_focus_section_enqueue(){
	   wp_enqueue_style( 'amaz-store-focus-section-style',AMAZ_STORE_THEME_URI . 'customizer/customize-focus-section/css/focus-section.css');
		wp_enqueue_script( 'amaz-store-focus-section-script',AMAZ_STORE_THEME_URI . 'customizer/customize-focus-section/js/focus-section.js', array('jquery'),'',false);
	}
function amaz_store_focus_section_helper_script_enqueue(){
		wp_enqueue_script( 'amaz-store-focus-section-addon-script', AMAZ_STORE_THEME_URI . 'customizer/customize-focus-section/js/addon-focus-section.js', array('jquery'),'',false);
	}

