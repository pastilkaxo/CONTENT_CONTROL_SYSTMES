( function( $ ){
//**********************************/
// Slider settings
//**********************************/
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
		OPNCustomizerToggles['amaz_store_top_slide_layout'] = [
		    {
				controls: [    
				'amaz_store_top_slider_2_title',
				'amaz_store_lay2_adimg',
				'amaz_store_lay2_url',
				'amaz_store_lay2_adimg2',
				'amaz_store_lay2_url2',
				'amaz_store_top_slider_2_title2',
				'amaz_store_lay2_adimg3',
				'amaz_store_lay2_url3',
				'amaz_store_lay3_adimg',
				'amaz_store_lay3_url',
				'amaz_store_lay3_adimg3',
				'amaz_store_lay3_3url',
				'amaz_store_lay3_adimg2',
				'amaz_store_lay3_2url',
				'amaz_store_include_category_slider',
				'amaz_store_lay3_bg_img',
				'amaz_store_discount_offer_txt',
				'amaz_store_cat_url',
				'amaz_store_lay3_url3',
				'amaz_store_top_slider_3_title2',
				],
				callback: function(slideroptn){
					if(slideroptn =='slide-layout-1'){
					return false;
					}
					return true;
				}
			},	
			{
				controls: [    
				'amaz_store_top_slide_content',
				'amaz_store_top_slider_2_title',
				'amaz_store_lay2_adimg',
				'amaz_store_lay2_url',
				'amaz_store_lay2_adimg2',
				'amaz_store_lay2_url2',
				'amaz_store_top_slider_2_title2',
				'amaz_store_lay2_adimg3',
				'amaz_store_lay2_url3',
				'amaz_store_lay3_bg_img',
				],
				callback: function(slideroptn){
					if(slideroptn =='slide-layout-2'){
					return true;
					}
					return false;
				}
			},	
			{
				controls: [  
				'amaz_store_top_slide_content',  
				'amaz_store_lay3_adimg',
				'amaz_store_lay3_url',
				'amaz_store_lay3_adimg2',
				'amaz_store_lay3_2url',
				'amaz_store_lay3_adimg3',
				'amaz_store_lay3_3url',
				'amaz_store_include_category_slider',
				'amaz_store_lay3_bg_img',
				],
				callback: function(slideroptn){
					if(slideroptn =='slide-layout-3'){
					return true;
					}
					return false;
				}
			},	
			{
				controls: [  
				
				'amaz_store_lay3_bg_img',
				],
				callback: function(slideroptn){
					if(slideroptn =='slide-layout-4' || slideroptn =='slide-layout-3'|| slideroptn =='slide-layout-2'){
					return true;
					}
					return false;
				}
			},
			{
				controls: [    
				'amaz_store_top_slide_content6',
				],
				callback: function(slideroptn){
					if(slideroptn =='slide-layout-6'){
					return true;
					}
					return false;
				}
			},	
			{
				controls: [    
				'amaz_store_top_slide_content',
				],
				callback: function(slideroptn){
					if(slideroptn =='slide-layout-1' || slideroptn =='slide-layout-2' || slideroptn =='slide-layout-3' || slideroptn =='slide-layout-4' || slideroptn =='slide-layout-9'){
					return true;
					}
					return false;
				}
			},				 
		];	
            OPNCustomizerToggles['amaz_store_top_slider_optn'] = [
		    {
				controls: [    
				'amaz_store_slider_speed',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == true){
					return true;
					}
					return false;
				}
			},
			
			];
			OPNCustomizerToggles['amaz_store_cat_slider_optn'] = [
		    {
				controls: [    
				'amaz_store_cat_slider_speed',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == true){
					return true;
					}
					return false;
				}
			},
			
			];
			OPNCustomizerToggles['amaz_store_product_slider_optn'] = [
		    {
				controls: [    
				'amaz_store_product_slider_speed',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == true){
					return true;
					}
					return false;
				}
			},
			];	
			OPNCustomizerToggles['amaz_store_category_slider_optn'] = [
		    {
				controls: [    
				'amaz_store_category_slider_speed',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == true){
					return true;
					}
					return false;
				}
			}
			
			];

			OPNCustomizerToggles['amaz_store_product_list_slide_optn'] = [
		    {
				controls: [    
				'amaz_store_product_list_slide_speed',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == true){
					return true;
					}
					return false;
				}
			}
			
			];
			OPNCustomizerToggles['amaz_store_feature_product_slider_optn'] = [
		    {
				controls: [    
				'amaz_store_feature_product_slider_speed',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == true){
					return true;
					}
					return false;
				}
			}
			
			];
			OPNCustomizerToggles['amaz_store_cat_tb_lst_slider_optn'] = [
		    {
				controls: [    
				'amaz_store_cat_tb_lst_slider_speed',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == true){
					return true;
					}
					return false;
				}
			}
			
			];
			OPNCustomizerToggles['amaz_store_brand_slider_optn'] = [
		    {
				controls: [    
				'amaz_store_brand_slider_speed',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == true){
					return true;
					}
					return false;
				}
			}
			
		];


    });
})( jQuery );