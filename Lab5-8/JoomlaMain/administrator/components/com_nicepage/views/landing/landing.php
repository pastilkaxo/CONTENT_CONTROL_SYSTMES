<?php
/**
 * @package   Nicepage Website Builder
 * @author    Nicepage https://www.nicepage.com
 * @copyright Copyright (c) 2016 - 2019 Nicepage
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.framework');

?>
<!DOCTYPE html>
<html>
<head>
    <jdoc:include type="head" />
</head>
<body class="{bodyClass}" style="{bodyStyle}" {bodyDataBg}>
<!--np_message--><jdoc:include type="message" /><!--/np_message-->
<jdoc:include type="component" />
</body>
</html>