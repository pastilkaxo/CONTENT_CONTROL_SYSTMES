/****************************/		
//Above header layout
/****************************/	
( function( $ ) {
//**********************************/
// container hide and show settings
//**********************************/
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ) {
		OPNCustomizerToggles ['amaz_store_inner_above_header_select'] = [
		    {
				controls: [
					'amaz_store_inner_above_header_slider',
					'amaz_store_inner_above_header_autoplay',
				    
				],
				callback: function(layout){
					if (layout=='innerpageslider'){
					return true;
					}
					return false;
				   }
			},                                               										
		];
		
		});
})( jQuery );