<?php
use Drupal\Core\Form\FormStateInterface;
/**
 * @file
 * Custom setting for kite theme.
 */
function kite_form_system_theme_settings_alter(&$form, FormStateInterface &$form_state, $form_id = NULL) {
  $form['kite'] = [
    '#type'       => 'vertical_tabs',
    '#title'      => '<h3>' . t('kite Theme Settings') . '</h3>',
    '#default_tab' => 'general',
  ];

  // General settings tab.
  $form['general'] = [
    '#type'  => 'details',
    '#title' => t('General'),
    '#description' => t('<h3>Thanks for using kite Theme</h3>kite is a free Drupal 8, 9, 10 theme design.'),
    '#group' => 'kite',
  ];


  // Slider tab.
  $form['slider'] = [
    '#type'  => 'details',
    '#title' => t('Homepage Slider'),
    '#description' => t('<h3>Manage Homepage Slider</h3>'),
    '#group' => 'kite',
  ];





  
  /**
   * Slider Settings
   */
  // Show or hide slider on homepage.
  $form['slider']['slider_enable_option'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Enable Slider'),
  ];

  $form['slider']['slider_enable_option']['slider_show'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show Slider on Homepage'),
    '#default_value' => theme_get_setting('slider_show', 'kite'),
    '#description'   => t("Check this option to show slider on homepage. Uncheck to hide."),
  ];
  /* Slider -> Image upload */
  $form['slider']['slider_image_section'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Slider Background Image'),
  ];
  $form['slider']['slider_image_section']['slider_image'] = [
    '#type'          => 'managed_file',
    '#upload_location' => 'public://',
    '#upload_validators' => array(
      'file_validate_extensions' => array('gif png jpg jpeg svg'),
    ),
    '#title'  => t('<p>Upload Slider Image</p>'),
    '#default_value'  => theme_get_setting('slider_image', 'kite'),
    '#description'   => t('kite theme has limitation of single image for banner.')
  ];
  $form['slider']['slider_time_field'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Autoplay Interval Time'),
  ];
  $form['slider']['slider_time_field']['slider_time'] = [
    '#type'          => 'number',
    '#default_value' => theme_get_setting('slider_time', 'kite'),
    '#title'         => t('Enter slider interval time between two slides'),
    '#description'   => t('Time interval between two slides. Default value is 5000, this means 5 seconds.'),
  ];

  $form['slider']['slider_dots_field'] = [
    '#type'          => 'fieldset',
    '#title'         => t('Slider Dots Navigation'),
  ];

  $form['slider']['slider_dots_field']['slider_dots'] = [
    '#type'          => 'select',
    '#title'         => t('Show or Hide Slider Dots Navigation'),
    '#options' => array(
      'true' => t('Show'),
      'false' => t('Hide'),),
    '#default_value' => theme_get_setting('slider_dots', 'kite'),
    '#description'   => t('Show or hide slider dots navigation that appears at the bottom of slider.'),
  ];

  $form['slider']['slider_code'] = [
    '#type'          => 'textarea',
    '#title'         => t('Slider Code'),
    '#default_value' => theme_get_setting('slider_code', 'kite'),
    '#description'   => t('Please refer to this <a href="https://drupar.com/kite-theme-documentation/how-manage-homepage-slider" target="_blank">documentation page</a> for slider code tutorial.'),
  ];

  

  // Settings under footer tab.
  // Scroll to top.
  $form['footer']['scrolltotop'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Scroll To Top'),
  ];

  $form['footer']['scrolltotop']['scrolltotop_on'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable scroll to top feature.'),
    '#default_value' => theme_get_setting('scrolltotop_on', 'kite'),
    '#description'   => t("Check this option to enable scroll to top feature. Uncheck to disable this fearure and hide scroll to top icon."),
  ];

  // Footer -> Copyright.
  $form['footer']['copyright'] = [
    '#type'        => 'fieldset',
    '#title'       => t('Website Copyright Text'),
  ];

  $form['footer']['copyright']['copyright_text'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Show website copyright text in footer.'),
    '#default_v alue' => theme_get_setting('copyright_text', 'kite'),
    '#description'   => t("Check this option to show website copyright text in footer. Uncheck to hide."),
  ];



  /**
   * Insert Codes
   */
  $form['insert_codes']['insert_codes_tab'] = [
    '#type'  => 'vertical_tabs',
  ];


// End form.
}
