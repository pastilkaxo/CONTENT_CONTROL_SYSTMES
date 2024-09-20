<?php

/**
 * Example widget using class
 * 
 * DEMO: Include this file in your theme functions.php file or plugin
 */
class APSW_Serach_Widget {
    
    /**
     * Unique widget id used for custom inline css
     * @var string
     */
    private $widget_id;
    
	/**
	 * Constructor
	 */
    public function __construct() {
        
        /**
         * Add widget via builder
         */
        $this->map_and_init();
    }
    
    /**
     * Render widget frontend view
     * 
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance The settings for the particular instance of the widget.
     * @param array $form_fields Widget admin form fields configuration array
     * @param string $this->id Widget generated unique id by instance number. 
     *                        Can be used to target this widget instance only
	 * @param string $widget_id Widget generated unique id by instance number. 
	 *                        Can be used to target this widget instance only
     */
    public function render_view( $args, $instance, $form_fields, $widget_id ) {
		
		/**
		 * Please note: 
		 * $instance will hold all admin form fields values
		 */
		
		// Unique widget id used for custom inline css
        $this->widget_id = $widget_id;
        
		// Set widget title
        $widget_title = isset( $instance['title'] ) ? $instance['title'] : '';
		$style		  = isset( $instance['search_bar_style'] ) ? $instance['search_bar_style'] : '';
        
        // before and after widget arguments are defined by themes
        echo wp_kses( $args['before_widget'] ,aspw_alowed_tags() );
		
        if ( ! empty( $widget_title ) ) {
			echo wp_kses( $args['before_title'] ,aspw_alowed_tags() );
			echo  esc_html( $widget_title );
			echo wp_kses( $args['after_title'] ,aspw_alowed_tags() );
          
        }

		do_action('apsw_search_bar_preview', absint( $style ) );
		
		
		// before and after widget arguments are defined by themes
      
		echo wp_kses( $args['after_widget'] ,aspw_alowed_tags() );
    
        
    }
    
    
    /**
     * Map and init blog posts widget widget
     */
    public function map_and_init() {

        $config = array(

            // Core configuration
			
			/**
             * Unique widget id
             * @var string (required)
             */
            'base_id' => 'aspw-widgets-wrap',
			
			/**
             * Widget name
             * @var string (required)
             */
            'name' => esc_html__('+ Advanced Product Search', 'apsw-lang'),
			
			/**
             * Widger callback function to render frontend html
             * 
             * If use class callback, the class instance must be used
             * If you use array('MyClassName', 'method') than __autoload will not fire properly when
             * a not-yet-loaded class was invoked through a PHP command
			 * 
             * @var string|array String if function name is passed, if using class method than it will be array (required)
             */
            'callback' => array( $this, 'render_view' ),
			
			/**
             * Widget Options
             * Option array passed to wp_register_sidebar_widget() using $options.
             * @see https://codex.wordpress.org/Function_Reference/wp_register_sidebar_widget
             * @var array|string (optional)
             */
            'widget_ops' => array(
                'classname' => 'aspw-widgets-wrap-class',
                'description' => esc_html__( 'Advanced Product Search – powerful live search plugin for WooCommerce', 'apsw-lang' ),
                'customize_selective_refresh' => false,
            ),
			
			/**
             * Width and height of the widget
             * Option array passed to wp_register_widget_control() using $options.
             * @see https://codex.wordpress.org/Function_Reference/wp_register_widget_control
             * @var array|string (optional)
             */
            'control_ops' => array( 
                'width' => 400, 
                'height' => 350 
            ),

			/**
             * Admin widget form section html element.
             * Example: <p>, ,<section>, <p> 
             * Can not have value of <div>
             * @var string (optional)
             */
            'section_opening_tag' => '<p>',
            /**
             * Admin widget form section html element.
             * Example: <p>, ,<section>, <p> 
             * Can not have value of <div>
             * @var string (optional)
             */
            'section_closing_tag' => '</p>',
			
			/**
             * Field arguments
             * @see field reference for supported field types
             */
            'form_fields' => array(
				
				/**
				 * Please note:
				 * 
				 * array key is required. It should be unique string for widget admin form. 
				 * String may contain only lowercase letters and underlines
				 * It will be used as name and id.
				 * Also you will get values on frontend using this key
				 */
				
				'title' => array(
					'type' => 'text', // Required  // Input type: text, password, search, tel, button
					'label' => esc_html__( 'Title :', 'apsw-lang' ), // Optional
					'placeholder' => esc_html__( 'Advance Search ', 'apsw-lang' ), // Optional
					
				),
				
				'search_bar_style' => array(
					'type' => 'select', // Required
					'label' => esc_html__( 'Search Bar:', 'apsw-lang' ), // Optional
					'options' => array( // Required
						'1' => esc_html__( 'Style 1', 'apsw-lang' ),
						'2.0' => esc_html__( 'Style 2', 'apsw-lang' ),
						'3.0' => esc_html__( 'Style 3', 'apsw-lang' ),
						'4.0' => esc_html__( 'Style 4', 'apsw-lang' ),
						'5.0' => esc_html__( 'Style 5', 'apsw-lang' ),
						'6.0' => esc_html__( 'Style 6', 'apsw-lang' ),
					),
					//'options' => array( 'callable' => array( $class_instance, 'method_name' ) ), // instead you can use function not method
					'default' => '1' // Optional
				),
				
            )

        );
        
		// Init widget
        if ( function_exists( 'predic_widget' ) ) {
            predic_widget()->add_widget( $config );
        }
    }
}
new APSW_Serach_Widget();