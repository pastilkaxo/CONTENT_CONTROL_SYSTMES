<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$item    = $displayData['data'];
$display = $item->text;
$app = Factory::getApplication();

switch ((string) $item->text)
{
    case Text::_('JLIB_HTML_START'):
    case Text::_('JPREV'):
    case Text::_('JNEXT'):
    case Text::_('JLIB_HTML_END'):
        $aria = Text::sprintf('JLIB_HTML_GOTO_POSITION', strtolower($item->text));
        break;
    default:
        $aria = Text::sprintf('JLIB_HTML_GOTO_PAGE', strtolower($item->text));
        break;
}

if ($displayData['active'])
{
    if ($item->base > 0)
    {
        $limit = 'limitstart.value=' . $item->base;
    }
    else
    {
        $limit = 'limitstart.value=0';
    }

    $class = 'active';

    if ($app->isClient('administrator'))
    {
        $link = 'href="#" onclick="document.adminForm.' . $item->prefix . $limit . '; Joomla.submitform();return false;"';
    }
    elseif ($app->isClient('site'))
    {
        $link = 'href="' . $item->link . '"';
    }
}
else
{
    $class = (property_exists($item, 'active') && $item->active) ? 'active' : 'disabled';
}

?>
<?php if ($displayData['active']) : ?>
    <a aria-label="<?php echo $aria; ?>" <?php echo $link; ?> class="page-link"><?php echo $display; ?></a>
<?php elseif (isset($item->active) && $item->active) : ?>
    <?php $aria = Text::sprintf('JLIB_HTML_PAGE_CURRENT', strtolower($item->text)); ?>
    <span aria-current="true" aria-label="<?php echo $aria; ?>" class="page-link"><?php echo $display; ?></span>
<?php else : ?>
    <span class="page-link" aria-hidden="true"><?php echo $display; ?></span>
<?php endif; ?>
