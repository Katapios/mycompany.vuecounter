<?php 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Счётчик Vue"); 

// Подключаем компонент с режимом ЧПУ
$APPLICATION->IncludeComponent(
    "mycompany:vuecounter", 
    "", 
    array(
       "SEF_MODE"    => "Y",
       "SEF_FOLDER"  => "/vuecounter/", 
       // другие параметры компонента (если есть)
    )
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
