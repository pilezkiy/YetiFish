<?
$MID = "yeti.fish";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
CUtil::InitJSCore(array('jquery'));
$APPLICATION->SetTitle(GetMessage("YETIFISH_GENERATOR_TITLE"));
//$APPLICATION->AddHeadScript("/bitrix/js/".$MID."/script.js");
$APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/js/".$MID."/script.js\"></script>");

$aTabs = array(
	array("DIV" => "yf_generator", "TAB" => GetMessage("YETIFISH_GENERATOR_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("YETIFISH_GENERATOR_TAB_TITLE")),
	array("DIV" => "yf_options", "TAB" => GetMessage("YETIFISH_OPTIONS_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("YETIFISH_OPTIONS_TAB_TITLE")),
);

CModule::IncludeModule($MID);

$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");


if(!empty($_REQUEST["YF_OPTIONS_SAVE"]))
{
	COption::SetOptionString($MID,"FLICKR_APIKEY",$_REQUEST["FLICKR_APIKEY"]);
}


?>
<form name="find_form" method="POST" action="<?echo $APPLICATION->GetCurPage()?>?">
<?=bitrix_sessid_post()?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
if(CModule::IncludeModule("iblock"))
{
	?>
	<tr>
	<td>
		<?
		$ibList = array();
		$rsIBType = CIBlockType::GetList();
		while($arIBType = $rsIBType->fetch())
		{
			if($arIBType = CIBlockType::GetByIDLang($arIBType["ID"], LANGUAGE_ID))
			{
				$ibList[$arIBType["IBLOCK_TYPE_ID"]] = array(
					"IB_TYPE_NAME" => $arIBType["NAME"],
					"ITEMS" => array(),
				);
			}
		}
		
		$rsIb = CIBlock::GetList();
		while($ib = $rsIb->fetch())
		{
			$ibList[$ib["IBLOCK_TYPE_ID"]]["ITEMS"][$ib["ID"].""] = $ib;
		}
		?>
		<table>
		<tr>
			<td><label><?=GetMessage("YETIFISH_SELECT_IB")?>:</label></td>
			<td>
			<select name="IBLOCK_ID" id="yetiFishIblock">
				<?
				foreach($ibList as $ibt)
				{
					if(count($ibt["ITEMS"]) > 0)
					{
						?>
						<optgroup label="<?=$ibt["IB_TYPE_NAME"]?>">
							<?
							foreach($ibt["ITEMS"] as $ib)
							{
								?><option value="<?=$ib["ID"]?>"><?=$ib["NAME"]?></option><?
							}
							?>
						</optgroup> 
						<?
					}
				}
				?>
			</select>
			</td>
		</tr>
		<tr>
			<td><label><?=GetMessage("YETIFISH_NEWS_COUNT")?>:</label></td>
			<td><input name="NEWS_COUNT" id="yetiFishNewsCount" value="5" size="3"/></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="checkbox" name="SET_DETAIL_PHOTO" id="yetiFishSetDetailPhoto" value="Y"/><label for="yetiFishSetDetailPhoto"><?=GetMessage("YETIFISH_SET_DETAIL_PHOTO")?></label></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="checkbox" name="SET_PREVIEW_PHOTO" id="yetiFishSetPreviewPhoto" value="Y"/><label for="yetiFishSetPreviewPhoto"><?=GetMessage("YETIFISH_SET_PREVIEW_PHOTO")?></label></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="checkbox" name="SET_SEPARATE_PHOTO" id="yetiFishSetSeparatePhoto" value="Y"/><label for="yetiFishSetSeparatePhoto"><?=GetMessage("YETIFISH_SEPARATE_PHOTO")?></label></td>
		</tr>
		<tr>
			<td><label><?=GetMessage("YF_PHOTO_TAGS")?>:</label></td>
			<td><input name="PHOTO_TAGS" id="yetiFishPhotoTags" value="city,street" size="50"/></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="checkbox" name="RANDOM_SECTIONS" id="yetiFishRndSec" value="Y"/><label for="yetiFishRndSec"><?=GetMessage("YETIFISH_RANDOM_SECTIONS")?></label></td>
		</tr>
		<tr>
			<td><label><?=GetMessage("YETIFISH_LANG")?>:</label></td>
			<td>
				<select name="LANGUAGE">
					<option value="ru" selected="selected"><?=GetMessage("YETIFISH_LANG_RU")?></option>
					<option value="en"><?=GetMessage("YETIFISH_LANG_EN")?></option>
					<option value="de"><?=GetMessage("YETIFISH_LANG_DE")?></option>
				</select>
				<label><input type="radio" name="TRANSLATE_SERVICE" value="yandex" checked="checked" />&nbsp;<?=GetMessage("YF_YANDEX_TRANSLATE")?></label>
				<label><input type="radio" name="TRANSLATE_SERVICE" value="google"/>&nbsp;<?=GetMessage("YF_GOOGLE_TRANSLATE")?></label>
			</td>
		</tr>
		<tr>
			<td><label><?=GetMessage("YETIFISH_NEWS_THEME")?>:</label></td>
			<td>
				<select name="NEWS_THEME">
					<?
					$yaLinks = CYetiFishGenerator::$yaLinks;
					foreach($yaLinks as $code=>$link)
					{
						$themeName = GetMessage("YETIFISH_NEWS_THEME_".$code);
						if(empty($themeName))$themeName = $code;
						?><option value="<?=$code?>"><?=$themeName?></option><?
					}
					?>
				</select>
			</td>
		</tr>
		</table>
	
	</td>
	</tr>
	<?
}
else
{
	ShowError(GetMessage("YETIFISH_IBLOCKMODULE_ERROR"));
}


$tabControl->BeginNextTab();
?>
<tr>
<td>
	<table>
		<tr>
			<td><label><?=GetMessage("YF_FLICKR_APIKEY")?>:</label></td>
			<td>
			<?
			$apiKey = COption::GetOptionString($MID,"FLICKR_APIKEY");
			?>
			<input name="FLICKR_APIKEY" value="<?=$apiKey?>" size="50"/>
			</td>
		</tr>
	</table>
</td>
</tr>
<?


$tabControl->Buttons();
?>
<div class="yf_buttons" rel="yf_generator">
	<? /* <button type="submit" id="btn-generator"><?=GetMessage("YETIFISH_BTN_GENERATE")?></button> */ ?>
	<input type="submit" id="btn-generator" name="YF_GENERATE_BTN" value="<?=GetMessage("YETIFISH_BTN_GENERATE")?>"  />
</div>
<div class="yf_buttons" rel="yf_options" style="display:none;">
	<input type="submit" name="YF_OPTIONS_SAVE" value="<?=GetMessage("YF_OPT_SAVE")?>"  />
</div>
<?
$tabControl->End();
?>
</form>
<div id="yf-request-result" style="display:none"></div>
<?
require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
?>