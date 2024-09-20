jQuery(document).ready(function(){
//redirect
//above-header
jQuery( '.focus-customizer-menu-redirect-col1,.focus-customizer-menu-redirect-col2,.focus-customizer-menu-redirect-col3' ).on( 'click', function (e){
            e.preventDefault();
            wp.customize.panel('nav_menus').focus();
} );
jQuery( '.focus-customizer-widget-redirect-col1,.focus-customizer-widget-redirect-col2,.focus-customizer-widget-redirect-col3,.focus-customizer-widget-redirect' ).on( 'click', function (e){
            e.preventDefault();
            wp.customize.panel( 'widgets' ).focus();
} );
jQuery( '.focus-customizer-social_media-redirect-col1,.focus-customizer-social_media-redirect-col2,.focus-customizer-social_media-redirect-col3' ).on( 'click', function (e){
            e.preventDefault();
            wp.customize.section( 'amaz-store-social-icon' ).focus();
} ); 


jQuery('input[id=amaz_store_main_header_layout-mhdrdefault],input[id=amaz_store_main_header_layout-mhdrone],input[id=amaz_store_main_header_layout-mhdrtwo],input[id=amaz_store_main_header_layout-mhdrfour]').attr("disabled",true);
jQuery('input[id=amaz_store_top_slide_layout-slide-layout-2],input[id=amaz_store_top_slide_layout-slide-layout-3],input[id=amaz_store_top_slide_layout-slide-layout-4],input[id=amaz_store_top_slide_layout-slide-layout-6],input[id=amaz_store_top_slide_layout-slide-layout-9]').attr("disabled",true);
jQuery('input[id=amaz_store_cat_slide_layout-cat-layout-2],input[id=amaz_store_cat_slide_layout-cat-layout-1]').attr("disabled",true);
jQuery('input[id=amaz_store_banner_layout-bnr-four],input[id=amaz_store_banner_layout-bnr-three],input[id=amaz_store_banner_layout-bnr-five],input[id=amaz_store_banner_layout-bnr-six]').attr("disabled",true);
jQuery('input[id=amaz_store_bottom_footer_widget_layout-ft-wgt-one],input[id=amaz_store_bottom_footer_widget_layout-ft-wgt-two],input[id=amaz_store_bottom_footer_widget_layout-ft-wgt-three],input[id=amaz_store_bottom_footer_widget_layout-ft-wgt-five],input[id=amaz_store_bottom_footer_widget_layout-ft-wgt-six],input[id=amaz_store_bottom_footer_widget_layout-ft-wgt-seven],input[id=amaz_store_bottom_footer_widget_layout-ft-wgt-eight]').attr("disabled",true);
jQuery('#_customize-input-amaz_store_pagination option[value="click"],#_customize-input-amaz_store_pagination option[value="scroll"],#_customize-input-amaz_store_blog_post_pagination option[value="scroll"],#_customize-input-amaz_store_blog_post_pagination option[value="click"],#customize-control-amaz_store_main_header_option option[value="button"],#customize-control-amaz_store_main_header_option option[value="widget"],#customize-control-amaz_store_main_header_option option[value="text"],#_customize-input-amaz_store_woo_product_animation option[value="swap"],#_customize-input-amaz_store_woo_product_animation option[value="slide"],#customize-control-amaz_store_above_header_col3_set option[value="callto"],#customize-control-amaz_store_above_header_col3_set option[value="button"],#customize-control-amaz_store_above_header_col3_set option[value="social"],#customize-control-amaz_store_above_header_col3_set option[value="widget"],#_customize-input-amaz_store_inner_above_header_select option[value="innerpageslider"]').attr("disabled", true);
jQuery('input[data-customize-setting-link="amaz_store_sticky_header"]').attr("disabled", true);
// jQuery('#accordion-section-amaz-typography-pro-show').css("pointer-events", "none");
jQuery('input[id=amaz_store_above_footer_layout-ft-abv-three],input[id=amaz_store_bottom_footer_layout-ft-btm-three],input[id=amaz_store_ribbon_backgroundvideo],#customize-control-amaz_store_ribbon_margin input,#customize-control-amaz_store_ribbon_bg_background_image button').attr("disabled", true);
/* === Checkbox Multiple Control === */
    jQuery( '.customize-control-checkbox-multiple input[type="checkbox"]' ).on(
        'change',
        function() {
   // alert('');
            checkbox_values = jQuery( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
                function() {
                    return this.value;
                }
            ).get().join( ',' );

            jQuery( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );
        }
    );

// section sorting
      jQuery( "#sortable" ).sortable({
        placeholder: "ui-sortable-placeholder", 
        cursor: 'move',
        opacity: 0.65,
        stop: function ( event, ui){
        var data = jQuery(this).sortable('toArray');
            //  console.log(data); // This should print array of IDs, but returns empty string/array      
            jQuery( this ).parents( '.customize-control').find( 'input[type="hidden"]' ).val( data ).trigger( 'change' );
        }
    });


 //hide show option
 wp.customize('amaz_store_top_slide_layout', function( value ) {
        var filter_type = value.bind( function( to ) {
        if(to=='slide-layout-1'){
            jQuery( '.customizer-repeater-logo-image-control' ).css('display','block' ); 
            }else{
             jQuery( '.customizer-repeater-logo-image-control' ).css('display','none' );     
            }
        } );
        if(filter_type()=='slide-layout-1'){
              jQuery( '.customizer-repeater-logo-image-control' ).css('display','block' );
                
            }  else{
             jQuery( '.customizer-repeater-logo-image-control' ).css('display','none' );     
            }

    } );     

});