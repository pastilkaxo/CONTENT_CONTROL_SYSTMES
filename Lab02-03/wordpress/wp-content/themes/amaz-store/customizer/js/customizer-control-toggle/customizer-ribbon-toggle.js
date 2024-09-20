/*************************************/
// Ribbon Hide N Show control
/*************************************/
( function( $ ){
OPNControlTrigger.addHook( 'amaz-store-toggle-control', function( argument, api ){
OPNCustomizerToggles ['amaz_store_ribbon_background'] = [
		     {
				controls:[    
				'amaz_store_ribbon_bg_background_image',
				
				],
				callback: function(layout){
					if(layout=='image'){
					return true;
					}else{
					return false;	
					}
				}
			},
			{
				controls: [  
				'amaz_store_ribbon_video_poster_image',
				'amaz_store_ribbon_bg_video', 
			    
				],
				callback: function(layout){
					if(layout =='image'){
					return false;
					}else{
					return true;	
					}
				}
			},		
			{
				controls: [  
				'amaz_store_ribbon_video_poster_image',
				'amaz_store_ribbon_bg_video',
				'amaz_store_enable_youtube_video' ,
			    
				],
				callback: function(layout1){
					if(layout1 =='video'){
					return true;
					}else{
					return false;	
					}
				}
			},	
		];	

		//youtube video

		OPNCustomizerToggles ['amaz_store_enable_youtube_video'] = [
		     {
				controls:[    
				'amaz_store_youtube_video_link',
				
				],
				callback: function(layout2){
					if(layout2==1){
					return true;
					}else{
					return false;	
					}
				}
			},	
			{
				controls: [  
				'amaz_store_ribbon_video_poster_image',
				'amaz_store_ribbon_bg_video', 
			    
				],
				callback: function(layout3){
					if(layout3 ==1){
					return false;
					}else{
					return true;	
					}
				}
			},	
		];

	});
})( jQuery );