<?php

CModule::IncludeModule("promotions");
global $DBType;
$arClasses=array(
    'cMainPromotions'=>'classes/general/cMainPromotions.php'
);
CModule::AddAutoloadClasses("promotions", $arClasses);