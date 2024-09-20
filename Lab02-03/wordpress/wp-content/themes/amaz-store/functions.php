<?php
/**
 * Amaz Store functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Amaz Store
 * @since 1.0.0
 */
/**
 * Theme functions and definitions
 */
if ( ! function_exists( 'amaz_store_setup' ) ) :
define( 'AMAZ_STORE_THEME_VERSION','1.0.0');
define( 'AMAZ_STORE_THEME_DIR', get_template_directory() . '/' );
define( 'AMAZ_STORE_THEME_URI', get_template_directory_uri() . '/' );
define( 'AMAZ_STORE_THEME_SETTINGS', 'amaz-store-settings' );
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_amaz_store_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function amaz_store_setup(){
		/*
		 * Make theme available for translation.
		 */
		load_theme_textdomain( 'amaz-store', get_template_directory() . '/languages' );
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );
		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );
		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'woocommerce' );
	
		// Add support for Block Styles.
        add_theme_support( 'wp-block-styles' );

        // Add support for full and wide align images.
        add_theme_support( 'align-wide' );

        // Add support for editor styles.
        add_theme_support( 'editor-styles' );

        // Enqueue editor styles.
        add_editor_style( 'style-editor.css' );

		add_editor_style( 'editor.css' );

        // Add support for responsive embedded content.
        add_theme_support( 'responsive-embeds' );

		add_theme_support( 'custom-spacing' );
		
		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );
		/**
		 * Add support for core custom logo.
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
		// Add support for Custom Header.
		add_theme_support( 'custom-header', 

			apply_filters( 'amaz_store_custom_header_args', array(
				'default-image' => '',
				'flex-height'   => true,
				'header-text'   => false,
				'video'          => false,
		) 


		) );
		// Add support for Custom Background.
         $args = array(
	    'default-color' => 'f1f1f1',
        );
        add_theme_support( 'custom-background',$args );
        
        $GLOBALS['content_width'] = apply_filters( 'amaz_store_content_width', 640 );
        add_theme_support( 'woocommerce', array(
                                                 'thumbnail_image_width' => 320,
                                             ) );
         // Recommend plugins
        add_theme_support( 'recommend-plugins', array(

        	'themehunk-customizer' => array(
                'name' => esc_html__( 'Themehunk Customizer (Highly Recommended)', 'amaz-store' ),
                 'img' => 'icon-128x128.png',
                'active_filename' => 'themehunk-customizer/themehunk-customizer.php',
            ),
            'th-advance-product-search' => array(
            'name' => esc_html__( 'TH Advance Product Search', 'amaz-store' ),
            'img' => 'icon-128x128.gif',
            'active_filename' => 'th-advance-product-search/th-advance-product-search.php',
            ),
            'th-all-in-one-woo-cart' => array(
                 'name' => esc_html__( 'TH All In One Woo Cart', 'amaz-store' ),
                  'img' => 'icon-128x128.gif',
                 'active_filename' => 'th-all-in-one-woo-cart/th-all-in-one-woo-cart.php',
             ),
            'th-variation-swatches' => array(
                'name' => esc_html__( 'TH Variation Swatches', 'amaz-store' ),
                 'img' => 'icon-128x128.gif',
                'active_filename' => 'th-variation-swatches/th-variation-swatches.php',
            ),
            'lead-form-builder' => array(
                'name' => esc_html__( 'Lead Form Builder', 'amaz-store' ),
                 'img' => 'icon-128x128.png',
                'active_filename' => 'lead-form-builder/lead-form-builder.php',
            ),
            'wp-popup-builder' => array(
                'name' => esc_html__( 'WP Popup Builder – Popup Forms & Newsletter', 'amaz-store' ),
                 'img' => 'icon-128x128.png',
                'active_filename' => 'wp-popup-builder/wp-popup-builder.php',
            ), 
            'unlimited-blocks' => array(
                'name' => esc_html__( 'Unlimited blocks For Gutenberg', 'amaz-store' ),
                 'img' => 'icon-128x128.png',
                'active_filename' => 'unlimited-blocks/unlimited-blocks.php',
            ), 
            'woocommerce' => array(
                'name' => esc_html__( 'Woocommerce', 'amaz-store' ),
                 'img' => 'icon-128x128.gif',
                'active_filename' => 'woocommerce/woocommerce.php',
            ),
            'th-product-compare' => array(
                 'name' => esc_html__( 'TH Product Compare', 'amaz-store' ),
                  'img' => 'icon-128x128.gif',
                 'active_filename' => 'th-product-compare/th-product-compare.php',
             ),

            'yith-woocommerce-wishlist' => array(
                 'name' => esc_html__( 'YITH WooCommerce Wishlist', 'amaz-store' ),
                  'img' => 'icon-128x128.jpg',
                 'active_filename' => 'yith-woocommerce-wishlist/init.php',
             ),
            
            

        ) );

        // Import Data Content plugins
        add_theme_support( 'import-demo-content', array(
             'themehunk-customizer' => array(
                'name' => esc_html__( 'Themehunk Customizer', 'amaz-store' ),
                 'img' => 'icon-128x128.png',
                'active_filename' => 'themehunk-customizer/themehunk-customizer.php',
            ),

            'one-click-demo-import' => array(
                'name' => esc_html__( 'One Click Demo Import', 'amaz-store' ),
                'img' => 'icon-128x128.png',
                'active_filename' => 'one-click-demo-import/one-click-demo-import.php',
            ), 
            'woocommerce' => array(
                'name' => esc_html__( 'Woocommerce', 'amaz-store' ),
                'img' => 'icon-128x128.gif',
                'active_filename' => 'woocommerce/woocommerce.php',
            ),
            'th-advance-product-search' => array(
            'name' => esc_html__( 'TH Advance Product Search', 'amaz-store' ),
            'img' => 'icon-128x128.gif',
            'active_filename' => 'th-advance-product-search/th-advance-product-search.php',
            ),

            'th-all-in-one-woo-cart' => array(
                 'name' => esc_html__( 'TH All In One Woo Cart', 'amaz-store' ),
                  'img' => 'icon-128x128.gif',
                 'active_filename' => 'th-all-in-one-woo-cart/th-all-in-one-woo-cart.php',
             ),

        ));



        // Useful plugins
        add_theme_support( 'useful-plugins', array(
             'themehunk-megamenu-plus' => array(
                'name' => esc_html__( 'Megamenu plugin from Themehunk.', 'amaz-store' ),
                'active_filename' => 'themehunk-megamenu-plus/themehunk-megamenu.php',
            ),
        ) );

	}
endif;
add_action( 'after_setup_theme', 'amaz_store_setup' );
/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 */
/**
 * Register widget area.
 */
function amaz_store_widgets_init(){
	register_sidebar( array(
		'name'          => esc_html__( 'Primary Sidebar', 'amaz-store' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here to appear in your primary sidebar.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="amaz-store-widget-content">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(array(
		'name'          => esc_html__( 'Above Header First Widget', 'amaz-store' ),
		'id'            => 'top-header-widget-col1',
		'description'   => esc_html__( 'Add widgets here to appear in top header.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(array(
		'name'          => esc_html__( 'Above Header Second Widget', 'amaz-store' ),
		'id'            => 'top-header-widget-col2',
		'description'   => esc_html__( 'Add widgets here to appear in top header.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(array(
		'name'          => esc_html__( 'Above Header Third Widget', 'amaz-store' ),
		'id'            => 'top-header-widget-col3',
		'description'   => esc_html__( 'Add widgets here to appear in top header.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar(array(
		'name'          => esc_html__( 'Main Header Widget', 'amaz-store' ),
		'id'            => 'main-header-widget',
		'description'   => esc_html__( 'Add widgets here to appear in main header.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
    register_sidebar(array(
		'name'          => esc_html__( 'Footer Top First Widget', 'amaz-store' ),
		'id'            => 'footer-top-first',
		'description'   => esc_html__( 'Add widgets here to appear in top footer.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(array(
		'name'          => esc_html__( 'Footer Top Second Widget', 'amaz-store' ),
		'id'            => 'footer-top-second',
		'description'   => esc_html__( 'Add widgets here to appear in top footer.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(array(
		'name'          => esc_html__( 'Footer Top Third Widget', 'amaz-store' ),
		'id'            => 'footer-top-third',
		'description'   => esc_html__( 'Add widgets here to appear in top footer.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(array(
		'name'          => esc_html__( 'Footer Below First Widget', 'amaz-store' ),
		'id'            => 'footer-below-first',
		'description'   => esc_html__( 'Add widgets here to appear in top footer.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(array(
		'name'          => esc_html__( 'Footer Below Second Widget', 'amaz-store' ),
		'id'            => 'footer-below-second',
		'description'   => esc_html__( 'Add widgets here to appear in top footer.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(array(
		'name'          => esc_html__( 'Footer Below Third Widget', 'amaz-store' ),
		'id'            => 'footer-below-third',
		'description'   => esc_html__( 'Add widgets here to appear in top footer.', 'amaz-store' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	for ( $i = 1; $i <= 4; $i++ ){
		register_sidebar( array(
			'name'          => sprintf( esc_html__( 'Footer Widget Area %d', 'amaz-store' ), $i ),
			'id'            => 'footer-' . $i,
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
	
}
add_action( 'widgets_init', 'amaz_store_widgets_init' );
/**
 * Enqueue scripts and styles.
 */
function amaz_store_scripts(){
	// enqueue css
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_style( 'font-awesome', AMAZ_STORE_THEME_URI . 'third-party/fonts/font-awesome/css/font-awesome.css', '', AMAZ_STORE_THEME_VERSION );
	wp_enqueue_style( 'th-icon', AMAZ_STORE_THEME_URI . 'third-party/fonts/th-icon/style.css', '', AMAZ_STORE_THEME_VERSION );
	wp_enqueue_style( 'animate', AMAZ_STORE_THEME_URI . 'css/animate.css','',AMAZ_STORE_THEME_VERSION);
	wp_enqueue_style( 'owl.carousel-css', AMAZ_STORE_THEME_URI . 'css/owl.carousel.css','',AMAZ_STORE_THEME_VERSION);
	wp_enqueue_style( 'amaz-store-pro-menu', AMAZ_STORE_THEME_URI . 'css/amaz-store-menu.css','',AMAZ_STORE_THEME_VERSION);	
	wp_enqueue_style( 'amaz-store-main-style', AMAZ_STORE_THEME_URI . 'css/style.css','',AMAZ_STORE_THEME_VERSION);

	wp_enqueue_style( 'amaz-store-style', get_stylesheet_uri(), array(), AMAZ_STORE_THEME_VERSION );
	wp_add_inline_style('amaz-store-style', amaz_store_custom_style());
	
    //enqueue js
    wp_enqueue_script("jquery-effects-core",array( 'jquery' ));
    wp_enqueue_script( 'jquery-ui-autocomplete',array( 'jquery' ),'',true );
    wp_enqueue_script('imagesloaded');
    wp_enqueue_script('amaz-store-menu-js', AMAZ_STORE_THEME_URI .'js/amaz-store-menu.js', array( 'jquery' ), '1.0.0', true );
   
    wp_enqueue_script('owl.carousel-js', AMAZ_STORE_THEME_URI .'js/owl.carousel.js', array( 'jquery' ), '1.0.1', true );
   
    wp_enqueue_script('amaz-store-accordian-menu-js', AMAZ_STORE_THEME_URI .'js/amaz-store-accordian-menu.js', array( 'jquery' ), AMAZ_STORE_THEME_VERSION , true );

    wp_enqueue_script( 'amaz-store-custom-js', AMAZ_STORE_THEME_URI .'js/amaz-store-custom.js', array( 'jquery' ), AMAZ_STORE_THEME_VERSION , true );
     $amazstorelocalize = array(
     			'amaz_store_above_header_autoplay' => get_theme_mod('amaz_store_above_header_autoplay',false),
     			'amaz_store_inner_above_header_autoplay' => get_theme_mod('amaz_store_inner_above_header_autoplay',false),
				'amaz_store_top_slider_optn' => get_theme_mod('amaz_store_top_slider_optn',false),
				'amaz_store_move_to_top_optn' => get_theme_mod('amaz_store_move_to_top',false),
				'amaz_store_sticky_header_effect' => get_theme_mod('amaz_store_sticky_header_effect','scrldwmn'),
				'amaz_store_slider_speed' => get_theme_mod('amaz_store_slider_speed','3000'),
				'amaz_store_rtl' => (bool)get_theme_mod('amaz_store_rtl'),
				
			);
    wp_localize_script( 'amaz-store-custom-js', 'amaz_store',  $amazstorelocalize);
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ){
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'amaz_store_scripts' );
/********************************************************/
// Adding Dashicons in WordPress Front-end
/********************************************************/
add_action( 'wp_enqueue_scripts', 'amaz_store_load_dashicons_front_end' );
function amaz_store_load_dashicons_front_end(){
  wp_enqueue_style( 'dashicons' );
}

/**
 * Load init.
 */
require_once trailingslashit(AMAZ_STORE_THEME_DIR).'inc/init.php';



//custom function conditional check for blog page
function amaz_store_is_blog (){
    return ( is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag()) && 'post' == get_post_type();
}


if ( ! function_exists( 'wp_body_open' ) ) {

	/**
	 * Shim for wp_body_open, ensuring backward compatibility with versions of WordPress older than 5.2.
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}
//for shop page
remove_action('woocommerce_init','th_compare_add_action_shop_list');
//To disable th compare Pro button 
remove_action('woocommerce_init', 'tpcp_add_action_shop_list');