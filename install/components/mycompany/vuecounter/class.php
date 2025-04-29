<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Localization\Loc;
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
            'getDeals' => [],
            'getLeads' => [],
            'getContacts' => [],
            'getTasks' => [],
            'getProducts' => [],
            'deleteItem' => [],
            'deleteAllItems' => [],
        ];
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }

    private function checkAccess(): bool
    {
        global $USER;
        return $USER->IsAuthorized() && $USER->IsAdmin();
    }

    public function deleteItemAction(string $entityType, int $id): array
    {
        if (!$this->checkAccess()) {
            return ['success' => false, 'error' => 'Доступ запрещён'];
        }

        if ($id <= 0) {
            return ['success' => false, 'error' => 'Неверный ID'];
        }

        try {
            switch ($entityType) {
                case 'deals':
                    if (!Loader::includeModule('crm')) {
                        return ['success' => false, 'error' => 'Модуль CRM не подключен'];
                    }
                    $result = \Bitrix\Crm\DealTable::delete($id);
                    if (!$result->isSuccess()) {
                        return ['success' => false, 'error' => implode(', ', $result->getErrorMessages())];
                    }
                    break;

                case 'leads':
                    if (!Loader::includeModule('crm')) {
                        return ['success' => false, 'error' => 'Модуль CRM не подключен'];
                    }
                    $result = \Bitrix\Crm\LeadTable::delete($id);
                    if (!$result->isSuccess()) {
                        return ['success' => false, 'error' => implode(', ', $result->getErrorMessages())];
                    }
                    break;

                case 'contacts':
                    if (!Loader::includeModule('crm')) {
                        return ['success' => false, 'error' => 'Модуль CRM не подключен'];
                    }
                    $result = \Bitrix\Crm\ContactTable::delete($id);
                    if (!$result->isSuccess()) {
                        return ['success' => false, 'error' => implode(', ', $result->getErrorMessages())];
                    }
                    break;

                case 'tasks':
                    if (!Loader::includeModule('tasks')) {
                        return ['success' => false, 'error' => 'Модуль Tasks не подключен'];
                    }
                    $result = \Bitrix\Tasks\Internals\TaskTable::delete($id);
                    if (!$result->isSuccess()) {
                        return ['success' => false, 'error' => implode(', ', $result->getErrorMessages())];
                    }
                    break;

                case 'products':
                    if (!Loader::includeModule('iblock')) {
                        return ['success' => false, 'error' => 'Модуль Iblock не подключен'];
                    }
                    $success = \CIBlockElement::Delete($id);
                    if (!$success) {
                        return ['success' => false, 'error' => 'Ошибка удаления товара'];
                    }
                    break;

                default:
                    return ['success' => false, 'error' => 'Неверный тип сущности'];
            }

            return ['success' => true];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Ошибка сервера: ' . $e->getMessage()];
        }
    }


    public function deleteBatchItemsAction(string $entityType, int $batch = 100, int $offset = 0): array
    {
        if (!$this->checkAccess()) {
            return ['success' => false, 'error' => 'Доступ запрещён'];
        }

        try {
            $ids = [];

            switch ($entityType) {
                case 'deals':
                    if (!Loader::includeModule('crm')) {
                        return ['success' => false, 'error' => 'Модуль CRM не подключен'];
                    }
                    $ids = \Bitrix\Crm\DealTable::getList([
                        'select' => ['ID'],
                        'order' => ['ID' => 'ASC'],
                        'offset' => $offset,
                        'limit' => $batch,
                    ])->fetchAll();
                    break;

                case 'leads':
                    if (!Loader::includeModule('crm')) {
                        return ['success' => false, 'error' => 'Модуль CRM не подключен'];
                    }
                    $ids = \Bitrix\Crm\LeadTable::getList([
                        'select' => ['ID'],
                        'order' => ['ID' => 'ASC'],
                        'offset' => $offset,
                        'limit' => $batch,
                    ])->fetchAll();
                    break;

                case 'contacts':
                    if (!Loader::includeModule('crm')) {
                        return ['success' => false, 'error' => 'Модуль CRM не подключен'];
                    }
                    $ids = \Bitrix\Crm\ContactTable::getList([
                        'select' => ['ID'],
                        'order' => ['ID' => 'ASC'],
                        'offset' => $offset,
                        'limit' => $batch,
                    ])->fetchAll();
                    break;

                case 'tasks':
                    if (!Loader::includeModule('tasks')) {
                        return ['success' => false, 'error' => 'Модуль Tasks не подключен'];
                    }
                    $ids = \Bitrix\Tasks\Internals\TaskTable::getList([
                        'select' => ['ID'],
                        'order' => ['ID' => 'ASC'],
                        'offset' => $offset,
                        'limit' => $batch,
                    ])->fetchAll();
                    break;

                case 'products':
                    if (!Loader::includeModule('catalog') || !Loader::includeModule('iblock')) {
                        return ['success' => false, 'error' => 'Не подключены модули catalog или iblock'];
                    }
                    $catalogInfo = \Bitrix\Catalog\CatalogIblockTable::getList([
                        'filter' => ['=PRODUCT_IBLOCK_ID' => false],
                        'select' => ['IBLOCK_ID']
                    ])->fetch();

                    if (!$catalogInfo || !$catalogInfo['IBLOCK_ID']) {
                        return ['success' => false, 'error' => 'Инфоблок товаров не найден'];
                    }

                    $iblockId = $catalogInfo['IBLOCK_ID'];

                    $ids = \Bitrix\Iblock\ElementTable::getList([
                        'select' => ['ID'],
                        'filter' => ['=IBLOCK_ID' => $iblockId],
                        'order' => ['ID' => 'ASC'],
                        'offset' => $offset,
                        'limit' => $batch,
                    ])->fetchAll();
                    break;

                default:
                    return ['success' => false, 'error' => 'Неверный тип сущности'];
            }

            foreach ($ids as $item) {
                $id = (int)$item['ID'];
                if ($id <= 0) {
                    continue;
                }

                switch ($entityType) {
                    case 'deals':
                        \Bitrix\Crm\DealTable::delete($id);
                        break;
                    case 'leads':
                        \Bitrix\Crm\LeadTable::delete($id);
                        break;
                    case 'contacts':
                        \Bitrix\Crm\ContactTable::delete($id);
                        break;
                    case 'tasks':
                        \Bitrix\Tasks\Internals\TaskTable::delete($id);
                        break;
                    case 'products':
                        \CIBlockElement::Delete($id);
                        break;
                }
            }

            $processed = count($ids);

            return [
                'success' => true,
                'processed' => $processed,
                'nextOffset' => $processed > 0 ? $offset + $processed : null,
            ];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Ошибка сервера: ' . $e->getMessage()];
        }
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

}
