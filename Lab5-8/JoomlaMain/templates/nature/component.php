<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$app    = Factory::getApplication();
$doc    = $app->getDocument();
$this->language  = $doc->language;
$this->direction = $doc->direction;

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <jdoc:include type="head" />
</head>
<body class="<?php echo $this->direction === 'rtl' ? 'rtl' : ''; ?>">
<jdoc:include type="message" />
<jdoc:include type="component" />
</body>
</html>
