<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arLang = arraY(
	"ru"=>"RU",
	"en"=>"EN",
	"de"=>"DE",
);

$arComponentParameters = array(
	"PARAMETERS" => array(
		"PARAGRAPH_CNT" => array(
			"NAME" => GetMessage("YLI_PARAGRAPH_CNT"), 
			"TYPE" => "NUMBER",
			"DEFAULT" => "3",
		),
		"ADD_BIG_IMAGE" => array(
			"NAME" => GetMessage("YLI_ADD_BIG_IMAGE"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ADD_PARAGRAPH_IMGS" => array(
			"NAME" => GetMessage("YLI_ADD_PARAGRAPH_IMGS"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		
		"LANG" => array(
			"NAME" => GetMessage("YLI_LANG"), 
			"TYPE" => "LIST",
			"VALUES" => $arLang,
			"DEFAULT" => "ru",
		),
		"SET_TITLE" => array(
			"NAME" => GetMessage("YLI_SET_TITLE"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"CACHE_TYPE"  =>  "A",
        "CACHE_TIME"  =>  3600,
	),
);
?>