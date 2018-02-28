<?

//YBWS

//Цель изменений при импорте из МойСклад:

//запрет изменения всех цен, кроме базовой
//Если входящая цена = 0, то сделать ее пустой строкой
//Запрет измененния раздела существующего товара. Новые по-умолчанию - в корень каталога



AddEventHandler("iblock", "OnBeforeIBlockElementAdd", array('YBWSMoySkladImport', 'onProductAdd'));
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", array('YBWSMoySkladImport', 'onProductUpdate'));

AddEventHandler("catalog", "OnBeforePriceAdd", array('YBWSMoySkladImport', 'onPriceAdd'));
AddEventHandler("catalog", "OnBeforePriceUpdate", array('YBWSMoySkladImport', 'onPriceUpdate'));
AddEventHandler("catalog", "OnBeforePriceDelete", array('YBWSMoySkladImport', 'onPriceDelete'));

class YBWSMoySkladImport {
	
	const ImportUserID=676;
	const CatalogID=14;
	
	function onProductAdd(&$arFields)
	{
		//define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/ybws_import.txt");
		//AddMessage2Log("NEW ADD", "NEW ADD");
		//AddMessage2Log($arFields, "arFields");
		
		//проверим пользователя:
		if($arFields['MODIFIED_BY']==self::ImportUserID && $arFields['IBLOCK_ID']==self::CatalogID)
		{
			//AddMessage2Log("Товар добавлен импортом", "USER_CHECK");	
			//новые товары сыпятся только в основоной раздела
			unset($arFields['IBLOCK_SECTION']);
			unset($arFields['IBLOCK_SECTION_ID']);
		}
		
	}
	
	function onProductUpdate(&$arFields)
	{
		//define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/ybws_import.txt");
		//AddMessage2Log("NEW UPDATE", "NEW UPDATE");
		//AddMessage2Log($arFields, "arFields");
		
		//проверим пользователя:
		if($arFields['MODIFIED_BY']==self::ImportUserID && $arFields['IBLOCK_ID']==self::CatalogID)
		{
			//AddMessage2Log("Товар изменен импортом", "USER_CHECK");
			
			//запрет изменения раздела товара
			unset($arFields['IBLOCK_SECTION']);
			unset($arFields['IBLOCK_SECTION_ID']);
		}
	}
	
	function onPriceAdd(&$arFields)
	{
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/ybws_import.txt");
		AddMessage2Log("NEW PRICE ADD", "NEW PRICE ADD");
		AddMessage2Log($arFields, "arFields");
		
		//проверим пользователя:
		global $USER;
		if($USER->GetID()==self::ImportUserID)
		{
			AddMessage2Log("Цена добавлена импортом", "USER_CHECK");	
			
			$BASE_PRICE=CCatalogGroup::GetBaseGroup();
			$BASE_PRICE=$BASE_PRICE['ID'];
			
			if(floatval($arFields['PRICE'])==0)
			{
				AddMessage2Log("Нулевая цена", "PRICE_CHECK");
				unset($arFields['PRICE']);
				unset($arFields['^PRICE']);
				unset($arFields['PRODUCT_ID']);
				global $APPLICATION;
				$APPLICATION->throwException("this user can`t create empty prices");
				return false;
			}
			else
			{
				//грузим только базовую цену...
				if($arFields['CATALOG_GROUP_ID']!=$BASE_PRICE)
				{
					//AddMessage2Log("Не базовая цена", "PRICE_CHECK");	
					//unset($arFields['PRICE']);
				}
			}
			

		}
		
	}
	
	function onPriceUpdate($ID, &$arFields)
	{
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/ybws_import.txt");
		AddMessage2Log("NEW PRICE UPDATE", "NEW PRICE UPDATE");
		AddMessage2Log($arFields, "arFields");
		
		//проверим пользователя:
		global $USER;
		if($USER->GetID()==self::ImportUserID)
		{
			AddMessage2Log("Цена изменена импортом", "USER_CHECK");	

			$BASE_PRICE=CCatalogGroup::GetBaseGroup();
			$BASE_PRICE=$BASE_PRICE['ID'];
			
			if(floatval($arFields['PRICE'])==0)
			{
				AddMessage2Log("Нулевая цена", "PRICE_CHECK");
				unset($arFields['PRICE']);
				unset($arFields['^PRICE']);
				CPrice::Delete($ID);
			}
			else
			{
				//обновляем только базовую цену...
				if($arFields['CATALOG_GROUP_ID']!=$BASE_PRICE)
				{
					AddMessage2Log("Не базовая цена", "PRICE_CHECK");	
					//return false;
					unset($arFields['PRICE']);
					unset($arFields['^PRICE']);
				}
			}
				

		}
		
	}
	
	function OnPriceDelete($ID)
	{
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/ybws_import.txt");
		AddMessage2Log("NEW PRICE DELETE", "NEW PRICE DELETE");
		AddMessage2Log($ID, "ID");
		//проверим пользователя:
		global $USER;
		if($USER->GetID()==self::ImportUserID)
		{
			AddMessage2Log("Цена удалена импортом", "USER_CHECK");	
			$arFields = CPrice::GetByID($ID);
			AddMessage2Log($arFields, "arFields");
			if(floatval($arFields['PRICE'])>0) 
			{
				AddMessage2Log("Не нулевая цена", "PRICE_CHECK");	
				return false;
			}
		}
	}
}

?>