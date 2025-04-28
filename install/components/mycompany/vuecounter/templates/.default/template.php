<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

// Подключаем CSS
Asset::getInstance()->addCss($templateFolder . '/dist/assets/main.css');

// Подключаем JS
Asset::getInstance()->addJs($templateFolder . '/dist/assets/main.js'); 

?>
<div id="app"></div>
