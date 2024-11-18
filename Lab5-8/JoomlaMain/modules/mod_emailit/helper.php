<?php

/*
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2012 E-MAILiT                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 3 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program.  If not, see <http://www.gnu.org/licenses/>.    |
 * +--------------------------------------------------------------------------+
 */
 
class ModEmailitHelper
{
	/**
	 * appendEmailitScript
	 *
	 * Reads button settings and creates corresponding MAILiT button.
	 *
	 * @param object $params
	 * @return void
	 *
	 */
	public static function appendEmailitScript(&$params) {
		$arr_parrams = ModEmailitHelper::populateParams($params);
		//Creates Emailit script
		$outputValue = "";
		if(!defined('_EMAILiT')){
			define('_EMAILiT', true);
			$configValues = array();
			if ($arr_parrams["display_counter"] == '0')
				$configValues[] = "display_counter:false";
			else
				$configValues[] = "display_counter:true";
			if ($arr_parrams["combine_counters"] == '0')
				$configValues[] = "combine_counters:false";
			else
				$configValues[] = "combine_counters:true";				
			if ($arr_parrams["TwitterID"] != "")
				$configValues[] = "TwitterID:'" . $arr_parrams["TwitterID"] . "'";
            if ($arr_parrams["FB_appId"] != "")
                $configValues[] = "FB_appId:'" . $arr_parrams["FB_appId"] . "'";			
			if ($arr_parrams['follow_services'] != "")
				$configValues[] = "follow_services:" . $arr_parrams["follow_services"];
			if ($arr_parrams['thanks_message'] != "")
				$configValues[] = "thanks_message:'" . addslashes($arr_parrams["thanks_message"]) . "'";
			if ($arr_parrams['global_back_color'] != "")
				$configValues[] = "global_back_color:'" . $arr_parrams["global_back_color"] . "'";
			if ($arr_parrams['mobile_back_color'] != "")
				$configValues[] = "mobile_back_color:'" . $arr_parrams["mobile_back_color"] . "'";				
			if ($arr_parrams['global_text_color'] != "")
				$configValues[] = "global_text_color:'" . $arr_parrams["global_text_color"] . "'";
			if ($arr_parrams['text_display'] != "Share" && $arr_parrams['text_display'] != "") {
				$configValues[] = "text_display:'" . addslashes($arr_parrams["text_display"]) . "'";
			}
			if ($arr_parrams['mobile_bar'] != "0")
				$configValues[] = "mobile_bar:true";
			else
				$configValues[] = "mobile_bar:false";
            if ($arr_parrams['mobile_position'] == "top")
                $configValues[] = "mobile_position:'top'";					
			if ($arr_parrams['after_share_dialog'] != "0")
				$configValues[] = "after_share_dialog:true";
			else
				$configValues[] = "after_share_dialog:false";    
			if ($arr_parrams['display_ads'] != "0")
				$configValues[] = "display_ads:true";
			else
				$configValues[] = "display_ads:false";
			if ($arr_parrams['hover_pinit'] != "0")
				$configValues[] = "hover_pinit:true";
			else
				$configValues[] = "hover_pinit:false";
			if ($arr_parrams['popup'] == "1")
				$configValues[] = "popup:true";
			else
				$configValues[] = "popup:false";
			if ($arr_parrams['notrack'] == "1")
				$configValues[] = "notrack:true";
			else
				$configValues[] = "notrack:false"; 
			
			if ($arr_parrams['ad_url'] != "")
				$configValues[] = "ad_url:'" . $arr_parrams["ad_url"] . "'";
			if ($arr_parrams['logo'] != "")
				$configValues[] = "logo:'" . $arr_parrams["logo"] . "'";

			if ($arr_parrams['open_on'] != "") {
				$configValues[] = "open_on:'" . $arr_parrams["open_on"] . "'";
			}
			if ($arr_parrams['auto_popup'] && $arr_parrams['auto_popup'] != "0") {
				$configValues[] = "auto_popup:" . $arr_parrams["auto_popup"] * 1000;
			}
			if ($arr_parrams['emailit_branding'] != "0")
				$configValues[] = "emailit_branding:true";
			else
				$configValues[] = "emailit_branding:false";			
			if ($arr_parrams['mob_button_set'] === "mob_same"){
				$mobileServices = $arr_parrams['default_buttons'];
				if ($arr_parrams["global_button"] === "last") {
					if($mobileServices != "")
						$mobileServices .= ",EMAILiT";
					else
						$mobileServices = "EMAILiT";
				}else if ($arr_parrams["global_button"] === "first") {
					if($mobileServices != "")
						$mobileServices = "EMAILiT," . $mobileServices;
					else
						$mobileServices = "EMAILiT";
				}
				$configValues[] = "'mobileServices':'" . $mobileServices . "'";
			}else if ($arr_parrams['mob_button_set'] === "mob_custom")
				$configValues[] = "'mobileServices':'" . str_replace(array("\r", "\n", " "), '', $arr_parrams['mobile_services']) . "'";	
				
			$outputValue .= "var e_mailit_config = {" . implode(",", $configValues) . "};";
			$outputValue .= "(function() {	var b=document.createElement('script');	
								b.type='text/javascript';b.async=true;\r\n	
								b.src='//www.e-mailit.com/widget/menu3x/js/button.js';\r\n	
								var c=document.getElementsByTagName('head')[0];	c.appendChild(b) })()";
			$outputValue .= PHP_EOL;

			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($outputValue);
		}
		
		$outputValue = ModEmailitHelper::emailit_createButton($arr_parrams);

		echo $outputValue;
	}

	static function emailit_createButton($emailit_options, $url = null, $title = null) {
            $shared_url = $url != null ? "data-emailit-url='" . $url . "'" : "";
            $shared_title = $title != null ? "data-emailit-title='" . strip_tags($title) . "'" : "";

            //Creating div elements for e-mailit
            $style = $emailit_options["toolbar_type"];
            if ($emailit_options['back_color'] != "") {
                $style .= " no_bgr";
            }
            $back_color = $text_color = "";
            if ($emailit_options['back_color'] != "") {
                $back_color = " data-back-color='" . $emailit_options["back_color"] . "'";
            }    
            if ($emailit_options['text_color'] != "") {
                $text_color = " data-text-color='" . $emailit_options["text_color"] . "'";
            }        

            if($emailit_options["toolbar_type"] !== "native"){
                $size = $emailit_options["size"];
                if ($emailit_options['size'] != "") {
                    $style .= " size" . $size;
                }        
            }

            if (isset($emailit_options["circular"]) && $emailit_options["circular"] == "1") {
                $style .= " circular";
            }

            if ($emailit_options["toolbar_type"] !== "circular" &&$emailit_options["toolbar_type"] !== "native" && isset($emailit_options["rounded"]) && $emailit_options["rounded"] == "1") {
                $style .= " rounded";
            }
            if (isset($emailit_options["toolbar_position"])) {
                    $style .= " " . $emailit_options["toolbar_position"];
            }            
            $outputValue = "<div class=\"e-mailit_toolbox $style\" $shared_url $shared_title$back_color$text_color>" . PHP_EOL;
            if ($emailit_options["global_button"] === "first") {
                $outputValue .= "<div class=\"e-mailit_btn_EMAILiT\"></div>" . PHP_EOL;
            }

            $stand_alone_buttons = array_filter(explode(",", $emailit_options["default_buttons"]));

            foreach ($stand_alone_buttons as $stand_alone_button) {
                $outputValue .= "<div class=\"e-mailit_btn_$stand_alone_button\"></div>" . PHP_EOL;
            }
            if ($emailit_options["global_button"] === "last") {
                $outputValue .= "<div class=\"e-mailit_btn_EMAILiT\"></div>";
            }
            $outputValue .= "</div>" . PHP_EOL;
            return $outputValue;
        }
		
	/**
	 * populateParams
	 *
	 * Gets the plugin parameters and holds them as a collection
	 *
	 * @return Array of user selected E-MAILiT configuration values
	 */
	static function populateParams($params) {
		$arrParams = array("notrack","combine_counters","FB_appId","popup","mobile_position","rounded","size","emailit_branding","mobile_services","mob_button_set","mobile_back_color","logo","toolbar_position","circular","global_button","toolbar_type","back_color","global_back_color","default_buttons","follow_services","thanks_message","display_counter", "text_color","text_display","global_text_color","mobile_bar","after_share_dialog","display_ads","hover_pinit","ad_url","follow_after_share","open_on","auto_popup","show_frontpage", "position", "filter_art", "filter_cat", "filter_sec", "TwitterID");
		foreach ($arrParams as $key => $value) {
			$arr_parrams[$value] = $params->get($value);
		}
		return $arr_parrams;
	} 
}