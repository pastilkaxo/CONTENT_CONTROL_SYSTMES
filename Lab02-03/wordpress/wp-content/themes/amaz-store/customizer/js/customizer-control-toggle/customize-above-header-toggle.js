/****************************/		
//Above header layout
/****************************/	
( function( $ ) {
//**********************************/
// container hide and show settings
//**********************************/
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ) {
		OPNCustomizerToggles ['amaz_store_above_header_col3_set'] = [
		    {
				controls: [
					'amaz_store_col3_texthtml',
					'amaz_store_col3_social_media_redirect',
				    'amaz_store_col3_menu_redirect',
				    'amaz_store_col3_widget_redirect',
				    'amaz_store_top_hdr_calto_txt',
				    'amaz_store_top_hdr_calto_nub',
				    'amaz_store_above_hdr_btn_txt',
				    'amaz_store_above_hdr_btn_lnk',
				    
				],
				callback: function(layout){
					if (layout=='none'){
					return false;
					}
					return true;
				   }
			},
            {
				controls: [    
				'amaz_store_col3_texthtml',
				
				],
				callback: function(layout){
					if(layout=='text'){
					return true;
					}
					return false;
				}
			},
            
            {
				controls: [ 
				'amaz_store_col3_menu_redirect',
				
				],
				callback: function(layout){
					if(layout=='menu'){
					return true;
					}
					return false;
				}
			},
            {
				controls: [ 
				'amaz_store_col3_widget_redirect',
				],
				callback: function(layout){
					if(layout=='widget'){
					return true;
					}
					return false;
				}
			},
            {
				controls: [ 
				'amaz_store_col3_social_media_redirect',
				],
				callback: function(layout){
					if(layout=='social'){
					return true;
					}
					return false;
				}
			},
			{
				controls: [ 
				'amaz_store_top_hdr_calto_txt',
				'amaz_store_top_hdr_calto_nub',
				],
				callback: function(layout){
					if(layout=='callto'){
					return true;
					}
					return false;
				}
			},
			{
				controls: [
				    'amaz_store_above_hdr_btn_txt',
				    'amaz_store_above_hdr_btn_lnk',
				    
				],
				callback: function(layout){
					if (layout=='button'){
					return true;
					}
					return false;
				   }
			},
					
		];
		
		});
})( jQuery );