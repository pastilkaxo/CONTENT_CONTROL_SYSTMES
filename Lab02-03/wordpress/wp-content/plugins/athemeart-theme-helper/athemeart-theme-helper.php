<?php
/**
 * Plugin Name: aThemeArt Demo Import
 * Plugin URI: http://athemeart.com/
 * Description: Import aThemeArt official themes demo content, widgets and theme settings with just one click..
 * Version: 1.0.5
 * Author: aThemeart
 * Author URI: https://athemeart.com
 * License: GPLv3 or later
 * Text Domain: athemeart-theme-helper
 * Domain Path: /languages/
 * Tested up to: 6.0.2
 * @package athemeart-theme-helper
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Returns the main instance of aThemeArt_Demo_Import to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object aThemeArt_Demo_Import
 */
function aThemeArt_Demo_Import() {
    return aThemeArt_Demo_Import::instance();
}

// End aThemeArt_Demo_Import()

add_action('init', 'aThemeArt_Demo_Import');

define('ATA_DEMO_URL', plugin_dir_url( __FILE__ ));

/**
 * Main aThemeArt_Demo_Import Class
 *
 * @class aThemeArt_Demo_Import
 * @version	1.0.0
 * @since 1.0.0
 * @package	aThemeArt_Demo_Import
 */
final class aThemeArt_Demo_Import {

    /**
     * aThemeArt_Demo_Import The single instance of aThemeArt_Demo_Import.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $version;

    // Admin - Start

    /**
     * The admin object.
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $admin;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($widget_areas = array()) {
        $this->token = 'athemeart-theme-helper';
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->version = '1.0';

        define('ATHEMEART_URL', $this->plugin_url);
        define('ATHEMEART_PATH', $this->plugin_path);
        define('ATHEMEART_VERSION', $this->version);
        define('ATHEMEART_FILE_PATH', __FILE__);
        define('ATHEMEART_ADMIN_PANEL_HOOK_PREFIX', 'theme-panel_page_athemeart-panel');


        register_activation_hook(__FILE__, array($this, 'install'));

        add_action('init', array($this, 'load_plugin_textdomain'));
        
        // Demos scripts
        add_action('admin_enqueue_scripts', array($this, 'scripts'));

        //007
        

        require_once( ATHEMEART_PATH . 'includes/panel/demos.php' );
           
        

      
    }
    
    public static function scripts() {
            
            wp_enqueue_style('athemeart-notices', plugins_url('includes/panel/assets/css/notify.css', __FILE__));
        }

    /**
     * Main aThemeArt_Demo_Import Instance
     *
     * Ensures only one instance of aThemeArt_Demo_Import is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see aThemeArt_Demo_Import()
     * @return Main aThemeArt_Demo_Import instance
     */
    public static function instance() {
        if (is_null(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    }

// End instance()

    /**
     * Load the localisation file.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('athemeart-theme-helper', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone() {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '1.0.0');
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '1.0.0');
    }

    /**
     * Installation.
     * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install() {
        $this->_log_version_number();
    }

    /**
     * Log the plugin version number.
     * @access  private
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number() {
        // Log the version number.
        update_option($this->token . '-version', $this->version);
    }

}

// End Class

/**
 * Add Metadata on plugin activation.
 */
function athemeart_extra_activate() {
    add_option('athemeart_activation_redirect', true);
}

register_activation_hook(__FILE__, 'athemeart_extra_activate');

/**
 * Remove Metadata on plugin Deactivation.
 */
function athemeart_extra_deactivate() {
    delete_option('athemeart_activation_redirect');
}

register_deactivation_hook(__FILE__, 'athemeart_extra_deactivate');


add_action('admin_init', 'athemeart_plugin_redirect');

/**
 * Redirect after plugin activation
 */
function athemeart_plugin_redirect() {
    if (get_option('athemeart_activation_redirect', false)) {
        delete_option('athemeart_activation_redirect');
        if (!is_network_admin() || !isset($_GET['activate-multi'])) {
            wp_redirect('themes.php?page=athemeart-panel-install-demos');
        }
    }
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'athemeart_action_links');

function athemeart_action_links($links) {
    $links['install_demos'] = sprintf('<a href="%1$s" class="install-demos">%2$s</a>', esc_url(admin_url('themes.php?page=athemeart-panel-install-demos')), esc_html__('Install Demos', 'athemeart-theme-helper'));
    return $links;
}

remove_filter( 'wp_import_post_meta', 'Elementor\Compatibility::on_wp_import_post_meta');
remove_filter( 'wxr_importer.pre_process.post_meta', 'Elementor\Compatibility::on_wxr_importer_pre_process_post_meta');

