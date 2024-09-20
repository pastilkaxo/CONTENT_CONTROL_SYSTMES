/*************************************/
// Banner Hide N Show control
/*************************************/
( function( $ ){
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
OPNCustomizerToggles ['amaz_store_banner_layout'] = [
		     

		     {
				controls: [    
				'amaz_store_bnr_1_img',
				'amaz_store_bnr_1_url',
				'amaz_store_bnr_2_img',
				'amaz_store_bnr_2_url',
				'amaz_store_bnr_3_img',
				'amaz_store_bnr_3_url',
				'amaz_store_bnr_4_img',
				'amaz_store_bnr_4_url',
				'amaz_store_bnr_5_img',
				'amaz_store_bnr_5_url',
				
				],
				callback: function(layout){
					if(layout=='bnr-four'){
					return true;
					}else{
					return false;	
					}
				}
			},	
			{
				controls: [    
				'amaz_store_bnr_1_img',
				'amaz_store_bnr_1_url',
				'amaz_store_bnr_2_img',
				'amaz_store_bnr_2_url',
				'amaz_store_bnr_3_img',
				'amaz_store_bnr_3_url',
				'amaz_store_bnr_4_img',
				'amaz_store_bnr_4_url',
				
				],
				callback: function(layout){
					if(layout=='bnr-five' ||  layout=='bnr-four'){
					return true;
					}else{
					return false;	
					}
				}
			},	
		    {
				controls: [    
				'amaz_store_bnr_1_img',
				'amaz_store_bnr_1_url',
				'amaz_store_bnr_2_img',
				'amaz_store_bnr_2_url',
				'amaz_store_bnr_3_img',
				'amaz_store_bnr_3_url',
				
				],
				callback: function(layout){
					if(layout=='bnr-three' || layout=='bnr-four' || layout=='bnr-five'){
					return true;
					}else{
					return false;	
					}
				}
			},	
			{
				controls: [    
				'amaz_store_bnr_1_img',
				'amaz_store_bnr_1_url',
				'amaz_store_bnr_2_img',
				'amaz_store_bnr_2_url',
				
				],
				callback: function(layout){
					if(layout=='bnr-two'|| layout=='bnr-three' || layout=='bnr-four' || layout=='bnr-five' || layout=='bnr-six'){
					return true;
					}else{
					return false;	
					}
				}
			},	
			{
				controls: [    
				'amaz_store_bnr_1_img',
				'amaz_store_bnr_1_url',	
				],
				callback: function(layout){
					if(layout=='bnr-one' || layout=='bnr-two'|| layout=='bnr-three' || layout=='bnr-four' || layout=='bnr-five' || layout=='bnr-six'){
					return true;
					}else{
					return false;	
					}
				}
			},	
				
		];	
	});
})( jQuery );