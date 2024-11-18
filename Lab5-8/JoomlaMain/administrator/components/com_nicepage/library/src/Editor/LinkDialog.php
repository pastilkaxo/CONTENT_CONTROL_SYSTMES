<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

namespace NP\Editor;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use \JLoader;
use NP\Utility\Utility;

JLoader::register('Nicepage_Data_Mappers', JPATH_ADMINISTRATOR . '/components/com_nicepage/tables/mappers.php');

class LinkDialog
{
    /**
     * Add link dialog
     */
    public function addLinkDialog()
    {
        $categoryList = json_encode($this->getCategoryList());
        $mediaFiles = json_encode(Utility::getMediaFiles());
        if (!$mediaFiles) {
            $mediaFiles = '[]';
        }
        $allowedExtensions = json_encode(Utility::getAllowedExtensions());
        $maxRequestSize = Utility::getMaxRequestSize();
        $editLinkUrl = Utility::getAdminUrl() . '/index.php?option=com_content&view=articles&layout=modal&tmpl=component';
        $uploadFileLink = Utility::getAdminUrl() . '/index.php?option=com_nicepage&task=actions.uploadFile';
        $customUrlOptions = $this->getCustomOptions();
        $script1 = <<<EOF
        <script>
            window.phpVars = {
                'editLinkUrl': '$editLinkUrl',
                'customUrlOptions': '$customUrlOptions', 
                'maxRequestSize': $maxRequestSize,
                'uploadFileLink': '$uploadFileLink',
                'mediaFiles': $mediaFiles,
                'categoryList': $categoryList,
                'allowedExtensions': $allowedExtensions,
            } 
        </script>   
EOF;
        $script2 = '<script src="' . Utility::getAdminUrl() . '/components/com_nicepage/assets/js/link-dialog.js"></script>';
        Factory::getDocument()->addCustomTag($script1 . $script2);
    }

    /**
     * Get custom options
     *
     * @return mixed
     */
    public function getCustomOptions() {
        $customUrlOptions = $this->getDialogStyles() . $this->getDialogHtml();
        return call_user_func('base' . '64_encode', $customUrlOptions);
    }

    /**
     * Get link dialog styles
     *
     * @return string
     */
    public function getDialogStyles()
    {
        return <<<STYLES
<style>
.custom-url-options {
    width:100%;
}
.custom-url-options label{
    width: 55px;
    display: inline-block;
}
.custom-url-options input[type=text]{
    width: 350px;
}

.custom-url-options input {
    margin:0 0 9px 0;
}

.custom-url-options:after {
    content: "";
    clear: both;
    display: table;
}
.link-destination,
.target-option {
    margin-left: 70px;
}
.link-destination {
    margin-top: 4px;
}
.link-destination input,
.target-option input {
    margin-right: 10px;
    margin-top: 0px;
}
.link-destination .link-destination-label {
    width: auto;
    display: inline-block;
    vertical-align: top;
    margin-left: -80px;
    margin-top: 4px;
    width: 76px;
}
.link-destination ul {
    list-style-type: none;
    margin-left: 0px;
    margin-top: 4px;
    display: inline-block;
}

.link-destination label {
    width: 125px;
}

.list-container {
    background-color: #F5F5F5;
    border: 1px solid #BFBFBF;
    padding: 4px 6px 4px 10px;
    margin: 10px auto auto 0px;
    height: 300px;
    width: 100%;
    overflow: auto;
}

.anchors-list, .files-list, .dialogs-list, .category-list, .products-list {
    list-style-type: none;
}

.anchors-list li, .files-list li, .dialogs-list li, .category-list li, .products-list li {
    cursor: pointer;
}

.anchors-list li:hover, .files-list li:hover, .dialogs-list li:hover, .category-list li:hover, .products-list li:hover,
.anchors-list li.selected, .files-list li.selected, .dialogs-list li.selected, .category-list li.selected, .products-list li.selected {
    background-color: #e5f2ff;
}

.anchors-list li a, .files-list li a, .dialogs-list li a, .category-list li a, .products-list li a {
    color: #666;
}

#upload-btn {
    text-decoration: none;
}

a.disabled {
    pointer-events: none;
    color: #999999;
}

/* Dropdown Button */

.page-option {
    margin-top: 10px;
}
.dropbtn {
  /*background-color: #4CAF50;
  color: white;
  font-size: 16px;*/
  background-color: transparent;
  padding: 10px;
  border: none;
  cursor: pointer;
  outline: none;
}

/* Dropdown button on hover & focus */
.dropbtn:hover, .dropbtn:focus {
  /*background-color: #3e8e41;*/
}

.dropbtn-caret {
    margin-left: 5px;
    margin-top: -1px;
    border-top-color: #898989;
    display: inline-block;
    width: 0;
    height: 0;
    vertical-align: middle;
    border-top: 4px dashed;
    border-top: 4px solid \9;
    border-right: 4px solid transparent;
    border-left: 4px solid transparent;
}

/* The search field */
#myInput {
  box-sizing: border-box;
  background-image: url('searchicon.png');
  background-position: 14px 12px;
  background-repeat: no-repeat;
  /*font-size: 16px;*/
  padding: 14px 20px 12px 15px;
  border: none;
  border-bottom: 1px solid #ddd;
}

/* The search field when it gets focus/clicked on */
#myInput:focus {
/*outline: 3px solid #ddd;*/
}

/* The container <div> - needed to position the dropdown content */
.dropdown {
  position: relative;
  display: inline-block;
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f6f6f6;
  min-width: 230px;
  border: 1px solid #ddd;
  z-index: 1;
}

/* Links inside the dropdown */
.dropdown-content a {
  color: black;
  padding: 6px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a.selected {
  background:#d4d2d2;
}
/* Change color of dropdown links on hover */
.dropdown-content a:hover {background-color: #f1f1f1}

/* Show the dropdown menu (use JS to add this class to the .dropdown-content container when the user clicks on the dropdown button) */
.show {display:block;}

.a-list {
  max-height:180px;
  overflow-y: auto;
}

.page-dropdown {
    display: inline-block;
}
</style>
STYLES;
    }

    /**
     * Get link dialog html
     *
     * @return string
     */
    public function getDialogHtml()
    {
        return <<<HTML
<div class="custom-url-options">
    <div style="float:left;width:90%">
        <div style="float:left;width:60%">
            <div class="caption-option"><label for="caption">{{caption}}</label><input type="text" name="caption" value="" /></div>
            
            <div class="url-option"><label for="url">{{url}}</label><input type="text" name="url" value="" /></div>
            <div class="target-option"><input type="checkbox" name="target" />{{target}}</div>
         
            <div class="page-option">
                <label for="url">Page</label>
                <div class="page-dropdown">
                    <button class="dropbtn"><span class="dropbtn-value">[Current page]</span><span class="dropbtn-caret"></span></button>
                    <div id="myDropdown" class="dropdown-content">
                        <input type="text" autocomplete="off" placeholder="Search.." id="myInput">
                        <div class="a-list">
                            <a href="#" class="selected">[Current page]</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="phone-option"><label for="phone">{{phoneLink}}</label><input type="tel" name="phone" value="" /></div>
            
            <div class="email-option"><label for="phone">{{emailLink}}</label><input type="email" name="email" value="" /></div>
            <div class="email-subject-option"><label for="phone">{{emailSubject}}</label><input type="text" name="subject" value="" /></div>
        </div>
        
        <div style="float:left;width:40%">
            <div class="link-destination hidden">
            <div class="link-destination-label">{{Destination}}</div>
                <ul>
                    <li><input type="radio" name="link-destination" id="page-link" value="page"/><label for='page-link'>{{pageLink}}</lable></li>
                    <li><input type="radio" name="link-destination" id="anchor-link" value="section"/><label for='anchor-link'>{{anchorLink}}</lable></li>
                    <li>
                        <input type="radio" name="link-destination" id="file-link" value="file"/><label for='file-link'>{{fileLink}}</lable>
                        <input type="file" name="file" id="file-field" multiple="true" style="display: none"/>
                        <a href="#" id="upload-btn">{{upload}}</a>
                    </li>
                    <li><input type="radio" name="link-destination" id="phone-link" value="phone"/><label for='phone-link'>{{phoneLink}}</lable></li>
                    <li><input type="radio" name="link-destination" id="email-link" value="email"/><label for='email-link'>{{emailLink}}</lable></li>
                    <li><input type="radio" name="link-destination" id="dialog-link" value="dialog"/><label for='dialog-link'>{{dialogLink}}</lable></li>
                    <li><input type="radio" name="link-destination" id="blog-link" value="blog"/><label for='blog-link'>{{blogLink}}</lable></li>        
                    <li><input type="radio" name="link-destination" id="product-link" value="product"/><label for='product-link'>{{productLink}}</lable></li>            
                </ul>
            </div>      
        </div>
    </div>
    <div style="float:right">
        <button type="button" class="btn btn-success" id="save-options">Save</button>
    </div>
</div>
<div class="list-container hidden">
    <ul class="anchors-list hidden" id="anchors-list"></ul>
    <ul class="files-list hidden" id="files-list"></ul>
    <ul class="dialogs-list hidden" id="dialogs-list"></ul>
    <ul class="category-list hidden" id="category-list"></ul>
    <ul class="products-list hidden" id="products-list"></ul>
</div>
HTML;
    }

    /**
     * Get category list
     *
     * @return array
     */
    public function getCategoryList() {
        $result = array();
        $categories = \Nicepage_Data_Mappers::get('category');
        $categoryList = $categories->find(array('extension' => 'com_content'));
        foreach ($categoryList as & $categoryListItem) {
            array_push(
                $result,
                array(
                    'url' => 'index.php?option=com_content&view=category&layout=blog&id=' . $categoryListItem->id,
                    'title' => $categoryListItem->title
                )
            );
        }
        return $result;
    }
}
