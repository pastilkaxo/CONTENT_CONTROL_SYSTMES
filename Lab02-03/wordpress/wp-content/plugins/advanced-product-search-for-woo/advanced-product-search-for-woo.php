<?php
/*
Plugin Name: Advanced Product Search For WooCommerce
Plugin URI: https://athemeart.net/downloads/advanced-product-search-for-woo/
Description:Advanced Product Search For woocommerce  – powerful & simple live search plugin for WooCommerce
Version: 1.1.7
Author: aThemeArt
Author URI:  https://athemeart.net
Domain Path: /languages/
Tested up to: 6.6.5
WC requires at least: 3.0.0
WC tested up to: 8.6.2
*/

if ( ! defined('ABSPATH')) exit;  // if direct access



class APSW_Product_Search_Finale_Class {
	
	 /**
	 * The option_search_results
	 *
	 */
	public $option_search_from;
	 /**
	 * The option_search_results
	 *
	 */
	public $option_search_results;
	
	 /**
	 * The option_search_results
	 *
	 */
	public $option_color;
	 /**
	 * The single instance of the class
	 *
	 * @var Advanced_Product_Search_For_Woo
	 */
	private static $_instance;
 	/**
	 * Class constructor.
	 */
    function __construct() {
	
        $this->apsw_load_defines();
        $this->apsw_load_scripts();
		$this->apsw_load_textdomain();
        $this->apsw_load_functions();
        $this->apsw_load_classes();
		$this->option_search_from 		= wp_parse_args ( apsw_get_option('apsw_search_form') );
		$this->option_search_results 	= wp_parse_args ( apsw_get_option('apsw_search_results') );
		$this->option_color 			= wp_parse_args ( apsw_get_option('apsw_color_scheme') );
		
		add_filter( 'plugin_action_links', array( $this, 'go_pro' ), 999, 2 );
		add_action( 'admin_notices', array( $this, 'sample_admin_notice__success' ) );
		
		
		add_action( 'wp_ajax_nopriv_apsw_apsw_dismiss_notice', array( $this, 'dismiss_nux' ) );
		add_action( 'wp_ajax_apsw_dismiss_notice', array( $this, 'dismiss_nux' ) );
		
		//add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'settings_action_links' )  );
    }
	/**
	 * Main instance
	 *
	 * @return Advanced_Product_Search_For_Woo
	 */
	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	/**
	 *
	 * @return plugins related function 
	 */
    public function apsw_load_functions() {

      // require APSW_PLUGIN_DIR . 'includes/search-template.php';
	  
	  $this->apsw_load_module( 'includes/aspw-helper-functions' );
	   
    }
	/**
	 *
	 * @return plugins related function 
	 */
   public function apsw_load_classes() {
		
		
		if( !class_exists('Predic_Widget') ){
			$this->apsw_load_module( 'lib/predic-widget/predic-widget' );
		}
		
		$this->apsw_load_module( 'lib/class.settings-api' );
		
		$this->apsw_load_module( 'includes/classes/classes-settings-api' );
		
		$this->apsw_load_module( 'includes/classes/classes-search' );
		$this->apsw_load_module( 'includes/classes/widgets' );
		
    }
	
	protected static function apsw_load_module( $mod ) {
		$dir = APSW_PLUGIN_DIR;
	
		if ( empty( $dir ) or ! is_dir( $dir ) ) {
			return false;
		}
	
		$file = path_join( $dir, $mod . '.php' );
	
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}



    public function apsw_admin_scripts() {

	    wp_enqueue_style('apsw-style',  plugins_url( 'assets/admin/css/style.css' , __FILE__ ), array(), time());
     	
        
        wp_enqueue_script('jquery');
	  
        wp_enqueue_script('apsw-plugins-scripts', plugins_url( 'assets/admin/js/admin-scripts.js' , __FILE__ ) , array( 'jquery' ));
		
		$apsw_notify = array(
			'nonce' => wp_create_nonce( 'apsw_notice_dismiss_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php'),
		);
		
		wp_localize_script( 'apsw-plugins-scripts', 'apsw_loc', $apsw_notify );
       
    }

    public function apsw_front_scripts() {

	    wp_enqueue_style('apsw-styles', plugins_url( 'assets/front/css/style.css' , __FILE__ ), array(), time());
		
		 
	   
		$custom_css = "  .apsw-search-wrap {max-width:".absint( $this->option_search_from['search_bar_width'] ) ."px;}
		.apsw-search-wrap .apsw-search-form input[type='search'],.apsw-search-wrap.apsw_search_form_style_4 button.apsw-search-btn,.apsw-search-wrap.apsw_search_form_style_5 button.apsw-search-btn,.apsw-search-wrap.apsw_search_form_style_6 button.apsw-search-btn,.apsw-search-wrap .apsw-search-btn{ height:".absint( $this->option_search_from['search_bar_height'] ) ."px; line-height: ".absint( $this->option_search_from['search_bar_height'] )."px }
		.apsw-search-wrap .apsw-select-box-wrap{height:".absint( $this->option_search_from['search_bar_height'] ) ."px;}
		.apsw-search-wrap .apsw-category-items{ line-height: ".absint( $this->option_search_from['search_bar_height'] )."px; }
		.apsw_ajax_result{ top:".absint( $this->option_search_from['search_bar_height'] + 1)."px; }
		";
			
		$custom_css .=".apsw-search-wrap .apsw-search-form{ background:".esc_attr( $this->option_color['search_bar_bg'] )."; border-color:".esc_attr( $this->option_color['search_bar_border'] )."; }";
		
		$custom_css .=".apsw-search-wrap .apsw-category-items,.apsw-search-wrap .apsw-search-form input[type='search']{color:".esc_attr( $this->option_color['search_bar_text'] )."; }";
		
		$custom_css .=".apsw-search-wrap.apsw_search_form_style_4 button.apsw-search-btn, .apsw-search-wrap.apsw_search_form_style_5 button.apsw-search-btn, .apsw-search-wrap.apsw_search_form_style_6 button.apsw-search-btn{ color:".esc_attr( $this->option_color['search_btn_text'] )."; background:".esc_attr( $this->option_color['search_btn_bg'] )."; }";
		
		$custom_css .=".apsw-search-wrap .apsw-search-btn svg{ fill:".esc_attr( $this->option_color['search_btn_bg'] )."; }";
		
		$custom_css .=".apsw-search-wrap.apsw_search_form_style_4 button.apsw-search-btn::before, .apsw-search-wrap.apsw_search_form_style_5 button.apsw-search-btn::before, .apsw-search-wrap.apsw_search_form_style_6 button.apsw-search-btn::before { border-color: transparent ".esc_attr( $this->option_color['search_btn_bg'] )."  transparent;; }";
		
		$custom_css .=".apsw_ajax_result .apsw_result_wrap{ background:".esc_attr( $this->option_color['results_con_bg'] )."; border-color:".esc_attr( $this->option_color['results_con_bor'] )."; } ";
		
		$custom_css .="ul.apsw_data_container li:hover{ background:".esc_attr( $this->option_color['results_row_hover'] )."; border-color:".esc_attr( $this->option_color['results_con_bor'] )."; } ";
		$custom_css .="ul.apsw_data_container li .apsw-name{ color:".esc_attr( $this->option_color['results_heading_color'] ).";} ";
		$custom_css .="ul.apsw_data_container li .apsw-price{ color:".esc_attr( $this->option_color['price_color'] ).";} ";
		
		$custom_css .="ul.apsw_data_container li .apsw_result_excerpt{ color:".esc_attr( $this->option_color['results_text_color'] ).";} ";
		$custom_css .="ul.apsw_data_container li .apsw_result_category{ color:".esc_attr( $this->option_color['category_color'] ).";} ";
		$custom_css .="ul.apsw_data_container li.apsw_featured{ background:".esc_attr( $this->option_color['featured_product_bg'] ).";} ";
		$custom_css .="ul.apsw_data_container li .apsw_result_on_sale{ background:".esc_attr( $this->option_color['on_sale_bg'] ).";} ";
		$custom_css .="ul.apsw_data_container li .apsw_result_stock{ color:".esc_attr( $this->option_color['results_stock_color'] ).";} ";
		
		//$this->option_color	
        wp_add_inline_style( 'apsw-styles', $custom_css );

        wp_enqueue_script('jquery');
        wp_enqueue_script('apsw-plugins-scripts', plugins_url( 'assets/front/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
        wp_localize_script('apsw-plugins-scripts', 'apsw_localize', $this->apsw_get_localize_script() );
    }

    function apsw_load_scripts() {

		
        add_action( 'admin_enqueue_scripts', array( $this, 'apsw_admin_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'apsw_front_scripts' ) );
    }

    function apsw_load_defines(){

        $this->define('APSW_PLUGIN_URL',WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
        $this->define('APSW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        $this->define('APSW_PLUGIN_FILE', __FILE__ );
        $this->define('APSW_PLUGIN_VERSION', '1.0.0' );
        $this->define('APSW_PLUGIN_FILE', plugin_basename( __FILE__ ) );
        $this->define('APSW', 'advanced-product-search-for-woo' );
    }

    private function define( $name, $value ){
        if( ! defined( $name ) ) define( $name, $value );
    }

    private function apsw_get_localize_script(){

    	return apply_filters( 'apsw_localize_filters_', array(
    		'ajaxurl' => admin_url( 'admin-ajax.php'),
			'view_text'	=> esc_html( $this->option_search_results['view_all_text'] ),
		    'text' => array(
		    	'working' => esc_html__('Working...', 'apsw-lang'),
		    ),
	    ) );
		
		
    }
	
	public function apsw_load_textdomain( $locale = null ) {
		global $l10n;

		$domain = 'apsw-lang';

		if ( ( is_admin() ? get_user_locale() : get_locale() ) === $locale ) {
			$locale = null;
		}

		if ( empty( $locale ) ) {
			if ( is_textdomain_loaded( $domain ) ) {
				return true;
			} else {
				return load_plugin_textdomain( $domain, false, $domain . '/languages' );
			}
		} else {
			$mo_orig = $l10n[$domain];
			unapsw_load_textdomain( $domain );
	
			$mofile = $domain . '-' . $locale . '.mo';
			$path = WP_PLUGIN_DIR . '/' . $domain . '/languages';
	
			if ( $loaded = apsw_load_textdomain( $domain, $path . '/'. $mofile ) ) {
				return $loaded;
			} else {
				$mofile = WP_LANG_DIR . '/plugins/' . $mofile;
				return apsw_load_textdomain( $domain, $mofile );
			}
	
			$l10n[$domain] = $mo_orig;
		}

		return false;
	}
	public function go_pro( $actions, $file ) {
		if ( $file == plugin_basename( __FILE__ )) {
			
			$actions['apsw_go_pro'] = '<a href="https://athemeart.com/downloads/advanced-product-search-for-woo/" target="_blank" style="color: red; font-weight: bold">Go Pro!</a>';
			$action = $actions['apsw_go_pro'];
			unset( $actions['apsw_go_pro'] );
			array_unshift( $actions, $action );
			
			$actions['apsw_go_settings'] = '<a href="' . esc_url( admin_url( 'admin.php?page=advanced-product-search-for-woo' ) ) . '">'.__( 'Settings', 'apsw-lang' ) .'</a>';
			$action = $actions['apsw_go_settings'];
			unset( $actions['apsw_go_settings'] );
			array_unshift( $actions, $action );
		}
		return $actions;
	}
	
	public function sample_admin_notice__success() {
		if( get_option( 'apsw_notice_dismiss' ) != "" ){ return false;}
	?>
	<div class="notice notice-success apsw-notice-nux is-dismissible">
    	
		<p><?php _e( 'Thank you for installing Advanced Product Search For WooCommerce', 'apsw-lang' ); ?>
        	<a href="<?php echo esc_url( admin_url( 'admin.php?page=advanced-product-search-for-woo' ) ); ?>"><?php _e( 'Get started ', 'apsw-lang' ); ?></a>
        </p> 
	</div>
	<?php
		
	}
	
	
	public function dismiss_nux() {
			$nonce = !empty( $_POST[ 'nonce' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : false;
			
			if ( !$nonce || !wp_verify_nonce( $nonce, 'apsw_notice_dismiss_nonce' ) || !current_user_can( 'manage_options' ) ) {
				die();
			}
			update_option( 'apsw_notice_dismiss', true );
			die();
	}

}

global $apsw_product_search_final_class;
if ( ! $apsw_product_search_final_class ) {
	$apsw_product_search_final_class = APSW_Product_Search_Finale_Class::getInstance();
}

