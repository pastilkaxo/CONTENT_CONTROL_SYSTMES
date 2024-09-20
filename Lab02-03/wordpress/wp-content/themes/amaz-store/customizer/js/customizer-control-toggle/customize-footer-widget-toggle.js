/*****************************************************************************/
/**********************customizer control setting*************************/
/*****************************************************************************/
( function( $ ) {
//**********************************/
// Footer widget hide and show settings
//**********************************/
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
		OPNCustomizerToggles ['amaz_store_bottom_footer_widget_layout'] = [
			{
				controls: [
					
					
					'amaz_store_bottom_footer_widget_redirect',
				],
				callback: function(layout){
					if ('ft-wgt-none'== layout){
						return false;
					}
					return true;
				}
			},
				
		];	
 });
})( jQuery );