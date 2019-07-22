<?php
if(!defined("LOG_FILENAME")) {
    define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/log.txt");
};

use Bitrix\Main\Web\HttpClient;
use \Bitrix\Main\Web\Json;
use Bitrix\Main\SystemException;

class cMainPromotions
{
    static $MODULE_ID = "promotions";

    /**
     * Хэндлер, отслеживающий изменения в инфоблоках
     * @param $arFields
     * @return bool
     */
    public function auth(string $apiKey, $authApiAddr)
    {
        $httpClient = new HttpClient();
        $httpClient->setHeader("Content-Type", "application/json", true);
        $httpClient->post("http://test.loc/auth", Json::encode(["api_key" => $apiKey]));
        try {
            $authResult = Json::decode($httpClient->getResult());
        } catch (SystemException $e) {
            echo $e->getMessage();
            AddMessage2Log($e->getMessage());
        }

        if ($authResult["result"] === "success") {
            return true;
        } else {
            return false;
        }

        return false;
    }

    private static function export($arFields)
    {
        if ($arFields["RESULT"]) {
            $apiKey = COption::GetOptionString("promotions", "API_KEY");
            $iblockId = COption::GetOptionInt("promotions", "IBLOCK_ID");
            $authApiAddr = COption::GetOptionString("promotions", "AUTH_API");
            $exportApiAddr = COption::GetOptionString("promotions", "EXPORT_API");
            $data = [];

            if (cMainPromotions::auth($apiKey, $authApiAddr)) {
                if ($iblockId == $arFields["IBLOCK_ID"]) {
                    $arFilter = [
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => $iblockId
                    ];
                    $items = CIBlockElement::GetList([], $arFilter);
                    while ($item = $items->fetch()) {
                        array_push($data, $item);
                    }

                    $httpClient = new HttpClient();
                    $httpClient->setHeader("Content-Type", "application/json", true);

                    if(!empty($data) && count($data) > 0) {
                        if ($httpClient->post($exportApiAddr, Json::encode(["data" => $data]))) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                return false;
            }
        }
    }

    public function onAfterElementAddHandler($arFields)
    {
        self::export($arFields);
    }

    public function onAfterElementUpdateHandler($arFields)
    {
        self::export($arFields);
    }
}
