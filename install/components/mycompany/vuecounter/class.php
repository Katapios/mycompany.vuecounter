<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\LeadTable;
use Bitrix\Crm\ContactTable;
use Bitrix\Tasks\Internals\TaskTable;
use Bitrix\Catalog\CatalogIblockTable;
use Bitrix\Iblock\ElementTable;

class VueCounterComponent extends CBitrixComponent implements Controllerable
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

    public function getDealsAction($offset = 0, $limit = 20)
    {
        if (!Loader::includeModule('crm')) {
            return ['success' => false, 'error' => 'Модуль CRM не установлен'];
        }

        $deals = DealTable::query()
            ->setSelect(['ID', 'TITLE', 'STAGE_ID', 'OPPORTUNITY', 'CURRENCY_ID'])
            ->setOrder(['ID' => 'DESC'])
            ->setOffset($offset)
            ->setLimit($limit)
            ->fetchAll();

        $totalCount = DealTable::getCount();

        return [
            'success' => true,
            'deals' => $deals,
            'total' => $totalCount,
        ];
    }

    public function getLeadsAction($offset = 0, $limit = 20)
    {
        if (!Loader::includeModule('crm')) {
            return ['success' => false, 'error' => 'Модуль CRM не установлен'];
        }

        $leads = LeadTable::query()
            ->setSelect(['ID', 'TITLE', 'STATUS_ID', 'OPPORTUNITY', 'CURRENCY_ID'])
            ->setOrder(['ID' => 'DESC'])
            ->setOffset($offset)
            ->setLimit($limit)
            ->fetchAll();

        $totalCount = LeadTable::getCount();

        return [
            'success' => true,
            'leads' => $leads,
            'total' => $totalCount,
        ];
    }

    public function getContactsAction($offset = 0, $limit = 20)
    {
        if (!Loader::includeModule('crm')) {
            return ['success' => false, 'error' => 'Модуль CRM не установлен'];
        }

        $contacts = ContactTable::query()
            ->setSelect(['ID', 'NAME', 'LAST_NAME', 'POST'])
            ->setOrder(['ID' => 'DESC'])
            ->setOffset($offset)
            ->setLimit($limit)
            ->fetchAll();

        $totalCount = ContactTable::getCount();

        return [
            'success' => true,
            'contacts' => $contacts,
            'total' => $totalCount,
        ];
    }

    public function getTasksAction($offset = 0, $limit = 20)
    {
        if (!Loader::includeModule('tasks')) {
            return ['success' => false, 'error' => 'Модуль Tasks не установлен'];
        }

        $tasks = TaskTable::query()
            ->setSelect(['ID', 'TITLE', 'STATUS', 'RESPONSIBLE_ID'])
            ->setOrder(['ID' => 'DESC'])
            ->setOffset($offset)
            ->setLimit($limit)
            ->fetchAll();

        $totalCount = TaskTable::getCount();

        return [
            'success' => true,
            'tasks' => $tasks,
            'total' => $totalCount,
        ];
    }

    public function getProductsAction($offset = 0, $limit = 20)
    {
        if (!Loader::includeModule('catalog') || !Loader::includeModule('iblock')) {
            return ['success' => false, 'error' => 'Не подключены модули catalog или iblock'];
        }

        $catalogInfo = CatalogIblockTable::getList([
            'filter' => ['=PRODUCT_IBLOCK_ID' => false],
            'select' => ['IBLOCK_ID']
        ])->fetch();

        if (!$catalogInfo || !$catalogInfo['IBLOCK_ID']) {
            return ['success' => false, 'error' => 'Инфоблок товаров не найден'];
        }

        $products = ElementTable::query()
            ->setSelect(['ID', 'NAME'])
            ->setFilter(['=IBLOCK_ID' => $catalogInfo['IBLOCK_ID']])
            ->setOrder(['ID' => 'DESC'])
            ->setOffset($offset)
            ->setLimit($limit)
            ->fetchAll();

        $totalCount = ElementTable::getCount([
            '=IBLOCK_ID' => $catalogInfo['IBLOCK_ID']
        ]);

        return [
            'success' => true,
            'products' => $products,
            'total' => $totalCount,
        ];
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}
