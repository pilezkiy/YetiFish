<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("yeti.fish");

$arResult = array();

$PARAGRAPH_CNT = intval($arParams["PARAGRAPH_CNT"]);
if($PARAGRAPH_CNT == 0)$PARAGRAPH_CNT = 3;

$ADD_BIG_IMAGE = ($arParams["ADD_BIG_IMAGE"] == "Y")?"Y":"N";
$ADD_PARAGRAPH_IMGS = ($arParams["ADD_PARAGRAPH_IMGS"] == "Y")?"Y":"N";
$LANG = $arParams["LANG"];
$langs = array("ru","en","de");
if(!in_array($LANG,$langs))
{
	$LANG = "ru";
}

if ($this->StartResultCache())
{
	$arResult["PARAGRAPH"] = array();
	while($PARAGRAPH_CNT > 0)
	{
		$arText = CYetiFishGenerator::getNewsTexts();
		$arResult["PARAGRAPH"][] = $arText;
		$PARAGRAPH_CNT--;
	}
	
	if($ADD_BIG_IMAGE == "Y")
	{
		$bigImg = CYetiFishGenerator::getPicture();
		$arResult["BIG_IMAGE"] = $bigImg;
	}
	
	if($ADD_PARAGRAPH_IMGS == "Y")
	{
		foreach($arResult["PARAGRAPH"] as $k=>$p)
		{
			$img = CYetiFishGenerator::getPicture();
			$arResult["PARAGRAPH"][$k]["IMAGE"] = $img;
		}
	}
	
	if($LANG!="ru")
	{
		foreach($arResult["PARAGRAPH"] as $k=>$p)
		{
			$arResult["PARAGRAPH"][$k]["TITLE"] = CYetiFishGenerator::yaTranslate($p["TITLE"],"ru",$LANG);
			foreach($p["TEXT"] as $kk=>$t)
			{
				$arResult["PARAGRAPH"][$k]["TEXT"][$kk] = CYetiFishGenerator::yaTranslate($t,"ru",$LANG);
			}
		}
	}
	
	$SITE = CSite::GetByID(SITE_ID)->fetch();
	foreach($arResult["PARAGRAPH"] as $k=>$p)
	{
		$arResult["PARAGRAPH"][$k]["TITLE"] = $GLOBALS["APPLICATION"]->ConvertCharset($p["TITLE"], 'UTF-8', $SITE["CHARSET"]);
		foreach($p["TEXT"] as $kk=>$t)
		{
			$arResult["PARAGRAPH"][$k]["TEXT"][$kk] = $GLOBALS["APPLICATION"]->ConvertCharset($t, 'UTF-8', $SITE["CHARSET"]);
		}
	}
	
	$this->IncludeComponentTemplate();
}


if($arParams["SET_TITLE"] == "Y")
{
	$firstParagraph = reset($arResult["PARAGRAPH"]);
	if(is_array($firstParagraph))
	{
		$APPLICATION->SetTitle($firstParagraph["TITLE"]);
	}
	
}

?>