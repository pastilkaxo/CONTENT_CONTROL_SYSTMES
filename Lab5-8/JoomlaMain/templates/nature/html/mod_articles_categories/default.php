<?php
defined('_JEXEC') or die;
?>
<div class="u-categories u-categories-vertical">
     
        <?php require JModuleHelper::getLayoutPath('mod_articles_categories', $params->get('layout', 'default') . '_items'); ?>
        
        
    
    <svg style="display:none;">
        <g id="icon-categories-open">
            <polygon points="12,10 9,6 15,6 "></polygon>
        </g>
        <g id="icon-categories-closed">
            <polygon points="14,8 10,11 10,5 "></polygon>
        </g>
        <g id="icon-categories-leaf">
            <polygon points="14,8 10,11 10,5 "></polygon>
        </g>
    </svg>
</div>
