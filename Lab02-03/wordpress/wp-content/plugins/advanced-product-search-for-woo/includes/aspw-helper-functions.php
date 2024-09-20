<?php
/**
 * ASPW Common function - 
 *
 * @Author        aThemeArt
 * @Copyright:    2019 aThemeArt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

function aspw_alowed_tags() {
	
	$wp_post_allow_tag = wp_kses_allowed_html( 'post' );
	
	$allowed_tags = array(
		'a' => array(
			'class' => array(),
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
		),
		'abbr' => array(
			'title' => array(),
		),
		'b' => array(),
		'blockquote' => array(
			'cite'  => array(),
		),
		'cite' => array(
			'title' => array(),
		),
		'code' => array(),
		'del' => array(
			'datetime' => array(),
			'title' => array(),
		),
		'dd' => array(),
		'div' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
			'id' => array(),
		),
		'dl' => array(),
		'dt' => array(),
		'em' => array(),
		'h1' => array(),
		'h2' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
			'id' => array(),
		),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'i' => array(),
		'img' => array(
			'alt'    => array(),
			'class'  => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
		'li' => array(
			'class' => array(),
		),
		'ol' => array(
			'class' => array(),
		),
		'p' => array(
			'class' => array(),
		),
		'q' => array(
			'cite' => array(),
			'title' => array(),
		),
		'span' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'strike' => array(),
		'strong' => array(),
		'ul' => array(
			'class' => array(),
		),
		'fieldset' => array(),
		
		
		'label' => array(
			'for' => array(),
			'class' => array(),
			'style' => array(),
		),
		'form' => array(
			'role' => array(),
			'class' => array(),
			'autocomplete' => array(),
			'action' => array(),
			'method' => array(),
		),
		'input' => array(
			'type' => array(),
			'id' => array(),
			'name' => array(),
			'class' => array(),
			'value' => array(),
			'placeholder' => array(),
			'data-charaters' => array(),
			'data-functiontype' => array(),
			'min' => array(),
			'max' => array(),
			'step' => array(),
			'checked' => array(),
		),
		
		'textarea' => array(
			'type' => array(),
			'id' => array(),
			'name' => array(),
			'class' => array(),
			'value' => array(),
			'placeholder' => array(),
			'rows' => array(),
			'cols' => array(),
			
		),
		'select' => array(
			'class' => array(),
			'id' => array(),
			'name' => array(),
			'value' => array(),
			'placeholder' => array(),
		),
		'option' => array(
			'value' => array(),
			'data-value' => array(),
			'selected' => array(),
			
		),
		'button' => array(
			'type' => array(),
			'id' => array(),
			'name' => array(),
			'class' => array(),
			'value' => array(),
			'placeholder' => array(),
		),
		
		'img' => array(
			'class' => array(),
			'src' => array(),
			'name' => array(),
			'class' => array(),
			'value' => array(),
			'placeholder' => array(),
		),
		
		'svg' => array(
			'version' => array(),
			'id' => array(),
			'xmlns' => array(),
			'xmlns:xlink' => array(),
			'x' => array(),
			'y' => array(),
			'width' => array(),
			'height' => array(),
			'viewbox' => array(),
			'style' => array(),
			'xml:space' => array(),
		),
		
		'g' => array(),
		'path' => array(
			'd' => array(),
			'id' => array(),
			'xmlns' => array(),
			'xmlns:xlink' => array(),
			'x' => array(),
			'y' => array(),
			'width' => array(),
			'height' => array(),
			'viewBox' => array(),
			'style' => array(),
			'xml:space' => array(),
		),
		
	);

	
	$tags = array_merge($wp_post_allow_tag, $allowed_tags);

	return apply_filters( 'aspw_alowed_tags', $tags );
	
}


if ( ! function_exists( 'apsw_get_option' ) ) :

	/**
	 * Get theme option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function apsw_get_option( $key ) {

		if ( empty( $key ) ) {
			return;
		}
		$value = '';
		$default = apsw_default_theme_options(  );
		$default_value = null;

		if ( is_array( $default ) && isset( $default[ $key ] ) ) {
			$default_value = $default[ $key ];
		}

		if ( null !== $default_value ) {
			$value = get_option( $key, $default_value );
		}
		else {
			$value = get_option( $key );
		}
		
		if( is_array( $value ) && !empty( $value ) ){
			$value = array_merge( $default[ $key ],$value);
		}else{
			$value = $default[ $key ];
		}
		
	
		return apply_filters( 'apsw_get_option', wp_parse_args ( $value ) );
	}

endif;



if ( ! function_exists( 'apsw_default_theme_options' ) ) :
	/**
	 * Get theme option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function apsw_default_theme_options( ) {

		$defaults = array();
		
		/*Global Layout*/
		$defaults['apsw_search_form']  =  array(
				'search_value'			=> esc_html__( 'Search here...', 'apsw-lang' ),
				'search_btn' 			=> esc_html__( 'Search ', 'apsw-lang' ),
				'search_bar_width'		=> 999,
				'search_bar_height'		=> 56,
				'action_charaters'		=> 1,
				'show_loader'			=> 'yes',
				'search_action' 		=> 'both',
				'search_form_style'		=> 'apsw_search_form_style_1',
				
				
		);
		$defaults['apsw_search_results']  =  array(
		
				'content_source' 		=> 'content',
				'length' 				=> 120,
				'number_of_product' 	=> 3,
				'nothing_found' 		=> esc_html__( 'No products were found !', 'apsw-lang' ),
				'nothing_found_cat' 	=> esc_html__( 'No products were found matching your selection category !', 'apsw-lang' ),
				'view_all_text' 		=> esc_html__( 'View All', 'apsw-lang' ),
				'show_image'			=> 'yes',
				'show_description'		=> 'yes',
				'show_price'			=> 'yes',
				'show_rating'			=> 'no',
				'show_category'			=> 'no',
				'stock_status'			=> 'no',
				'show_on_sale'			=> 'yes',
				'show_feature'			=> 'no',
				'show_add_to_cart'		=> 'yes',
				'show_sku'				=> 'yes',
				
		);
		
		$defaults['apsw_color_scheme']  =  array(
		
				'search_bar_bg' 		=> '#fff',
				'search_bar_border' 	=> '#e2e1e6',
				'search_bar_text' 		=> '#43454b',
				'search_btn_bg' 		=> '#d83c3c',
				'search_btn_text' 		=> '#fff',
				
				
				'results_con_bg' 		=> '#fff',
				'results_con_bor' 		=> '#e2e1e6',
				'results_row_hover' 	=> '#d9d9d9',
				'results_heading_color' => '#000',
				'price_color' 			=> '#000',
				'results_text_color' 	=> '#989499',
				'category_color' 		=> '#dd0000',
				'featured_product_bg' 	=> '#C7FBAE',
				'on_sale_bg' 			=> '#5391E8',
				'results_stock_color' 	=> '#dd0000',
				 
		);
		
	

		// Pass through filter.
		$defaults = apply_filters( 'apsw_default_theme_options', wp_parse_args ( $defaults ) );

		return $defaults;
		
	}

endif;
