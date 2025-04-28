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
        'getDeals' => ['prefilters' => []],
        'getLeads' => ['prefilters' => []],
        'getContacts' => ['prefilters' => []],
        'getTasks' => ['prefilters' => []],
        'getProducts' => ['prefilters' => []],
    ];
    }

    public function getDealsAction()
    {
        if (!\Bitrix\Main\Loader::includeModule('crm')) {
            return ['success' => false, 'error' => 'Модуль CRM не установлен'];
        }

        $deals = \Bitrix\Crm\DealTable::getList([
            'select' => ['ID', 'TITLE', 'STAGE_ID', 'OPPORTUNITY', 'CURRENCY_ID'],
            'order' => ['ID' => 'DESC'],
            'limit' => 10,
        ])->fetchAll();

        return ['success' => true, 'deals' => $deals];
    }


    public function getLeadsAction()
    {
        if (!\Bitrix\Main\Loader::includeModule('crm')) {
            return ['success' => false, 'error' => 'Модуль CRM не установлен'];
        }

        $leads = \Bitrix\Crm\LeadTable::getList([
            'select' => ['ID', 'TITLE', 'STATUS_ID', 'OPPORTUNITY', 'CURRENCY_ID'],
            'order' => ['ID' => 'DESC'],
            'limit' => 10,
        ])->fetchAll();

        return ['success' => true, 'leads' => $leads];
    }

    public function getContactsAction()
    {
        if (!\Bitrix\Main\Loader::includeModule('crm')) {
            return ['success' => false, 'error' => 'Модуль CRM не установлен'];
        }

        $contacts = \Bitrix\Crm\ContactTable::getList([
            'select' => ['ID', 'NAME', 'LAST_NAME', 'POST'],
            'order' => ['ID' => 'DESC'],
            'limit' => 10,
        ])->fetchAll();

        return ['success' => true, 'contacts' => $contacts];
    }

    public function getTasksAction()
    {
        if (!\Bitrix\Main\Loader::includeModule('tasks')) {
            return ['success' => false, 'error' => 'Модуль Tasks не установлен'];
        }

        $tasks = \Bitrix\Tasks\Internals\TaskTable::getList([
            'select' => ['ID', 'TITLE', 'STATUS', 'RESPONSIBLE_ID'],
            'order' => ['ID' => 'DESC'],
            'limit' => 10,
        ])->fetchAll();

        return ['success' => true, 'tasks' => $tasks];
    }

    public function getProductsAction()
    {
        if (!\Bitrix\Main\Loader::includeModule('catalog') || !\Bitrix\Main\Loader::includeModule('iblock')) {
            return ['success' => false, 'error' => 'Не подключены модули catalog или iblock'];
        }

        // Найдем инфоблок товаров
        $iblockId = \Bitrix\Catalog\CatalogIblockTable::getList([
            'filter' => ['=PRODUCT_IBLOCK_ID' => false],
            'select' => ['IBLOCK_ID']
        ])->fetch()['IBLOCK_ID'];

        if (!$iblockId) {
            return ['success' => false, 'error' => 'Инфоблок товаров не найден'];
        }

        // Теперь получаем элементы инфоблока
        $products = \Bitrix\Iblock\ElementTable::getList([
            'filter' => ['=IBLOCK_ID' => $iblockId],
            'select' => ['ID', 'NAME'],
            'order' => ['ID' => 'DESC'],
            'limit' => 10,
        ])->fetchAll();

        return ['success' => true, 'products' => $products];
    }


    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}
