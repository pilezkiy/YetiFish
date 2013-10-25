<?
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"yeti.fish",
	array(
		'CYetiFishGenerator' => 'classes/general/generator.php',
	)
);

Class CYetiFish 
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			"parent_menu" => "global_menu_services",
			//"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => GetMessage("YETIFISH_MODULE_NAME"),
			"title" => '',
			"url" => "yeti.fish_generator.php?lang=".LANGUAGE_ID,
			"icon" => "yetifish_menu_icon",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);
		$aModuleMenu[] = $aMenu;
	}
}
?>
