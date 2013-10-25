<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!empty($arResult["BIG_IMAGE"]["big"]))
{
	?><p><img src="<?=$arResult["BIG_IMAGE"]["big"]?>" width="600"/></p><?
}

$imgLeft = true;
$i = 0;
foreach($arResult["PARAGRAPH"] as $p)
{
	if(!($i == 0 && $arParams["SET_TITLE"] == "Y"))
	{
		?><h3><?=$p["TITLE"]?></h3><?
	}
	
	if(!empty($p["IMAGE"]["small"]))
	{
		$float = $imgLeft?"left":"right";
		?>
		<div style="float:<?=$float?>; overflow:hidden; padding:0 10px 10px 0;">
			<a href="<?=$p["IMAGE"]["big"]?>"><img src="<?=$p["IMAGE"]["small"]?>" width="180"/></a>
		</div>
		<?
	}
	foreach($p["TEXT"] as $t)
	{
		?><p><?=$t?></p><?
	}
	
	$imgLeft = !$imgLeft;
	$i++;
}

?>