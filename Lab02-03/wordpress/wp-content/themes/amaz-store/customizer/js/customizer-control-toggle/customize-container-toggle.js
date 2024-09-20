/*********************************/
// Sidebar hide and show control
/*********************************/
( function( $ ){
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
OPNCustomizerToggles ['amaz_store_default_container'] = [
		    {
				controls: [    
				'amaz_store_conatiner_maxwidth',
				'amaz_store_conatiner_top_btm',
				],
				callback: function(layout){
					if(layout=='fullwidth'){
					return false;
					}
					return true;
				}
			},
			{
				controls: [    
				'amaz_store_conatiner_width',  
				],
				callback: function(layout){
					if(layout =='boxed'){
					return false;
					}
					return true;
				}
			},		
		];
	});
})( jQuery );