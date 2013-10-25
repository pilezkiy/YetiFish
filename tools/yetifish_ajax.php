<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule('yeti.fish');
CModule::IncludeModule('iblock');

switch($_REQUEST["action"])
{
	case "newsGenerate":
		if(check_bitrix_sessid())
		{
			$arResult = CYetiFishGenerator::addNews(
				intval($_REQUEST["IBLOCK_ID"]),
				intval($_REQUEST["NEWS_COUNT"]),
				$_REQUEST["NEWS_THEME"],
				$_REQUEST["LANGUAGE"],
				$_REQUEST["TRANSLATE_SERVICE"],
				$_REQUEST["RANDOM_SECTIONS"] == "Y",
				$_REQUEST["PHOTO_TAGS"],
				$_REQUEST["SET_DETAIL_PHOTO"] == "Y",
				$_REQUEST["SET_PREVIEW_PHOTO"] == "Y",
				$_REQUEST["SET_SEPARATE_PHOTO"] == "Y"
				);
			$ids = array_keys($arResult["ITEMS"]);
			if(is_array($ids) && count($ids) > 0)
			{
				$rs = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>intval($_REQUEST["IBLOCK_ID"]),"ID"=>$ids),false,false,array("ID","IBLOCK_TYPE_ID","IBLOCK_ID","NAME","PREVIEW_TEXT","DETAIL_TEXT","DETAIL_PICTURE","PREVIEW_PICTURE"));
				while($item = $rs->fetch())
				{
					?>
					<div style="margin-bottom:20px;">
						<h2><a target="_blank" href="/bitrix/admin/iblock_element_edit.php?ID=<?=$item["ID"]?>&type=<?=$item["IBLOCK_TYPE_ID"]?>&IBLOCK_ID=<?=$item["IBLOCK_ID"]?>"><?=$item["NAME"]?></a></h2>
						<div>
						<?
						$dPicId = intval($item["DETAIL_PICTURE"]);
						if($dPicId > 0)
						{
							$path = CFile::GetPath($dPicId);
							$pResz = CFile::ResizeImageGet($dPicId, array('width'=>200, 'height'=>200), BX_RESIZE_IMAGE_PROPORTIONAL, true);                
							?><a target="_blank" href="<?=$path?>"><img src="<?=$pResz["src"]?>"/></a><?
						}
						$pPicId = intval($item["PREVIEW_PICTURE"]);
						if($pPicId > 0)
						{
							$path = CFile::GetPath($pPicId);
							$pResz = CFile::ResizeImageGet($pPicId, array('width'=>200, 'height'=>200), BX_RESIZE_IMAGE_PROPORTIONAL, true);                
							?><a target="_blank" href="<?=$path?>"><img src="<?=$pResz["src"]?>"/></a><?
						}
						?>
						</div>
						<p><?=$item["PREVIEW_TEXT"]?></p>
						<p><?=$item["DETAIL_TEXT"]?></p>
					</div>
					<hr/>
					<?
				}

			}
		}
		break;
}
?>