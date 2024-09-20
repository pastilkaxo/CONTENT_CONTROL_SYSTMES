<?php
/**
 * Demos
 *
 * @package aThemeArt_Demo_Import
 * @category Core
 * @author aThemeArt
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Start Class
if (!class_exists('aThemeArt_Demos')) {

    class aThemeArt_Demos {

        /**
         * Start things up
         */
        public function __construct() {

            // Return if not in admin
            if (!is_admin() || is_customize_preview()) {
                return;
            }

            // Import demos page
            if (version_compare(PHP_VERSION, '5.4', '>=')) {
                require_once( ATHEMEART_PATH . '/includes/panel/classes/importers/class-helpers.php' );
                require_once( ATHEMEART_PATH . '/includes/panel/classes/class-install-demos.php' );
            }

            // Start things
            add_action('admin_init', array($this, 'init'));

            // Demos scripts
            add_action('admin_enqueue_scripts', array($this, 'scripts'));

            // Allows xml uploads
            add_filter('upload_mimes', array($this, 'allow_xml_uploads'));

            // Demos popup
            add_action('admin_footer', array($this, 'popup'));
        }

        /**
         * Register the AJAX methods
         *
         * @since 1.0.0
         */
        public function init() {

            // Demos popup ajax
            add_action('wp_ajax_athemeart_ajax_get_demo_data', array($this, 'ajax_demo_data'));
            add_action('wp_ajax_athemeart_ajax_required_plugins_activate', array($this, 'ajax_required_plugins_activate'));

            // Get data to import
            add_action('wp_ajax_athemeart_ajax_get_import_data', array($this, 'ajax_get_import_data'));

            // Import XML file
            add_action('wp_ajax_athemeart_ajax_import_xml', array($this, 'ajax_import_xml'));

            // Import customizer settings
            add_action('wp_ajax_athemeart_ajax_import_theme_settings', array($this, 'ajax_import_theme_settings'));

            // Import widgets
            add_action('wp_ajax_athemeart_ajax_import_widgets', array($this, 'ajax_import_widgets'));

            // After import
            add_action('wp_ajax_athemeart_after_import', array($this, 'ajax_after_import'));
        }

        /**
         * Load scripts
         *
         * @since 1.4.5
         */
        public static function scripts($hook_suffix) {

            if ('appearance_page_athemeart-panel-install-demos' == $hook_suffix) {

                // CSS
                wp_enqueue_style('athemeart-demos-style', plugins_url('/assets/css/demos.min.css', __FILE__));

                // JS
                wp_enqueue_script('athemeart-demos-js', plugins_url('/assets/js/demos.min.js', __FILE__), array('jquery', 'wp-util', 'updates'), '1.0', true);

                wp_localize_script('athemeart-demos-js', 'aThemeDemos', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'demo_data_nonce' => wp_create_nonce('get-demo-data'),
                    'athemeart_import_data_nonce' => wp_create_nonce('athemeart_import_data_nonce'),
                    'content_importing_error' => esc_html__('There was a problem during the importing process resulting in the following error from your server:', 'athemeart-theme-helper'),
                    'button_activating' => esc_html__('Activating', 'athemeart-theme-helper') . '&hellip;',
                    'button_active' => esc_html__('Active', 'athemeart-theme-helper'),
                ));
            }
            
            //wp_enqueue_style('athemeart-notices', plugins_url('/assets/css/notify.css', __FILE__));
        }

        /**
         * Allows xml uploads so we can import from server
         *
         * @since 1.0.0
         */
        public function allow_xml_uploads($mimes) {
            $mimes = array_merge($mimes, array(
                'xml' => 'application/xml'
            ));
            return $mimes;
        }

        /**
         * Get demos data to add them in the Demo Import and Pro Demos plugins
         *
         * @since 1.4.5
         */
        public static function get_demos_data() {
            $theme = wp_get_theme();

            $data = array(

                'blog' => array(
                    'demo_name' => 'Blog',
                    'categories' => array('free'),
                    'xml_file' => 'https://demo.athemeart.com/demo-import/blog-content.xml',
                    'theme_settings' =>  'https://demo.athemeart.com/demo-import/default-customizer.dat',
                    'widgets_file' =>  'https://demo.athemeart.com/demo-import/default-customizer.dat',
                    'screenshot' => 'https://demo.athemeart.com/demo-import/blog-screenshot.jpg',
                    'blog_title' => 'Blog',
                    'posts_to_show' => '6',
                    'elementor_width' => '1400',
                    'is_shop' => false,
                    'required_plugins' => array(
                        'free' => array(
                            array(
                                'slug' => 'elementor',
                                'init' => 'elementor/elementor.php',
                                'name' => 'Elementor',
                            ),
                        ),
                    'premium' => array( ),
                    ),
                ), 
            );

            if( file_exists( ATHEMEART_PATH . 'data/'.$theme->template.'/init.php') ){   
                require_once( ATHEMEART_PATH . 'data/'.$theme->template.'/init.php' );
            }
            
            // Return
            return apply_filters('athemeart_demos_data', $data);
        }

        /**
         * Get the category list of all categories used in the predefined demo imports array.
         *
         * @since 1.4.5
         */
        public static function get_demo_all_categories($demo_imports) {
            $categories = array();

            foreach ($demo_imports as $item) {
                if (!empty($item['categories']) && is_array($item['categories'])) {
                    foreach ($item['categories'] as $category) {
                        $categories[sanitize_key($category)] = $category;
                    }
                }
            }

            if (empty($categories)) {
                return false;
            }

            return $categories;
        }

        /**
         * Return the concatenated string of demo import item categories.
         * These should be separated by comma and sanitized properly.
         *
         * @since 1.4.5
         */
        public static function get_demo_item_categories($item) {
            $sanitized_categories = array();

            if (isset($item['categories'])) {
                foreach ($item['categories'] as $category) {
                    $sanitized_categories[] = sanitize_key($category);
                }
            }

            if (!empty($sanitized_categories)) {
                return implode(',', $sanitized_categories);
            }

            return false;
        }

        /**
         * Demos popup
         *
         * @since 1.4.5
         */
        public static function popup() {
            global $pagenow;
            if (isset($_GET['page'])) {
                // Display on the demos pages
                if (( 'themes.php' == $pagenow && 'athemeart-panel-install-demos' == $_GET['page'])) {
                    ?>

                    <div id="athemeart-demo-popup-wrap">
                        <div class="athemeart-demo-popup-container">
                            <div class="athemeart-demo-popup-content-wrap">
                                <div class="athemeart-demo-popup-content-inner">
                                    <a href="#" class="athemeart-demo-popup-close">×</a>
                                    <div id="athemeart-demo-popup-content"></div>
                                </div>
                            </div>
                        </div>
                        <div class="athemeart-demo-popup-overlay"></div>
                    </div>

                    <?php
                }
            }
        }

        /**
         * Demos popup ajax.
         *
         * @since 1.4.5
         */
        public static function ajax_demo_data() {

            if (!current_user_can('manage_options') || !wp_verify_nonce($_GET['demo_data_nonce'], 'get-demo-data')) {
                die('This action was stopped for security purposes.');
            }

            // Database reset url
            if (is_plugin_active('wordpress-database-reset/wp-reset.php')) {
                $plugin_link = admin_url('tools.php?page=database-reset');
            } else {
                $plugin_link = admin_url('plugin-install.php?s=WordPress+Database+Reset&tab=search');
            }
      
            // Get all demos
            $demos = self::get_demos_data();

            // Get selected demo
            if (isset($_GET['demo_name'])) {
                $demo = sanitize_text_field( wp_unslash( $_GET['demo_name'] ));
            }

            // Get required plugins
            $plugins = $demos[$demo]['required_plugins'];

            // Get free plugins
            $free = $plugins['free'];

            // Get premium plugins
            
            $premium = isset($plugins['premium']) ? $plugins['premium'] : '' ;
            ?>

            <div id="athemeart-demo-plugins">

                <h2 class="title"><?php echo sprintf(esc_html__('Import the %1$s demo', 'athemeart-theme-helper'), esc_attr($demos[$demo]['demo_name'])); ?></h2>

                <div class="athemeart-popup-text">

                    <p><?php
            echo
            sprintf(
                    esc_html__('Importing demo data allow you to quickly edit everything instead of creating content from scratch. It is recommended uploading sample data on a fresh WordPress install to prevent conflicts with your current content. You can use this plugin to reset your site if needed: %1$sWordpress Database Reset%2$s.', 'athemeart-theme-helper'),
                    '<a href="' . esc_url( $plugin_link ) . '" target="_blank">',
                    '</a>'
            );
            ?></p>

                    <div class="athemeart-required-plugins-wrap">
                        <h3><?php esc_html_e('Required Plugins', 'athemeart-theme-helper'); ?></h3>
                        <p><?php esc_html_e('For your site to look exactly like this demo, the plugins below need to be activated.', 'athemeart-theme-helper'); ?></p>
                        <div class="athemeart-required-plugins oe-plugin-installer">
                            <?php
                            self::required_plugins($free, 'free');
                            self::required_plugins($premium, 'premium');
                            ?>
                        </div>
                    </div>

                </div>

                <a class="athemeart-button athemeart-plugins-next" href="#"><?php esc_html_e('Go to the next step', 'athemeart-theme-helper'); ?></a>

            </div>

            <form method="post" id="athemeart-demo-import-form">

                <input id="athemeart_import_demo" type="hidden" name="athemeart_import_demo" value="<?php echo esc_attr($demo); ?>" />

                <div class="athemeart-demo-import-form-types">

                    <h2 class="title"><?php esc_html_e('Select what you want to import:', 'athemeart-theme-helper'); ?></h2>

                    <ul class="athemeart-popup-text">
                        <li>
                            <label for="athemeart_import_xml">
                                <input id="athemeart_import_xml" type="checkbox" name="athemeart_import_xml" checked="checked" />
                                <strong><?php esc_html_e('Import XML Data', 'athemeart-theme-helper'); ?></strong> (<?php esc_html_e('pages, posts, images, menus, etc...', 'athemeart-theme-helper'); ?>)
                            </label>
                        </li>

                        <li>
                            <label for="athemeart_theme_settings">
                                <input id="athemeart_theme_settings" type="checkbox" name="athemeart_theme_settings" checked="checked" />
                                <strong><?php esc_html_e('Import Customizer Settings', 'athemeart-theme-helper'); ?></strong>
                            </label>
                        </li>

                        <li>
                            <label for="athemeart_import_widgets">
                                <input id="athemeart_import_widgets" type="checkbox" name="athemeart_import_widgets" checked="checked" />
                                <strong><?php esc_html_e('Import Widgets', 'athemeart-theme-helper'); ?></strong>
                            </label>
                        </li>
                    </ul>

                </div>

                <?php wp_nonce_field('athemeart_import_demo_data_nonce', 'athemeart_import_demo_data_nonce'); ?>
                <input type="submit" name="submit" class="athemeart-button athemeart-import" value="<?php esc_html_e('Install this demo', 'athemeart-theme-helper'); ?>"  />

            </form>

            <div class="athemeart-loader">
                <h2 class="title"><?php esc_html_e('The import process could take some time, please be patient', 'athemeart-theme-helper'); ?></h2>
                <div class="athemeart-import-status athemeart-popup-text"></div>
            </div>

            <div class="athemeart-last">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path></svg>
                <h3><?php esc_html_e('Demo Imported!', 'athemeart-theme-helper'); ?></h3>
                <a href="<?php echo esc_url(get_home_url()); ?>"" target="_blank"><?php esc_html_e('See the result', 'athemeart-theme-helper'); ?></a>
            </div>

            <?php
            die();
        }

        /**
         * Required plugins.
         *
         * @since 1.4.5
         */
        public static function required_plugins($plugins, $return) {

            foreach ($plugins as $key => $plugin) {

                $api = array(
                    'slug' => isset($plugin['slug']) ? $plugin['slug'] : '',
                    'init' => isset($plugin['init']) ? $plugin['init'] : '',
                    'name' => isset($plugin['name']) ? $plugin['name'] : '',
                );

                if (!is_wp_error($api)) { // confirm error free
                    // Installed but Inactive.
                    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin['init']) && is_plugin_inactive($plugin['init'])) {

                        $button_classes = 'button activate-now button-primary';
                        $button_text = esc_html__('Activate', 'athemeart-theme-helper');

                        // Not Installed.
                    } elseif (!file_exists(WP_PLUGIN_DIR . '/' . $plugin['init'])) {

                        $button_classes = 'button install-now';
                        $button_text = esc_html__('Install Now', 'athemeart-theme-helper');

                        // Active.
                    } else {
                        $button_classes = 'button disabled';
                        $button_text = esc_html__('Activated', 'athemeart-theme-helper');
                    }
                    ?>

                    <div class="athemeart-plugin athemeart-clr athemeart-plugin-<?php echo esc_attr($api['slug']); ?>" data-slug="<?php echo esc_attr($api['slug']); ?>" data-init="<?php echo esc_attr($api['init']); ?>">
                        <h2><?php echo esc_html($api['name']); ?></h2>

                        
                        <button class="<?php echo esc_attr($button_classes); ?>" data-init="<?php echo esc_attr($api['init']); ?>" data-slug="<?php echo esc_attr($api['slug']); ?>" data-name="<?php echo esc_attr($api['name']); ?>"><?php echo esc_html($button_text); ?></button>
                       
                    </div>

                    <?php
                }
            }
        }

        /**
         * Required plugins activate
         *
         * @since 1.4.5
         */
        public function ajax_required_plugins_activate() {

            if (!current_user_can('install_plugins') || !isset($_POST['init']) || !$_POST['init']) {
                wp_send_json_error(
                        array(
                            'success' => false,
                            'message' => __('No plugin specified', 'athemeart-theme-helper'),
                        )
                );
            }

            $plugin_init = ( isset($_POST['init']) ) ? esc_attr($_POST['init']) : '';
            $activate = activate_plugin($plugin_init, '', false, true);

            if (is_wp_error($activate)) {
                wp_send_json_error(
                        array(
                            'success' => false,
                            'message' => $activate->get_error_message(),
                        )
                );
            }

            wp_send_json_success(
                    array(
                        'success' => true,
                        'message' => __('Plugin Successfully Activated', 'athemeart-theme-helper'),
                    )
            );
        }

        /**
         * Returns an array containing all the importable content
         *
         * @since 1.4.5
         */
        public function ajax_get_import_data() {
            if (!current_user_can('manage_options')) {
                die('This action was stopped for security purposes.');
            }
            check_ajax_referer('athemeart_import_data_nonce', 'security');

            echo json_encode(
                    array(
                        array(
                            'input_name' => 'athemeart_import_xml',
                            'action' => 'athemeart_ajax_import_xml',
                            'method' => 'ajax_import_xml',
                            'loader' => esc_html__('Importing XML Data', 'athemeart-theme-helper')
                        ),
                        array(
                            'input_name' => 'athemeart_theme_settings',
                            'action' => 'athemeart_ajax_import_theme_settings',
                            'method' => 'ajax_import_theme_settings',
                            'loader' => esc_html__('Importing Customizer Settings', 'athemeart-theme-helper')
                        ),
                        array(
                            'input_name' => 'athemeart_import_widgets',
                            'action' => 'athemeart_ajax_import_widgets',
                            'method' => 'ajax_import_widgets',
                            'loader' => esc_html__('Importing Widgets', 'athemeart-theme-helper')
                        ),
                    )
            );

            die();
        }

        /**
         * Import XML file
         *
         * @since 1.4.5
         */
        public function ajax_import_xml() {
            if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['athemeart_import_demo_data_nonce'], 'athemeart_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }

            // Get the selected demo
            if (isset($_POST['athemeart_import_demo'])) {
                $demo_type = sanitize_text_field(wp_unslash($_POST['athemeart_import_demo']));
            }

            // Get demos data
            $demo = aThemeArt_Demos::get_demos_data()[$demo_type];

            // Content file
            $xml_file = isset($demo['xml_file']) ? $demo['xml_file'] : '';

            

            // Delete the default post and page
            $sample_page = get_page_by_path('sample-page', OBJECT, 'page');
            $hello_world_post = get_page_by_path('hello-world', OBJECT, 'post');

            if (!is_null($sample_page)) {
                wp_delete_post($sample_page->ID, true);
            }

            if (!is_null($hello_world_post)) {
                wp_delete_post($hello_world_post->ID, true);
            }

            // Import Posts, Pages, Images, Menus.
            $result = $this->process_xml($xml_file);
            
            
            if (is_wp_error($result)) {
                echo json_encode($result->errors);
            } else {
                echo 'successful import';
            }

            die();
        }

        /**
         * Import customizer settings
         *
         * @since 1.4.5
         */
        public function ajax_import_theme_settings() {
            if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['athemeart_import_demo_data_nonce'], 'athemeart_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }

            // Include settings importer
            include ATHEMEART_PATH . 'includes/panel/classes/importers/class-settings-importer.php';

            // Get the selected demo
            if (isset($_POST['athemeart_import_demo'])) {
                $demo_type = sanitize_text_field(wp_unslash($_POST['athemeart_import_demo']));
            }

            // Get demos data
            $demo = aThemeArt_Demos::get_demos_data()[$demo_type];

            // Settings file
            $theme_settings = isset($demo['theme_settings']) ? $demo['theme_settings'] : '';

            // Import settings.
            $settings_importer = new athemeart_Settings_Importer();
            $result = $settings_importer->process_import_file($theme_settings);

            if (is_wp_error($result)) {
                echo json_encode($result->errors);
            } else {
                echo 'successful import';
            }

            die();
        }

        /**
         * Import widgets
         *
         * @since 1.4.5
         */
        public function ajax_import_widgets() {
            if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['athemeart_import_demo_data_nonce'], 'athemeart_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }

            // Include widget importer
            include ATHEMEART_PATH . 'includes/panel/classes/importers/class-widget-importer.php';

            // Get the selected demo
            if (isset($_POST['athemeart_import_demo'])) {
                $demo_type = sanitize_text_field(wp_unslash($_POST['athemeart_import_demo']));
            }

            // Get demos data
            $demo = aThemeArt_Demos::get_demos_data()[$demo_type];

            // Widgets file
            $widgets_file = isset($demo['widgets_file']) ? $demo['widgets_file'] : '';

            // Import settings.
            $widgets_importer = new athemeart_Widget_Importer();
            $result = $widgets_importer->process_import_file($widgets_file);

            if (is_wp_error($result)) {
                echo json_encode($result->errors);
            } else {
                echo 'successful import';
            }

            die();
        }

        /**
         * After import
         *
         * @since 1.4.5
         */
        public function ajax_after_import() {
            if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['athemeart_import_demo_data_nonce'], 'athemeart_import_demo_data_nonce')) {
                die('This action was stopped for security purposes.');
            }

            // If XML file is imported
            if ($_POST['athemeart_import_is_xml'] === 'true') {

                // Get the selected demo
                if (isset($_POST['athemeart_import_demo'])) {
                    $demo_type = sanitize_text_field(wp_unslash($_POST['athemeart_import_demo']));
                }

                // Get demos data
                $demo = aThemeArt_Demos::get_demos_data()[$demo_type];

               
                // Reading settings
                $homepage_title = isset($demo['home_title']) ? $demo['home_title'] : 'Home';
                $blog_title = isset($demo['blog_title']) ? $demo['blog_title'] : '';

                // Posts to show on the blog page
                $posts_to_show = isset($demo['posts_to_show']) ? $demo['posts_to_show'] : '';

                
                

                // Set imported menus to registered theme locations
                $locations = get_theme_mod('nav_menu_locations');
                $menus = wp_get_nav_menus();

                if ($menus) {

                    foreach ($menus as $menu) {

                        if ($menu->name == 'Main Menu') {
                            $locations['menu-1'] = $menu->term_id;
                            $locations['main_menu'] = $menu->term_id;
                            $locations['primary'] = $menu->term_id;
                        }
                        if ($menu->name == 'Primary') {
                          $locations['menu-1'] = $menu->term_id;
                            $locations['main_menu'] = $menu->term_id;
                            $locations['primary'] = $menu->term_id;
                        }
                        if ($menu->name == 'Main Menu') {
                            $locations['menu-1'] = $menu->term_id;
                            $locations['main_menu'] = $menu->term_id;
                            $locations['primary'] = $menu->term_id;
                        }
                         if ($menu->name == 'Primary Menu') {
                            $locations['menu-1'] = $menu->term_id;
                            $locations['main_menu'] = $menu->term_id;
                            $locations['primary'] = $menu->term_id;
                        }
                        
                        
                        
                    }
                }

                // Set menus to locations
                set_theme_mod('nav_menu_locations', $locations);

                // Disable Elementor default settings
                //update_option( 'elementor_disable_color_schemes', 'yes' );
                //update_option( 'elementor_disable_typography_schemes', 'yes' );
                if (!empty($elementor_width)) {
                    update_option('elementor_container_width', $elementor_width);
                }

                // Assign front page and posts page (blog page).
                $home_page = get_page_by_title($homepage_title);
                $blog_page = get_page_by_title($blog_title);

                update_option('show_on_front', 'page');

                if (is_object($home_page)) {
                    update_option('page_on_front', $home_page->ID);
                }

                if (is_object($blog_page)) {
                    update_option('page_for_posts', $blog_page->ID);
                }

                // Posts to show on the blog page
                if (!empty($posts_to_show)) {
                    update_option('posts_per_page', $posts_to_show);
                }
            }

            die();
        }

        /**
         * Import XML data
         *
         * @since 1.0.0
         */
        public function process_xml($file) {

            $response = athemeart_Demos_Helpers::get_remote($file);

            // No sample data found
            if ($response === false) {
                return new WP_Error('xml_import_error', __('Can not retrieve sample data xml file. The server may be down at the moment please try again later. If you still have issues contact the theme developer for assistance.', 'athemeart-theme-helper'));
            }

            // Write sample data content to temp xml file
            $temp_xml = ATHEMEART_PATH . 'includes/panel/temp.xml';
            file_put_contents($temp_xml, $response);

            // Set temp xml to attachment url for use
            $attachment_url = $temp_xml;

            // If file exists lets import it
            if (file_exists($attachment_url)) {
                $this->import_xml($attachment_url);
            } else {
                // Import file can't be imported - we should die here since this is core for most people.
                return new WP_Error('xml_import_error', __('The xml import file could not be accessed. Please try again or contact the theme developer.', 'athemeart-theme-helper'));
            }
        }

        /**
         * Import XML file
         *
         * @since 1.0.0
         */
        private function import_xml($file) {

            // Make sure importers constant is defined
            if (!defined('WP_LOAD_IMPORTERS')) {
                define('WP_LOAD_IMPORTERS', true);
            }

            // Import file location
            $import_file = ABSPATH . 'wp-admin/includes/import.php';

            // Include import file
            if (!file_exists($import_file)) {
                return;
            }

            // Include import file
            require_once( $import_file );

            // Define error var
            $importer_error = false;

            if (!class_exists('WP_Importer')) {
                $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

                if (file_exists($class_wp_importer)) {
                    require_once $class_wp_importer;
                } else {
                    $importer_error = __('Can not retrieve class-wp-importer.php', 'athemeart-theme-helper');
                }
            }

            if (!class_exists('WP_Import')) {
                $class_wp_import = ATHEMEART_PATH . 'includes/panel/classes/importers/class-wordpress-importer.php';

                if (file_exists($class_wp_import)) {
                    require_once $class_wp_import;
                } else {
                    $importer_error = __('Can not retrieve wordpress-importer.php', 'athemeart-theme-helper');
                }
            }

            // Display error
            if ($importer_error) {
                return new WP_Error('xml_import_error', $importer_error);
            } else {

                // No error, lets import things...
                if (!is_file($file)) {
                    $importer_error = __('Sample data file appears corrupt or can not be accessed.', 'athemeart-theme-helper');
                    return new WP_Error('xml_import_error', $importer_error);
                    
                } else {
                    $importer = new WP_Import();
                    $importer->fetch_attachments = true;
                    $importer->import($file);

                    // Clear sample data content from temp xml file
                    $temp_xml = ATHEMEART_PATH . 'includes/panel/temp.xml';
                    unlink($temp_xml);
                }
            }
        }

    }

}
new aThemeArt_Demos();
