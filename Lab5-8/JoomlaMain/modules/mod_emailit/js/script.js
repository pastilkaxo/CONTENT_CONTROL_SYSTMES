jQuery(document).ready(function () {
    e_mailit_config = {mobile_bar: false};
    jQuery.getScript("//www.e-mailit.com/widget/menu3x/js/button.js", function () {
        var share = e_mailit.services.split(","); // Get buttons
        for (var key = 0; key < share.length; key++) {
            var services = share[key];
            var name = services.replace(/_/gi, " ");
            if (name === "Google Plus")
                name = "Google+";
            if (name === "Facebook Like and Share")
                name = "Facebook Like & Share";
            var sharelinkInput = jQuery("<input type=\"checkbox\" id=\"checkbox" + services + "\" name=\"" + services + "\" />");
            var sharelinkLabel = jQuery("<label for=\"checkbox" + services + "\" class='services_list' id=\"" + services + "\" ><div class=\"E_mailit_" + services + "\"> </div> <span class='services_list_name'>" + name + "</span></label>");
            sharelinkInput.appendTo('#servicess');
            sharelinkLabel.appendTo('#servicess');
        }

        jQuery("#servicess input[type=checkbox]").click(function () {
            if (jQuery(this).is(':checked')) {
                var class_name = this.name.replace(/_/gi, " ");
                if (class_name === "Google Plus")
                    class_name = "Google+";
                if (class_name === "Facebook Like and Share")
                    class_name = "Facebook Like & Share";

                jQuery('#social_services').append('<li title="' + class_name + '" class="E_mailit_' + this.name + '"></li>');
                jQuery("#E_mailit_" + this.name + "").effect("transfer", {
                    to: "#social_services ." + this.name
                }, 500);
            } else {
                jQuery("#social_services .E_mailit_" + this.name).effect("transfer", {
                    to: "#" + this.name + ""
                }, 500).delay(500).remove();
            }
        });

        var new_share = jQuery('#jform_params_default_buttons').val().split(","); // Get buttons
        addButtons(new_share);

        jQuery("#social_services").sortable({
            revert: true,
            opacity: 0.8
        });
        jQuery("ul#social_services, #social_services li").disableSelection();
        jQuery("#check").button();
        jQuery("#servicess").buttonset();
        jQuery(".uncheck_all_btn").click(function () {
            jQuery("#servicess input[type=checkbox]").attr('checked', false);
            jQuery("#servicess input[type=checkbox]").button("refresh");
            jQuery("#social_services").empty();
            jQuery("#servicess input:not(:checked)").button("option", "disabled", false);
            jQuery(".message_good").show("fast");
        });

        jQuery(".social_services_default_btn").click(function () {
            jQuery(".uncheck_all_btn").click();
            addButtons(new_share);
            jQuery("#servises_customize_btn").show('fast');
            jQuery("#social_services #custom,#servicess,.filterinput,.social_services_default_btn,.message_good,.message_bad,.uncheck_all_btn").hide('fast');
            styleChanged();
        });

        jQuery("#servises_customize_btn").click(function () {
            jQuery("#servises_customize_btn").hide('fast');
            jQuery("#social_services #custom,#servicess,.filterinput,.message_good,.social_services_default_btn,.uncheck_all_btn").show('fast');
        });

        jQuery.expr[':'].Contains = function (a, i, m) {//boitheia gia to search me ta grammata tis :contains
            return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
        };
        jQuery('#filter-form-input-text').keyup(function (event) {
            var filter = jQuery('#filter-form-input-text').val();
            if (filter == '' || filter.length < 1) {
                jQuery(".services_list").show();
            } else {
                jQuery(".services_list").find(".services_list_name:not(:Contains(" + filter + "))").parent().parent().hide();
                jQuery(".services_list").find(".services_list_name:Contains(" + filter + ")").parent().parent().show();
            }
            var value = jQuery("#jform_params_toolbar_type input[type='radio']:checked").val();
            var nativeServices = ["Facebook", "Facebook_Like", "Facebook_Like_and_Share", "Facebook_Send", "Twitter", "Google_Plus", "LinkedIn", "Pinterest", "VKontakte", "Odnoklassniki","Zalo"];
            if (value === "native") {
                jQuery("#servicess label").each(function () {
                    if (jQuery.inArray(jQuery(this).attr('id'), nativeServices) < 0) {
                        jQuery(this).hide();
                    }
                });
            } else {
                jQuery("#servicess label#Facebook_Like, #servicess label#Facebook_Like_and_Share, #servicess label#Zalo").hide();
            }
            jQuery("#servicess").buttonset("refresh");
        });

        if (jQuery("#jform_params_follow_services").val() == "")
            jQuery("#jform_params_follow_services").val('{}');
        var follow_values = JSON.parse(jQuery("#jform_params_follow_services").val().replace(/\'/g, '"'));
        for (var key in e_mailit.follows_links) {
            var link_with_input = e_mailit.follows_links[key].replace(/{FOLLOW}/gi, '<input class="follow-field" name="follow_' + key + '" type="text">');
            jQuery("#social_services_follow").append('<li><i class="E_mailit_' + key + '"></i>' + link_with_input + '</li>');
            if (follow_values && follow_values[key]) {
                jQuery("#social_services_follow .follow-field[name='follow_" + key + "']").val(follow_values[key]);
            }
        }
        jQuery("#jform_params_mob_button_set input[type='radio']").click(function () {
            mobileButttonSetChanged();
        });
        function mobileButttonSetChanged() {
            var value = jQuery("#jform_params_mob_button_set input[type='radio']:checked").val();
            if (value === "mob_custom") {
                jQuery("#jform_params_mobile_services").parent().parent().show();
            } else {
                jQuery("#jform_params_mobile_services").parent().parent().hide();
            }
        }
        mobileButttonSetChanged();
        jQuery("#jform_params_toolbar_type input[type='radio']").click(function () {
            styleChanged();
        });
        function styleChanged() {
            jQuery('#filter-form-input-text').val("");
            var nativeServices = ["Facebook", "Facebook_Like", "Facebook_Like_and_Share", "Messenger", "Twitter", "Google_Plus", "LinkedIn", "Pinterest", "VKontakte", "Odnoklassniki","Zalo"];
            var value = jQuery("#jform_params_toolbar_type input[type='radio']:checked").val();
            if (value === "native") {
                jQuery("#jform_params_size").parent().parent().hide();
                jQuery("#jform_params_text_color").parent().parent().parent().hide();
                jQuery("#jform_params_back_color").parent().parent().parent().hide();
                jQuery("#jform_params_rounded").parent().parent().hide();
                jQuery("#jform_params_text_display").parent().parent().show();
                jQuery("#jform_params_global_text_color").parent().parent().parent().show();
                jQuery("#servicess label").show();
                jQuery("#servicess label").each(function () {
                    if (jQuery.inArray(jQuery(this).attr('id'), nativeServices) < 0) {
                        jQuery(this).hide();
                        jQuery("#social_services li.E_mailit_" + jQuery(this).attr('id')).remove();
                        jQuery("#servicess input#checkbox" + jQuery(this).attr('id')).prop('checked', false);
                    }
                });
            } else {
                    jQuery("#jform_params_text_display").parent().parent().hide();                    
                    jQuery("#servicess label").show();
                    jQuery("#servicess label#Facebook_Like, #servicess label#Facebook_Like_and_Share, #servicess label#Zalo").hide();
                    jQuery("#social_services li.E_mailit_Facebook_Like, #social_services li.E_mailit_Facebook_Like_and_Share, #social_services li.E_mailit_Zalo").remove();
                    jQuery("#servicess input#checkboxFacebook_Like, #servicess input#checkboxZalo").prop('checked', false);
                    
                    jQuery("#jform_params_back_color").parent().parent().parent().show();
                    jQuery("#jform_params_global_text_color").parent().parent().parent().hide();
                if (value === "wide") {
                    jQuery("#jform_params_text_color").parent().parent().parent().show();
                    jQuery("#jform_params_rounded").parent().parent().show();
                    jQuery("#jform_params_global_text_color").parent().parent().parent().show();
                    jQuery("#jform_params_text_display").parent().parent().show();
                }else if (value === "square"){
                    jQuery("#jform_params_text_color").parent().parent().parent().hide();
                    jQuery("#jform_params_rounded").parent().parent().show();
                }else{
                    jQuery("#jform_params_rounded").parent().parent().hide();
                    jQuery("#jform_params_text_color").parent().parent().parent().hide();
                }
            }
            jQuery("#servicess").buttonset("refresh");
        }
        styleChanged();

        jQuery("form[name='adminForm']").submit(function () {
            var e_mailit_default_servises = jQuery.map(jQuery('#social_services li'), function (element) {
                return jQuery(element).attr('class').replace(/E_mailit_/gi, '').replace(/ ui-sortable-handle/gi, '');
            }).join(',');

            jQuery('#jform_params_default_buttons').val(e_mailit_default_servises);

            var follow_services = {};
            jQuery("#social_services_follow .follow-field").each(function () {
                if (jQuery(this).val() !== "") {
                    var name = jQuery(this).attr('name').replace(/follow_/gi, '');
                    follow_services[name] = jQuery(this).val();
                }
            });
            var strFollows = JSON.stringify(follow_services).replace(/\"/g, "'");
            jQuery("#jform_params_follow_services").val(strFollows);
        });
        function addButtons(new_share) {
            for (var key = 0; key < new_share.length; key++) {
                var service = new_share[key];
                jQuery('#servicess #checkbox' + service).click();
            }
        }
    });
});