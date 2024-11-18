<?php
use Joomla\CMS\Factory;
$document = Factory::getApplication()->getDocument();
ob_start();
?>
    <footer class="u-align-center u-clearfix u-container-align-center u-footer u-grey-80 u-footer" id="sec-9869">
  <div class="u-clearfix u-sheet u-sheet-1">
    <p class="u-small-text u-text u-text-variant u-text-1">Пример текста. Кликните, чтобы выбрать элемент "Текст".</p>
  </div>
</footer>
<?php
ThemeHelper::getInstance()->footerHtml = ob_get_clean();
ob_start();
?>
    
<?php
ThemeHelper::getInstance()->footerExtraHtml = ob_get_clean();

