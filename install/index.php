<?php
use Bitrix\Main\ModuleManager;

class mycompany_vuecounter extends CModule
{
    public $MODULE_ID = "mycompany.vuecounter";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;


    public function __construct()
    {
        // Подключаем информацию о версии
        $arModuleVersion = [];
        include __DIR__."/version.php";
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        // Заполняем информацию о модуле
        $this->MODULE_NAME = GetMessage("MYCOMPANY_VUECOUNTER_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("MYCOMPANY_VUECOUNTER_MODULE_DESC");
        $this->PARTNER_NAME = GetMessage("MYCOMPANY_VUECOUNTER_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("MYCOMPANY_VUECOUNTER_PARTNER_URI");
    }


    public function DoInstall()
    {
        global $APPLICATION;
        // Копируем файлы компонента, публичные страницы, JS/CSS
        $this->InstallEvents();
        $this->InstallFiles();
        $this->InstallPublic();
        // Регистрация модуля в системе
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        // Можно сразу подключить модуль для регистрации автозагрузки
        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);
        // (Если есть операции с БД или события, вызвать $this->InstallDB(), InstallEvents())
        $APPLICATION->IncludeAdminFile(
            GetMessage("MYCOMPANY_VUECOUNTER_INSTALL_TITLE"),
            __DIR__."/step.php"
        );
    }


    public function DoUninstall()
    {
        global $APPLICATION;
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        // Удаляем модуль из системы
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
        // (Если были события или таблицы, вызвать UnInstallEvents(), UnInstallDB())
        $APPLICATION->IncludeAdminFile(
            GetMessage("MYCOMPANY_VUECOUNTER_UNINSTALL_TITLE"),
            __DIR__."/unstep.php"
        );
        // Удаляем созданные файлы и страницы
    }


    function InstallFiles()
    {
        // Убеждаемся, что папка назначения существует
        CheckDirPath($_SERVER["DOCUMENT_ROOT"]."/local/components/mycompany/");
        // Копируем компонент (рекурсивно, с перезаписью)
        CopyDirFiles(__DIR__."/components", $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);
        return true;
    }

    function InstallPublic()
    {
        // Предполагаем, что папка /vuecounter/ ещё не существует
        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/vuecounter/"))
        {
            CopyDirFiles(__DIR__."/public/ru/vuecounter", $_SERVER["DOCUMENT_ROOT"]."/vuecounter", true, true);
        }
        return true;
    }

        function UnInstallFiles()
    {
        DeleteDirFilesEx('/local/components/mycompany/vuecounter/');
        DeleteDirFilesEx('/vuecounter/');
    }

        public function InstallEvents()
    {
        // Регистрация обработчика меню
        RegisterModuleDependences(
            "main",
            "OnBuildGlobalMenu",
            $this->MODULE_ID,
            "Mycompany\\Vuecounter\\Menu",
            "addAdminMenu"
        );
        return true;
    }

    public function UnInstallEvents()
    {
        // Удаление обработчика меню
        UnRegisterModuleDependences(
            "main",
            "OnBuildGlobalMenu",
            $this->MODULE_ID,
            "Mycompany\\Vuecounter\\Menu",
            "addAdminMenu"
        );
        return true;
    }



}
?>