( function( $ ) {
//**********************************/
// Slider settings
//**********************************/
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
		OPNCustomizerToggles ['amaz_store_cat_slide_layout'] = [
		    {
				controls: [    
				'amaz_store_category_slider_optn', 
				],
				callback: function(layout){
					if(layout =='cat-layout-1'){
					return true;
					}
					return false;
				}
			},	
				
			
			 
		];	
		OPNCustomizerToggles ['amaz_store_cat_slide_layout'] = [
		     {
				controls:[    
				'amaz_store_cat_item_no',
				
				],
				callback: function(layout7){
					if(layout7=='cat-layout-1'){
					return true;
					}else{
					return false;	
					}
				}
			},	
				
		];
    });
})( jQuery );