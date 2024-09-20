<?php
/**
 * WordPress settings API demo class - 
 *
 * @Author        aThemeArt
 * @Copyright:    2019 aThemeArt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

if ( !class_exists('APSW_Settings_API' ) ):
class APSW_Settings_API {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
       
		add_submenu_page( 'woocommerce', 'Advanced Product Search', 'Advanced Product Search', 'manage_options', 'advanced-product-search-for-woo', array($this, 'plugin_page') ); 
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'apsw_search_form',
                'title' => esc_html__( 'Search Form', 'apsw-lang' )
            ),
            array(
                'id'    => 'apsw_search_results',
                'title' => esc_html__( 'Search Results', 'apsw-lang' )
            ),
			array(
                'id'    => 'apsw_color_scheme',
                'title' => esc_html__( 'Styling Options', 'apsw-lang' )
            ),
			
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
		$default = apsw_default_theme_options();
		
		$form 		= ( isset( $default['apsw_search_form'] ) ) ? wp_parse_args(  $default['apsw_search_form'] ) : array();
		$result 	= ( isset( $default['apsw_search_results'] ) ) ? wp_parse_args( $default['apsw_search_results'] ) : array();
		$color	 	= ( isset( $default['apsw_color_scheme'] ) ) ? wp_parse_args(  $default['apsw_color_scheme'] ) : array();
		
		
        $settings_fields = array(
            'apsw_search_form' => array(
			
                array(
                    'name'              => 'search_value',
                    'label'             => esc_attr__( 'Text for search field', 'apsw-lang' ),
                    'desc'              => esc_html__( 'Text for search field placeholder.', 'apsw-lang' ),
                    'placeholder'       => esc_attr( $form['search_value'] ),
                    'type'              => 'text',
                    'default'           => esc_attr( $form['search_value'] ),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'              => 'search_btn',
                    'label'             => esc_attr__( 'Text for search button', 'apsw-lang' ),
                    'desc'              => esc_html__( 'Text for search button text.', 'apsw-lang' ),
                    'placeholder'       => esc_attr( $form['search_btn'] ),
                    'type'              => 'text',
                    'default'           => esc_attr( $form['search_btn'] ),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'              => 'search_bar_width',
                    'label'             => esc_attr__( 'Search bar width', 'apsw-lang' ),
                    'desc'              => esc_html__( 'maximum width of search bar.', 'apsw-lang' ),
                    'placeholder'       => esc_attr( $form['search_bar_width'] ),
                    'min'               => 1,
                    'step'              => '1',
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval',
					'default'           => esc_attr( $form['search_bar_width'] ),
                ),
				
				array(
                    'name'              => 'search_bar_height',
                    'label'             => esc_attr__( 'Search bar height', 'apsw-lang' ),
                    'desc'              => esc_html__( 'maximum height of search bar.', 'apsw-lang' ),
                    'placeholder'       => esc_attr( $form['search_bar_height'] ),
					'default'           => esc_attr( $form['search_bar_height'] ),
                    'min'               => 20,
                    'step'              => '1',
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval'
                ),
				
                array(
                    'name'              => 'action_charaters',
                    'label'             => esc_attr__( 'Minimum number of characters', 'apsw-lang' ),
                    'desc'              => esc_html__( 'Minimum number of characters required to run ajax search.', 'apsw-lang' ),
                    'placeholder'       => esc_attr( $form['action_charaters'] ),
					'default'           => esc_attr( $form['action_charaters'] ),
                    'min'               => 1,
                    'step'              => '1',
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval'
                ),
				
				 array(
                    'name'    => 'show_loader',
                    'label'   => esc_attr__( 'Show loader', 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show loader animation while searching. ', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default' => esc_attr( $form['show_loader'] ),
					 'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'search_action',
                    'label'   => esc_attr__( "Search Actions", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show link to search results page at the bottom of search results block. ', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'both' 		=> esc_html__( "Both ajax search results and search results page", 'apsw-lang' ),
                        'ajax'  	=> esc_html__( "Only ajax search results ( no search results page )", 'apsw-lang' ),
						'simple'  	=> esc_html__( "Only search results page ( no ajax search results )", 'apsw-lang' ),
                    ),
					'default'       => esc_attr( $form['search_action'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'search_form_style',
                    'label'   => esc_attr__( "Search Bar Style", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show link to search results page at the bottom of search results block. ', 'apsw-lang' ),
                    'type'    => 'radio_img',
                    'options' => array(
                        'apsw_search_form_style_1' 	=> esc_url( APSW_PLUGIN_URL ).'/assets/images/search_form_style_1.png',
                        'apsw_search_form_style_2' 	=> esc_url( APSW_PLUGIN_URL ).'/assets/images/search_form_style_2.png',
						'apsw_search_form_style_3' 	=> esc_url( APSW_PLUGIN_URL ).'/assets/images/search_form_style_3.png',
						'apsw_search_form_style_4' 	=> esc_url( APSW_PLUGIN_URL ).'/assets/images/search_form_style_4.png',
						'apsw_search_form_style_5' 	=> esc_url( APSW_PLUGIN_URL ).'/assets/images/search_form_style_4.png',
						'apsw_search_form_style_6' 	=> esc_url( APSW_PLUGIN_URL ).'/assets/images/search_form_style_6.png',
						
                    ),
					'default'       => esc_attr( $form['search_form_style'] ),
					 'sanitize_callback' => 'sanitize_text_field'
                ),
				
				
				array(
                    'name'    => 'how_to_use',
                    'label'   => esc_attr__( "HOW TO USE Search Bar", 'apsw-lang' ),
                    'desc'    => esc_html__( 'You can use as widgets, you will find inside widgets areas or you can use the shortcode [apsw_search_bar_preview]', 'apsw-lang' ),
                    'type'    => 'html',
                   
                ),
				
				
				
            ),
            'apsw_search_results' => array(
			
			
				 array(
                    'name'    => 'content_source',
                    'label'   => esc_attr__( "Description source", 'apsw-lang' ),
                    'desc'    => esc_html__( 'From where to take product description.If first source is empty data will be taken from other sources. ', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'content' => 'Content',
                        'excerpt'  => 'Excerpt'
                    ),
					'default'       => esc_attr( $result['content_source'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'              => 'length',
                    'label'             => esc_attr__( 'Content length', 'apsw-lang' ),
                    'desc'              => esc_html__( 'Maximal allowed number of words for product description.', 'apsw-lang' ),
                   	'default'       => esc_attr( $result['length'] ),
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval'
                ),
				
				array(
                    'name'              => 'number_of_product',
                    'label'             => esc_attr__( 'Number of product', 'apsw-lang' ),
                    'desc'              => esc_html__( 'Maximum number of displayed search results. ', 'apsw-lang' ),
                     'default'      	=> esc_attr( $result['number_of_product'] ),
                    'type'              => 'numberdisable',
                    'sanitize_callback' => 'floatval'
                ),
				
				array(
                    'name'              => 'nothing_found',
                    'label'             => esc_attr__( 'Nothing found text', 'apsw-lang' ),
                    'desc'              => esc_html__( 'Text when there is no product found search results. .', 'apsw-lang' ),
                    'default'     	 	=> esc_attr( $result['nothing_found'] ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'              => 'nothing_found_cat',
                    'label'             => esc_attr__( 'Nothing found text with category search', 'apsw-lang' ),
                    'desc'              => esc_html__( 'Text when there is no product found search results. .', 'apsw-lang' ),
                    'type'              => 'text',
                    'default'     	 	=> esc_attr( $result['nothing_found_cat'] ),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'              => 'view_all_text',
                    'label'             => esc_attr__( 'View All Text', 'apsw-lang' ),
                    'desc'              => esc_html__( 'leave empty to hide the button.', 'apsw-lang' ),
                    'default'     	 	=> esc_attr( $result['nothing_found_cat'] ),
                    'type'              => 'text',
                    
                    'sanitize_callback' => 'view_all_text'
                ),
				
				array(
                    'name'              => 'divider',
                    'type'              => 'divider',
					'desc'              => esc_html__( 'More Settings for search results. ', 'apsw-lang' ),
                ),
				
				
               array(
                    'name'    => 'show_image',
                    'label'   => esc_attr__( "Show Product image", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show product image for each search result.', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['show_image'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'show_description',
                    'label'   => esc_attr__( "Show Product Description", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show product description text. ', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['show_description'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'show_price',
                    'label'   => esc_attr__( "Show price", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show product price for each search result.', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['show_price'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'show_rating',
                    'label'   => esc_attr__( "Show Rating", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show product Rating for each search result.', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['show_rating'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'show_category',
                    'label'   => esc_attr__( "Show product category", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show product category for each search result.', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['show_category'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'show_sku',
                    'label'   => esc_attr__( "Show product sku", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show product sku for each search result.', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['show_sku'] ),
                ),
				
				array(
                    'name'    => 'stock_status',
                    'label'   => esc_attr__( "Show stock status", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show product price for stock status products.', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['stock_status'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'show_feature',
                    'label'   => esc_attr__( "Active Feature ", 'apsw-lang' ),
                    'desc'    => esc_html__( 'will active green color each Feature product.', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['show_feature'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'show_on_sale',
                    'label'   => esc_attr__( "Show On Sale", 'apsw-lang' ),
                    'desc'    => esc_html__( 'Show product On Sale status products.', 'apsw-lang' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    ),
					'default'     	 	=> esc_attr( $result['show_on_sale'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				
				
            ),
			'apsw_color_scheme' => array(
				
				array(
                    'name'    => 'search_bar_bg',
                    'label'   => esc_attr__( 'Search Bar background', 'wedevs' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['search_bar_bg'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'search_bar_border',
                    'label'   => esc_attr__( 'Search Bar border', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['search_bar_border'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'    => 'search_bar_text',
                    'label'   => esc_attr__( 'Search Bar Text', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['search_bar_text'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'    => 'search_btn_bg',
                    'label'   => esc_attr__( 'Search button background', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['search_btn_bg'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'search_btn_text',
                    'label'   => esc_attr__( 'Search button color', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['search_btn_text'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'              => 'divider',
                    'type'              => 'divider',
					'desc'              => esc_html__( 'Settings for search search results. ', 'apsw-lang' ),
                ),
				
				array(
                    'name'    => 'results_con_bg',
                    'label'   => esc_attr__( 'Results Container background', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['results_con_bg'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'results_con_bor',
                    'label'   => esc_attr__( 'Results Container border', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['results_con_bor'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'results_row_hover',
                    'label'   => esc_attr__( 'Results each row hover background', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['results_row_hover'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'results_heading_color',
                    'label'   => esc_attr__( 'Results Title Color', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['results_heading_color'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'    => 'price_color',
                    'label'   => esc_attr__( 'Price Color', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['results_text_color'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'    => 'results_text_color',
                    'label'   => esc_attr__( 'Results Text Color', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['results_text_color'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'category_color',
                    'label'   => esc_attr__( 'Results Category Color', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['category_color'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'results_stock_color',
                    'label'   => esc_attr__( 'Results Stock Color', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['results_stock_color'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'    => 'on_sale_bg',
                    'label'   => esc_attr__( 'On Sale background', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['on_sale_bg'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				array(
                    'name'    => 'featured_product_bg',
                    'label'   => esc_attr__( 'Featured Product background', 'apsw-lang' ),
                    'desc'    => esc_html__( 'The plugins comes with unlimited color schemes for your theme\'s styling.', 'apsw-lang' ),
                    'type'    => 'color',
                    'default' => esc_attr( $color['featured_product_bg'] ),
					'sanitize_callback' => 'sanitize_text_field'
                ),
				
				
				
				
			),
			
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap apsw_settings_wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
new APSW_Settings_API();
endif;
