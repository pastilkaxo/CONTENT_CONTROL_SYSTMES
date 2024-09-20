/****************/
// Main header	
/****************/
( function( $ ) {
//**********************************/
// Main Header settings
//**********************************/
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
		OPNCustomizerToggles ['amaz_store_main_header_option'] = [
		    {
				controls: [    
				'amaz_store_main_hdr_btn_txt', 
				'amaz_store_main_hdr_btn_lnk',
				'amaz_store_main_hdr_calto_txt',
				'amaz_store_main_hdr_calto_nub',
				'amaz_store_main_hdr_calto_email',
				'amaz_store_main_header_widget_redirect',
				'amaz_store_main_col3_texthtml',
				],
				callback: function(headeroptn){
					if (headeroptn =='none'){
					return false;
					}
					return true;
				}
			},	
			 {
				controls: [    
				'amaz_store_main_hdr_btn_txt', 
				'amaz_store_main_hdr_btn_lnk',
				],
				callback: function(layout){
					if(layout=='button'){
					return true;
					}
					return false;
				}
			},
			 {
				controls: [    
				'amaz_store_main_hdr_calto_txt',
				'amaz_store_main_hdr_calto_nub',
				'amaz_store_main_hdr_calto_email',
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
				'amaz_store_main_header_widget_redirect'
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
				'amaz_store_main_col3_texthtml', 
				],
				callback: function(layout){
					if(layout=='text'){
					return true;
					}
					return false;
				}
			},
			 
		];	
		OPNCustomizerToggles ['amaz_store_sticky_header'] = [
		    {
				controls: [    
				'amaz_store_sticky_header_effect', 
				],
				callback: function(headeroptn){
					if (headeroptn == true){
					return true;
					}
					return false;
				}
			},	
		];	
    });
})( jQuery );