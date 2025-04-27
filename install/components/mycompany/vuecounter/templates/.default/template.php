<?php
// if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// // Подключаем собранный скрипт
// $asset = \Bitrix\Main\Page\Asset::getInstance();
// $asset->addJs('/local/modules/mycompany.vuecounter/js/vuecounter/app.js');
// 

// <div id="app">
//     <noscript>
//         <strong>Для работы этой страницы требуется включить JavaScript.</strong>
//     </noscript>
//     <!-- Можно сюда положить скелетон-заглушку -->
//     <div>Загрузка...</div>
// </div>

// <script>
//     document.addEventListener('DOMContentLoaded', function () {
//         const app = Vue.createApp({});
//         app.mount('#app');
//     });
// </script>

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs('/bitrix/js/mycompany.vuecounter/app.js');
Asset::getInstance()->addCss("/bitrix/css/mycompany.vuecounter/main.css");
?>
<div id="app"><!-- Корневой элемент Vue приложения --></div>
<noscript>Для работы этой страницы требуется включить JavaScript.</noscript>
