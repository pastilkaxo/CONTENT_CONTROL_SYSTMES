<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
// The class name must always be the same as the filename (in camel case)
class JFormFieldFollows extends JFormField {
 
	//The field class must know its own type through the variable $type.
	protected $type = 'Follows';
 
	public function getLabel() {
            return parent::getLabel();
	}
 
	public function getInput() {
            return '<ul id="social_services_follow" class="large">'.
                    '<input  id="'.$this->id.'" name="'.$this->name.'" value="' . $this->value . '" type="hidden"/></ul>';
	}
}
