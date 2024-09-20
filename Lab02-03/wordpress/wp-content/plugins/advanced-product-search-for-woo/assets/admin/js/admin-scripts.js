jQuery( document ).ready( function($) {
	 "use strict";
		jQuery( this ).on( "click", ".apsw_radio_box label", function ( e ) {
			
			 
			 
		});
		
		$( document ).on( 'click', '.apsw-notice-nux .notice-dismiss', function() {
			
			$.ajax({
				url : apsw_loc.ajaxurl,
				type : 'post',
				data : {
					action 		: 'apsw_dismiss_notice',
					nonce 		: apsw_loc.nonce,
				}
			});		
			
		});

});