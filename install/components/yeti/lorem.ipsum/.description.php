<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("YLI_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("YLI_COMPONENT_DESCR"),
	"ICON" => "/images/include.gif",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "yeti_lorem_ipsum",
			"NAME" => GetMessage("YLI_GROUP_NAME"),
		),
	),
);
?>