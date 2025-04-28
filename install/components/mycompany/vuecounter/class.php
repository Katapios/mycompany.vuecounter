<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Crm\DealTable;

class VueCounterComponent extends CBitrixComponent implements \Bitrix\Main\Engine\Contract\Controllerable
{
    public function configureActions()
    {
        return [
            'getDeals' => [
                'prefilters' => [],
            ],
        ];
    }

    public function getDealsAction()
    {
        if (!Loader::includeModule('crm')) {
            return ['success' => false, 'error' => 'Модуль CRM не установлен'];
        }

        $deals = DealTable::getList([
            'select' => ['ID', 'TITLE', 'STAGE_ID', 'OPPORTUNITY', 'CURRENCY_ID'],
            'order' => ['ID' => 'DESC'],
            'limit' => 10,
        ])->fetchAll();

        return ['success' => true, 'deals' => $deals];
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}
