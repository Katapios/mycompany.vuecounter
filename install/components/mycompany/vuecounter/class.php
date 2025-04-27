<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Component\BaseUfComponent;

class MyCompanyVueCounterComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}
