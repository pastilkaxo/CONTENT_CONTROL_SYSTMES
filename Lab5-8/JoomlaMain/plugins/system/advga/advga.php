<?php

/**
 * @package		Advanced Google Analytics - Plugin for Joomla!
 * @author		Alin Marcu - http://deconf.com
 * @copyright	Copyright (c) 2010 - 2014 DeConf.com
 * @license		GNU/GPL license: http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );

class plgSystemadvga extends JPlugin {

 private $authorname, $categoryname, $pubyear;

 function plgSystemClickyTrackingCode(&$subject, $params) {

	parent::__construct($subject, $params);

	$mode = $this->params->def('mode', 1);

}

 function onContentAfterDisplay( $context, &$article, &$params ) {
		if ($context == "com_content.article"){
			$this->authorname=($article->created_by_alias) ? $article->created_by_alias : $article->author;
			$this->categoryname=$article->category_title;
			$temp=explode('-',$article->created);
			$this->pubyear=$temp[0];
		}
 }

function onBeforeRender() {
		JHtml::_('jquery.framework');
}

function onAfterRender(){

		$app = JFactory::getApplication();
		$user = JFactory::getUser();



		if ( $app->isClient('administrator') ){
			return;
		}

		if ((isset($user->groups[8]) || isset($user->groups[7])) AND (!$this->params->get('advga_trackadmin'))){
			return;
		}

		$adgva_ident = explode( '-', $this->params->get('advga_googleid') );

		if ( isset( $adgva_ident[0] ) && $adgva_ident[0] == 'UA' ){
			$advga_tracktype = 1;
		} else if ( isset( $adgva_ident[0] ) && $adgva_ident[0] == 'G' ) {
			$advga_tracktype = 0;
		} else {
			return;
		}

		$tracking_events = '';

		if ( $this->params->get('advga_event') ){

			if ( $advga_tracktype ){

				$tracking_events="<script type=\"text/javascript\">
(function($){
    $(window).load(function() {
            $('a').filter(function() {
				return this.href.match(/.*\.(".$this->params->get('advga_downloadfjq').")(\?.*)?$/);
            }).click(function(e) {
                ga('send','event', 'download', 'click', this.href);
            });
            $('a[href^=\"mailto\"]').click(function(e) {
                ga('send','event', 'email', 'send', this.href);
             });
            var loc = location.host.split('.');
            while (loc.length > 2) { loc.shift(); }
            loc = loc.join('.');
            var localURLs = [
                              loc,
                              "."'".$this->params->get('advga_domain')."'"."
                            ];
            $('a[href^=\"http\"]').filter(function() {
			if (!this.href.match(/.*\.(".$this->params->get('advga_downloadfjq').")(\?.*)?$/)){
				for (var i = 0; i < localURLs.length; i++) {
					if (this.href.indexOf(localURLs[i]) == -1) return this.href;
				}
			}
            }).click(function(e) {
                ga('send','event', 'outbound', 'click', this.href);
            });
    });
})(jQuery);
</script>";
			}
		}

		if ( !$advga_tracktype ){

			$tracking_0="\n<script async src=\"https://www.googletagmanager.com/gtag/js?id=".$this->params->get('advga_googleid')."\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '".$this->params->get('advga_googleid')."'";

		} else {

			$domain = $this->params->get('advga_domain');
			$root = explode ( '/', $domain );
			preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", str_ireplace ( 'www', '', isset ( $root [2] ) ? $root [2] : $domain ), $root );
			$tracking_0="\n<script type=\"text/javascript\">
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	  ga('create', '".$this->params->get('advga_googleid')."', 'auto');
	  ";

		}

		if ( $advga_tracktype ){
			$tracking_2="\n</script>";
		}

		$tracking_push='';

		if ($this->params->get('advga_remarketing') AND $advga_tracktype){
			$tracking_push.="\nga('require', 'displayfeatures');";
		}

		if ($this->params->get('advga_anonim')){
			$anonim = "";
			if ( !$advga_tracktype ){
				$anonim ="\n 'anonymize_ip': true";
			} else {
				$tracking_push.="\nga('set', 'anonymizeIp', true);";
			}
		}

		if( ( $app->getInput()->get( 'view' ) == 'article' ) OR ( $app->getInput()->get( 'view' ) == 'item' ) ){

			$custom_map = array();
			$custom_event = array();

			if ($this->authorname AND $this->params->get('advga_authors')){
				if ( $advga_tracktype ){
					$tracking_push.="\nga('set', 'dimension1', '".$this->authorname."');";
				} else {
					$custom_map['dimension1'] = 'aiwp_dim_1';
					$custom_event['aiwp_dim_1'] = $this->authorname;
				}
			}

			if ($this->categoryname AND $this->params->get('advga_categories')){
				if ($advga_tracktype){
					$tracking_push.="\nga('set', 'dimension2', '".$this->categoryname."');";
				} else {
					$custom_map['dimension2'] = 'aiwp_dim_2';
					$custom_event['aiwp_dim_2'] = $this->categoryname;
				}

			}

			if ($this->pubyear AND $this->params->get('advga_pubyear')){
				if ($advga_tracktype){
					$tracking_push.="\nga('set', 'dimension3', '".$this->pubyear."');";
				} else {
					$custom_map['dimension3'] = 'aiwp_dim_3';
					$custom_event['aiwp_dim_3'] = $this->pubyear;
				}

			}

		}

		if ($this->params->get('advga_usertype')){
			if (isset($user->username)){
				if ( $advga_tracktype ){
					$tracking_push.="\nga('set', 'dimension4', 'registered');";
				} else {
					$custom_map['dimension4'] = 'aiwp_dim_4';
					$custom_event['aiwp_dim_4'] = 'registered';
				}

			} else {
				if ( $advga_tracktype ){
					$tracking_push.="\nga('set', 'dimension4', 'guest');";
				} else {
					$custom_map['dimension4'] = 'aiwp_dim_4';
					$custom_event['aiwp_dim_4'] = 'guest';
				}
			}
		}

		if ( !$advga_tracktype ){
			if ( empty( $custom_map ) ){
				if ( empty( $anonim ) ) {
					$tracking_push .= ")";
				} else {
					$tracking_push .= ", {\n  'anonymize_ip': true\n  });";
				}
			} else {
				$custom_map_json = str_replace('"', "'", json_encode( $custom_map ) );
				$custom_event_json = str_replace('"', "'", json_encode( $custom_event ) );
				if ( empty( $anonim ) ) {
					$tracking_push .= ", {";
				} else {
					$tracking_push .= ", {\n  'anonymize_ip': true,";
				}
				$tracking_push .= "\n  'custom_map': " . $custom_map_json . "\n  });";
				$tracking_push .= "\n  gtag('event', 'aiwp_dimensions', " . $custom_event_json . ");\n";
			}
			$tracking_push .= "\n  if (window.performance) {
    var timeSincePageLoad = Math.round(performance.now());
    gtag('event', 'timing_complete', {
      'name': 'load',
      'value': timeSincePageLoad,
      'event_category': 'JS Dependencies'
    });
  }\n";
			$tracking_push .= "</script>";
		}

		if ( !$advga_tracktype ){
			$tracking="\n<!-- BEGIN Advanced Google Analytics - http://deconf.com/advanced-google-analytics-joomla/ -->\n".$tracking_0.$tracking_push."\n<!-- END Advanced Google Analytics -->\n\n";
		} else {
			$tracking_1="\nga('send', 'pageview');";
			$tracking="\n<!-- BEGIN Advanced Google Analytics - http://deconf.com/advanced-google-analytics-joomla/ -->\n".$tracking_events.$tracking_0.$tracking_push.$tracking_1.$tracking_2."\n<!-- END Advanced Google Analytics -->\n\n";
		}

		$buffer = $app->getBody();
		$buffer = preg_replace ("/<\/head>/", $tracking."\n</head>", $buffer);
		$app->setBody( $buffer );

	return;

 }

}