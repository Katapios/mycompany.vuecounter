<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

use Bitrix\Main\UI\Extension;
Extension::load("main.ui.grid"); // Подключаем Bitrix UI Grid
Extension::load("ui.buttons"); // Кнопки для грида

// Подключаем CSS
Asset::getInstance()->addCss($templateFolder . '/dist/assets/main.css');

// Подключаем JS
Asset::getInstance()->addJs($templateFolder . '/dist/assets/main.js'); 

?>
<div id="app"></div>
