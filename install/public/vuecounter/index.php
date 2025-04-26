<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Vue Counter");

// Подключаем наш компонент
$APPLICATION->IncludeComponent(
    "mycompany:vuecounter", 
    "",
    [],
    false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
