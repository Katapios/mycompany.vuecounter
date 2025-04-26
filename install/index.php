<?php
use Bitrix\Main\ModuleManager;

class mycompany_vuecounter extends CModule
{
    public function __construct()
    {
        $this->MODULE_ID = 'mycompany.vuecounter';
        $this->MODULE_NAME = 'Vue Counter';
        $this->MODULE_DESCRIPTION = 'Модуль с Vue 3 счетчиком';
        $this->PARTNER_NAME = 'MyCompany';
        $this->PARTNER_URI = 'https://mycompany.example.com';
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        CopyDirFiles(
            __DIR__ . "/public",
            $_SERVER["DOCUMENT_ROOT"],
            true,
            true
        );
    }

    public function DoUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
?>