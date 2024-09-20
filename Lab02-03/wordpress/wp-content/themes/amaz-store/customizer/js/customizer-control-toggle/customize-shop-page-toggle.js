( function( $ ) {
//**********************************/
// Slider settings
//**********************************/
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
         OPNCustomizerToggles ['amaz_store_pagination'] = [
		    {
				controls: [    
				'amaz_store_pagination_loadmore_btn_text',
				],
				callback: function(sliderspdoptn){
					if(sliderspdoptn == 'click'){
					return true;
					}
					return false;
				}
			},
			
			];


    });
})( jQuery );