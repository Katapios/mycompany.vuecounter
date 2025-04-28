<?php
namespace MyCompany\VueCounter;

use Bitrix\Main\Localization\Loc;

class Menu
{
    public static function addAdminMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        global $APPLICATION;

        if (!is_object($APPLICATION) || $APPLICATION->GetGroupRight("example.vueadmin") < "R") {
            return;
        }

        $aModuleMenu[] = [
            "parent_menu" => "global_menu_services",
            "section"     => "mycompany_vuecounter",
            "sort"        => 100,
            "text"        => "CRM-сущности",
            "title"       => "CRM-сущности",
            "url"         => "/vuecounter/",
            "icon"        => "default_menu_icon",
            "page_icon"   => "default_page_icon",
            "items_id"    => "menu_mycompany_vuecounter",
            "items"       => []
        ];
    }
}