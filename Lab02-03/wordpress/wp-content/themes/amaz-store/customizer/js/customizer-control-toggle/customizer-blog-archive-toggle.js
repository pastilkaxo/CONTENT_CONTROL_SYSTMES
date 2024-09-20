/*************************************/
// Blog Archive Hide and Show control
/*************************************/
( function( $ ){
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
OPNCustomizerToggles ['amaz_store_blog_post_content'] = [
		    {
				controls: [    
				'amaz_store_blog_expt_length',
				'amaz_store_blog_read_more_txt',
				],
				callback: function(layout){
					if(layout=='full'|| layout=='nocontent'){
					return false;
					}
					return true;
				}
			},	
		];	
	});
})( jQuery );