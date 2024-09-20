jQuery( document ).ready( function ( $ ) {
'use strict';


jQuery('.ata-cart-notice.is-dismissible').on('click', '.notice-dismiss', function(e){
 e.preventDefault();

	var data = {
		action: 'ata_cart_notices',
		security: ata_cart_vars.ata_cart_nonce
	};
	 jQuery.post( ata_cart_vars.ajaxurl, data, function( response ) {
	 	
	 
	}); 
	return false;
});




} );

