//Changes display per Captcha type in Admin Panel
jQuery(function() {
    jQuery('#captchaType').change(function(){
        jQuery('.rcwp_tabs').hide();
        jQuery('#' + jQuery(this).val()).show();
    });


    var captchaType = jQuery('#captchaType').find(":selected").val();
    jQuery('#' + captchaType).show();

    jQuery('#protectedLoginForm').change(function(){
	if (this.checked) {
        	jQuery('#login_form_attempts_fieldset').show();
	} else {
		jQuery('#login_form_attempts_fieldset').hide();
	}
	
    });
	if(jQuery('#protectedLoginForm').is(':checked')) {
		jQuery('#login_form_attempts_fieldset').show();
	} else {
		jQuery('#login_form_attempts_fieldset').hide();
	}

});
