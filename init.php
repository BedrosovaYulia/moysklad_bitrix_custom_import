<?php
if (isset($_GET['noinit']) && !empty($_GET['noinit']))
{
    $strNoInit = strval($_GET['noinit']);
    if ($strNoInit == 'N')
    {
        if (isset($_SESSION['NO_INIT']))
            unset($_SESSION['NO_INIT']);
    }
    elseif ($strNoInit == 'Y')
    {
        $_SESSION['NO_INIT'] = 'Y';
    }
}

if (!(isset($_SESSION['NO_INIT']) && $_SESSION['NO_INIT'] == 'Y'))
{
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/aspro.php"))
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/aspro.php");
	
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/moysklad_import.php"))
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/moysklad_import.php");
}

\Bitrix\Main\EventManager::getInstance()->addEventHandler('aspro.next', 'OnAsproRegionalityAddSelectFieldsAndProps', array('asproEvents', 'OnAsproRegionalityAddSelectFieldsAndProps'));


class asproEvents{
    public static function OnAsproRegionalityAddSelectFieldsAndProps(&$arSelect){
        $arSelect[] = 'PROPERTY_CURRENCY';
    }
}
?>