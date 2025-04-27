<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loader::registerAutoLoadClasses("mycompany.vuecounter", [
    // Пример: если есть класс таблицы счетчиков
    //"Mycompany\\Vuecounter\\CounterTable" => __DIR__."/lib/CounterTable.php",
]);