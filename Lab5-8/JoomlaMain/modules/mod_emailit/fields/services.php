<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
// The class name must always be the same as the filename (in camel case)
class JFormFieldServices extends JFormField {
 
	//The field class must know its own type through the variable $type.
	protected $type = 'Services';
 
	public function getLabel() {
            return parent::getLabel();
	}
 
	public function getInput() {
		JLoader::import( 'joomla.version' );
		$version = new JVersion();
		$joomlaVersion = isset($version->RELEASE)?$version->RELEASE:$version->getShortVersion();
		
		if (version_compare( $joomlaVersion, '2.5', '<=')) {
			if(JFactory::getApplication()->get('jquery') !== true) {
				JHtml::script('http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js');
				JFactory::getApplication()->set('jquery', true);
			}
			JHtml::script(JURI::root().'modules/mod_emailit/js/script2.5.js');
		} else {
			JHtml::_('jquery.framework');
			JHtml::script(JURI::root().'modules/mod_emailit/js/script.js');
		}	
		JHtml::script(JURI::root().'modules/mod_emailit/js/jquery-ui.min.js');
		JHtml::stylesheet(JURI::root().'modules/mod_emailit/css/jquery-ui.min.css');
		JHtml::stylesheet(JURI::root().'modules/mod_emailit/css/style.css');
		return '<div class="out-of-the-box">'.
				'<ul id="social_services" class="large"></ul>'.
				'<div class="services_buttons">'.
					'<a style="display:none;" class="social_services_default_btn">Restore settings</a>'.
					'<a style="display:none;" class="uncheck_all_btn">Clear all</a>'.
					'<a id="servises_customize_btn">Customize...</a> '.
				'</div>'.
				'<div class="message_good" style="display:none">Select your buttons</div>'.
				'<div class="filterinput">'.
					'<input placeholder="Search for services" data-type="search" id="filter-form-input-text">'.
				'</div>'.
				'<div id="servicess" class="large">'.
				'</div>'.
				'<input  id="'.$this->id.'" name="'.$this->name.'" value="' . $this->value . '" type="hidden"/>'.
			'</div>';
	}
}
