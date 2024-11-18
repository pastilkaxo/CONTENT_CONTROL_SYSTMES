<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

extract($displayData);

?>
<div class="nr-app-sidebar">
	<div class="nav">
		<ul>
			<?php foreach ($items as $key => $item) { 

				$class = '';

				if (isset($item['view']))
				{
					if (in_array($view, explode(",", $item['view'])))
					{
						$class = 'active';
					}
				}

				$target  = isset($item['target']) ? '_' . $item['target'] : "_self";
				$isModal = $item['url'] == '#proOnly';

				?>
			<li class="<?php echo $class; ?>">
				<a href="<?php echo $item['url']; ?>" target="<?php echo $target; ?>" <?php echo $isModal ? 'data-pro-only="' . Text::_($item['label']) . '"' : '' ?>>
					<span class="icon icon-<?php echo $item['icon'] ?>"></span>
					<span class="nav-label"><?php echo Text::_($item['label']); ?></span>
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>