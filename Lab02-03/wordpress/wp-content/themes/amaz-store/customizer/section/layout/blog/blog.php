<?php
/**
 *Blog Option
 /*******************/
//blog post content
/*******************/
    $wp_customize->add_setting('amaz_store_blog_post_content', array(
        'default'        => 'excerpt',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'esc_attr',
    ));
    $wp_customize->add_control('amaz_store_blog_post_content', array(
        'settings' => 'amaz_store_blog_post_content',
        'label'   => __('Blog Post Content','amaz-store'),
        'section' => 'amaz-store-section-blog-group',
        'type'    => 'select',
        'choices'    => array(
        'full'   => __('Full Content','amaz-store'),
        'excerpt' => __('Excerpt Content','amaz-store'), 
        'nocontent' => __('No Content','amaz-store'), 
        ),
         'priority'   =>9,
    ));
    // excerpt length
    $wp_customize->add_setting('amaz_store_blog_expt_length', array(
			'default'           =>'30',
            'capability'        => 'edit_theme_options',
			'sanitize_callback' =>'amaz_store_sanitize_number',
		)
	);
	$wp_customize->add_control('amaz_store_blog_expt_length', array(
			'type'        => 'number',
			'section'     => 'amaz-store-section-blog-group',
			'label'       => __( 'Excerpt Length', 'amaz-store' ),
			'input_attrs' => array(
				'min'  => 0,
				'step' => 1,
				'max'  => 3000,
			),
             'priority'   =>10,
		)
	);
	// read more text
    $wp_customize->add_setting('amaz_store_blog_read_more_txt', array(
			'default'           =>'Read More',
            'capability'        => 'edit_theme_options',
			'sanitize_callback' =>'amaz_store_sanitize_text',
            'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control('amaz_store_blog_read_more_txt', array(
			'type'        => 'text',
			'section'     => 'amaz-store-section-blog-group',
			'label'       => __( 'Read More Text', 'amaz-store' ),
			'settings' => 'amaz_store_blog_read_more_txt',
             'priority'   =>11,
			
		)
	);
    /*********************/
    //blog post pagination
    /*********************/
   $wp_customize->add_setting('amaz_store_blog_post_pagination', array(
        'default'        => 'num',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'esc_attr',
    ));
    $wp_customize->add_control('amaz_store_blog_post_pagination', array(
        'settings' => 'amaz_store_blog_post_pagination',
        'label'   => __('Post Pagination','amaz-store'),
        'section' => 'amaz-store-section-blog-group',
        'type'    => 'select',
        'choices' => array(
        'num'     => __('Numbered','amaz-store'),
        'click'   => __('Load More (Pro)','amaz-store'), 
        'scroll'  => __('Infinite Scroll (Pro)','amaz-store'), 
        ),
        'priority'   =>13,
    ));
    $wp_customize->add_setting( 'amaz_store_blog_single_sidebar_disable', array(
                'default'               => false,
                'sanitize_callback'     => 'amaz_store_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'amaz_store_blog_single_sidebar_disable', array(
                'label'                 => esc_html__('Force to disable sidebar in single page.', 'amaz-store'),
                'type'                  => 'checkbox',
                'section'               => 'amaz-store-section-blog-group',
                'settings'              => 'amaz_store_blog_single_sidebar_disable',
                'priority'   => 14,
            ) ) );
/****************/
//blog doc link
/****************/
$wp_customize->add_setting('amaz_store_blog_arch_learn_more', array(
    'sanitize_callback' => 'amaz_store_sanitize_text',
    ));
$wp_customize->add_control(new amaz_store_Misc_Control( $wp_customize, 'amaz_store_blog_arch_learn_more',
            array(
        'section'    => 'amaz-store-section-blog-group',
        'type'      => 'doc-link',
        'url'       => 'https://themehunk.com/docs/amaz-store/#blog-setting',
        'description' => esc_html__( 'To know more go with this', 'amaz-store' ),
        'priority'   =>100,
    )));