<?php

Class promotions extends CModule
{
    public $MODULE_ID = "promotions";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;

    function promotions()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = "Promotions Exporter";
        $this->MODULE_DESCRIPTION = "Модуль для экспорта акций";
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        // Install events
        RegisterModuleDependences("iblock",
            "OnAfterIBlockElementAdd",
            "promotions",
            "cMainPromotions",
            "onAfterElementAddHandler");

        RegisterModuleDependences("iblock",
            "OnAfterIBlockElementUpdate",
            "promotions",
            "cMainPromotions",
            "onAfterElementUpdateHandler");

        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Установка модуля promotions", $DOCUMENT_ROOT . "/local/modules/promotions/install/step.php");
        return true;
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        UnRegisterModuleDependences("iblock",
            "OnAfterIBlockElementAdd",
            "promotions",
            "cMainPromotions",
            "onAfterElementAddHandler");

        UnRegisterModuleDependences("iblock",
            "OnAfterIBlockElementUpdate",
            "promotions",
            "cMainPromotions",
            "onAfterElementUpdateHandler");

        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля promotions", $DOCUMENT_ROOT . "/local/modules/promotions/install/unstep.php");
        return true;
    }
}