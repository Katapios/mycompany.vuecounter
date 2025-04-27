<?php
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs('https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js'); // <- добавляем Vue
Asset::getInstance()->addJs($this->addExternalJS("/local/modules/mycompany.vuecounter/install/js/mycompany.vuecounter/app.js")); // <- потом свой собранный app.js
?>
<div id="app"></div>
