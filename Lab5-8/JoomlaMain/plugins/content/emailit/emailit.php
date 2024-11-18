<?php

/*
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2015 E-mailit                                        |
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

// no direct access
defined('_JEXEC') or die('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

jimport('joomla.plugin.plugin');
jimport('joomla.version');

/**
 * plgContentEmailit
 *
 * Creates Emailit sharing button with each and every posts.
 *
 */
class plgContentEmailit extends JPlugin {

    /**
     * Constructor
     *
     * Loads the plugin settings and assigns them to class variables
     *
     * @param reference $subject
     * @param object $config
     */
    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
        $this->setBaseURL();
        $this->setPageProtocol();        
        $this->populateParams();
        $this->appendEmailitScript();
    }
    
    private function appendEmailitScript(){
        if(!defined('_EMAILiT')){
            define('_EMAILiT', true);
            //Creates Emailit script
            $outputValue = "";
            $configValues = array();
            if ($this->arrParamValues["display_counter"] == '0')
                $configValues[] = "display_counter:false";
            else
                $configValues[] = "display_counter:true";
            if ($this->arrParamValues["combine_counters"] == '0')
                $configValues[] = "combine_counters:false";
            else
                $configValues[] = "combine_counters:true";				
            if ($this->arrParamValues["TwitterID"] != "")
                $configValues[] = "TwitterID:'" . $this->arrParamValues["TwitterID"] . "'";
            if ($this->arrParamValues["FB_appId"] != "")
                $configValues[] = "FB_appId:'" . $this->arrParamValues["FB_appId"] . "'";			
            if ($this->arrParamValues['follow_services'] != "")
                $configValues[] = "follow_services:" . $this->arrParamValues["follow_services"];
            if ($this->arrParamValues['thanks_message'] != "")
                $configValues[] = "thanks_message:'" . addslashes($this->arrParamValues["thanks_message"]) . "'";
            if ($this->arrParamValues['global_back_color'] != "")
                $configValues[] = "global_back_color:'" . $this->arrParamValues["global_back_color"] . "'";
            if ($this->arrParamValues['mobile_back_color'] != "")
                $configValues[] = "mobile_back_color:'" . $this->arrParamValues["mobile_back_color"] . "'";			
            if ($this->arrParamValues['global_text_color'] != "")
                $configValues[] = "global_text_color:'" . $this->arrParamValues["global_text_color"] . "'";
            if ($this->arrParamValues['text_display'] != "Share" && $this->arrParamValues['text_display'] != "") {
                $configValues[] = "text_display:'" . addslashes($this->arrParamValues["text_display"]) . "'";
            }
            if ($this->arrParamValues['mobile_bar'] != "0")
                $configValues[] = "mobile_bar:true";
            else
                $configValues[] = "mobile_bar:false";
            if ($this->arrParamValues['mobile_position'] == "top")
                $configValues[] = "mobile_position:'top'";				
            if ($this->arrParamValues['after_share_dialog'] != "0")
                $configValues[] = "after_share_dialog:true";
            else
                $configValues[] = "after_share_dialog:false";    
            if ($this->arrParamValues['display_ads'] != "0")
                $configValues[] = "display_ads:true";
            else
                $configValues[] = "display_ads:false";
             if ($this->arrParamValues['hover_pinit'] != "0")
                $configValues[] = "hover_pinit:true";
            else
                $configValues[] = "hover_pinit:false";
             if ($this->arrParamValues['popup'] == "1")
                $configValues[] = "popup:true";
            else
                $configValues[] = "popup:false";  				
            if ($this->arrParamValues['ad_url'] != "")
                $configValues[] = "ad_url:'" . $this->arrParamValues["ad_url"] . "'";
            if ($this->arrParamValues['logo'] != "")
                $configValues[] = "logo:'" . $this->arrParamValues["logo"] . "'";

            if ($this->arrParamValues['open_on'] != "") {
                $configValues[] = "open_on:'" . $this->arrParamValues["open_on"] . "'";
            }
            if ($this->arrParamValues['auto_popup'] && $this->arrParamValues['auto_popup'] != "0") {
                $configValues[] = "auto_popup:" . $this->arrParamValues["auto_popup"] * 1000;
            }
			
             if ($this->arrParamValues['notrack'] == "1")
                $configValues[] = "notrack:true";
            else
                $configValues[] = "notrack:false";  			
			
			$headline = array();
			if($this->arrParamValues['headline_content'] != ""){
				$headline['content'] = $this->arrParamValues['headline_content'];
				$headline['font-family'] = $this->arrParamValues['headline_font-family'];
				$headline['font-size'] = $this->arrParamValues['headline_font-size'];
				$headline['color'] = $this->arrParamValues['headline_color'];
			}
			$configValues[] = "headline:" . json_encode($headline);
			
            if ($this->arrParamValues['emailit_branding'] != "0")
                $configValues[] = "emailit_branding:true";
            else
                $configValues[] = "emailit_branding:false";			
			if ($this->arrParamValues['mob_button_set'] === "mob_same"){
				$mobileServices = $this->arrParamValues['default_buttons'];
				if ($this->arrParamValues["global_button"] === "last") {
					if($mobileServices != "")
						$mobileServices .= ",EMAILiT";
					else
						$mobileServices = "EMAILiT";
				}else if ($this->arrParamValues["global_button"] === "first") {
					if($mobileServices != "")
						$mobileServices = "EMAILiT," . $mobileServices;
					else
						$mobileServices = "EMAILiT";
				}
				$configValues[] = "'mobileServices':'" . $mobileServices . "'";
			}else if ($this->arrParamValues['mob_button_set'] === "mob_custom")
				$configValues[] = "'mobileServices':'" . str_replace(array("\r", "\n", " "), '', $this->arrParamValues['mobile_services']) . "'";		
			
            $outputValue .= "var e_mailit_config = {" . implode(",", $configValues) . "};";
            $outputValue .= "(function() {	var b=document.createElement('script');	
                                b.type='text/javascript';b.async=true;\r\n	
                                b.src='//www.e-mailit.com/widget/menu3x/js/button.js';\r\n	
                                var c=document.getElementsByTagName('head')[0];	c.appendChild(b) })()";
            $outputValue .= PHP_EOL;

            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration($outputValue);
        }
    }
    /*
     * onContentPrepare
     */

    public function onContentPrepare($context, &$article, &$params, $limitstart) {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
		$lang = JFactory::getLanguage();
        $menu = $app->getMenu();
		$menu_active = $menu->getActive();
		$menu_default = $menu->getDefault($lang->getTag());
		$component = $app->input->get('option');
		$view = $app->input->get('view');        
		
		$front_page = 
		(
			isset($menu_active) &&
			isset($menu_default) &&
			$menu_active == $menu_default &&
			$app->input->get('view') == $menu_default->query['view']
		) ? true : false;
		
        $featured = $app->input->get('view') == 'featured';
        
        $share = true;
        // If no text or no title
		if (!isset($article->text) || !isset($article->title)){
			$share = false;
		}elseif ($this->params->get('show_content', '1') != '1') {
            $share = false;
        }elseif (strpos($article->text, '{no_emailit}') !== false) {
            $share = false;
            $article->text = str_replace('{no_emailit}', '', $article->text);
        }elseif ($featured && $this->params->get('show_featured', '1') != '1') {
            $share = false;       
		}elseif ($front_page && $this->params->get('show_frontpage', '1') != '1') {
            $share = false;
        }elseif ($app->input->get('view') == 'category' &&!$featured && $this->params->get('show_categories', '1') != '1') {
            $share = false;
        }elseif ($this->params->get('filter_cat', 0) != 0) {
            if (in_array($article->catid, $this->params->get('filter_cat', array()))) {
                $share = false;
            }
        }

		if ($this->arrParamValues["filter_art"] != "") {
            $filter_artArray = explode(",", str_replace(" ", "", trim($this->arrParamValues["filter_art"]))); // array with excluded articles
            if (in_array($article->id, $filter_artArray))
                $share = false;
        }		
		
		if ($component == 'com_k2'
			&& (($view == "item" && $this->params->get('show_k2item', '1') != '1')
			|| ($view == "itemlist" && $this->params->get('show_k2list', '1') != '1')
			|| ($view == "latest" && $this->params->get('show_k2latest', '1') != '1'))){
			$share = false;
		}
		
        $url = $this->getArticleUrl($article);
        $title = isset($article->title) ? $article->title : '';
        if($share){
            $outputValue = $this->emailit_createButton($this->arrParamValues, $url, $title);

            //Positioning button according to the position chosen
            if (isset($article->text)) {
                if ("top" == $this->arrParamValues["position"]) {
                    $article->text = $outputValue . $article->text;
                } elseif ("bottom" == $this->arrParamValues["position"]) {
                    $article->text = $article->text . $outputValue;
                } else {
                    $article->text = $outputValue . $article->text . $outputValue;
                }
            }
        }
        if(strpos($article->text, '{emailit}') !== false){
            $article->text = str_replace('{emailit}', $this->emailit_createButton($this->arrParamValues, $url, $title), $article->text);
        }
    }

    private function emailit_createButton($emailit_options, $url = null, $title = null) {

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
     * @return void
     */
    private function populateParams() {
        $version = new JVersion;
        $joomlaVersion = isset($version->RELEASE)?$version->RELEASE:$version->getShortVersion();

        // Loading plugin parameters for Joomla 1.5
        if ($joomlaVersion && $joomlaVersion < 1.6) {
            $plugin = JPluginHelper::getPlugin('content', 'emailit');
            $params = new JParameter($plugin->params);
        }

        $arrParams = array("notrack","combine_counters","FB_appId","headline_content","headline_font-family","headline_font-size","headline_color","popup","mobile_position","rounded","size","emailit_branding","mobile_services","mob_button_set","mobile_back_color","logo","circular","global_button","toolbar_type","global_back_color","back_color","default_buttons","follow_services","thanks_message","display_counter", "global_text_color","text_color","text_display","mobile_bar","after_share_dialog","display_ads","hover_pinit","ad_url","follow_after_share","open_on","auto_popup","show_featured", "show_frontpage", "position", "filter_art", "filter_cat", "filter_sec", "TwitterID");
        foreach ($arrParams as $key => $value) {
            $this->arrParamValues[$value] = $joomlaVersion && $joomlaVersion > 1.5 ? $this->params->def($value) : $params->get($value);
        }
    }

    /**
     * 	Gets the current page protocol
     *
     * @return void
     */
    private function setPageProtocol() {
        $arrVals = explode(":", $this->baseURL);
        $this->pageProtocol = $arrVals[0];
    }

    /**
     * Setting the base url
     *
     * @return void
     */
    private function setBaseURL() {
        $uri = JURI::getInstance();
        $this->baseURL = $uri->toString(array('scheme', 'host', 'port'));
    }

    private function getArticleUrl(&$article)
    {
            if (!is_null($article))
            {
                    if (isset($article->id) && isset($article->catid))
                    {
                            // If a K2 item
                            if (class_exists('K2HelperRoute') && is_object($article->params) && $article->params->exists('k2Sef'))
                            {
                                    $url = JRoute::_(K2HelperRoute::getItemRoute($article->id, $article->catid));
                            }
                            // Otherwise, a standard Joomla article
                            else
                            {
                                    require_once(JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');
                                    $url = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid));
                            }

                            return JRoute::_($this->baseURL . $url, true, 0);
                    }
                    else
                    {
                            return null;
                    }
            }
    }
}
