<?
class CYetiFishGenerator
{
	private static $MID = "yeti.fish";
	private static $flickr_api_key = "09a0ea92182a4f5d5c56b80e0b634710";
	
	public static $yaLinks = array(
		"astronomy" => "http://vesna.yandex.ru/astronomy.xml",
		"geology" => "http://vesna.yandex.ru/geology.xml",
		"gyroscope" => "http://vesna.yandex.ru/gyroscope.xml",
		"literature" => "http://vesna.yandex.ru/literature.xml",
		"marketing" => "http://vesna.yandex.ru/marketing.xml",
		"mathematics" => "http://vesna.yandex.ru/mathematics.xml",
		"music" => "http://vesna.yandex.ru/music.xml",
		"polit" => "http://vesna.yandex.ru/polit.xml",
		"agrobiologia" => "http://vesna.yandex.ru/agrobiologia.xml",
		"law" => "http://vesna.yandex.ru/law.xml",
		"psychology" => "http://vesna.yandex.ru/psychology.xml",
		"geography" => "http://vesna.yandex.ru/geography.xml",
		"physics" => "http://vesna.yandex.ru/physics.xml",
		"philosophy" => "http://vesna.yandex.ru/philosophy.xml",
		"chemistry" => "http://vesna.yandex.ru/chemistry.xml",
		"estetica" => "http://vesna.yandex.ru/estetica.xml",
	);
	
	public static function getRandomFlickrPhoto($tags = "")
	{
		rand();rand();
		$tags = explode(",",$tags);
		$tags = array_filter($tags);
		foreach($tags as $k=>$t)$tags[$k] = rawurlencode($t);
		$tagsParam = implode(",",$tags);
		
		$api_key = COption::GetOptionString(self::$MID,"FLICKR_APIKEY");
		if(empty($api_key))$api_key = self::$flickr_api_key;
		
		
		$flickrApiUrl = "http://api.flickr.com/services/rest/?method=flickr.photos.search".
			"&api_key=".$api_key.
			"&tags=".$tagsParam.
			"&tag_mode=all".
			"&license=1".
			"&per_page=20".
			"&privacy_filter=1".
			"&safe_search=1".
			"&content_type=1".
			"&extras=url_z,url_o,url_l".
			"&sort=interestingness-desc".
			"&page=".rand(1,30).
			"&format=php_serial";
		$flickrData = file_get_contents($flickrApiUrl);
		$flickrData = unserialize($flickrData);
		
		$returnPhotos = array();
		
		if($flickrData["stat"] == "ok")
		{
			// http://farm{farm-id}.staticflickr.com/{server-id}/{id}_{o-secret}_o.(jpg|gif|png)
			// http://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}_[mstzb].jpg
			/*
			s	small square 75x75
			q	large square 150x150
			t	thumbnail, 100 on longest side
			m	small, 240 on longest side
			n	small, 320 on longest side
			-	medium, 500 on longest side
			z	medium 640, 640 on longest side
			c	medium 800, 800 on longest side†
			b	large, 1024 on longest side*
			o	original image, either a jpg, gif or png, depending on source format
			*/
			foreach($flickrData["photos"]["photo"] as $photo)
			{
				if(!empty($photo["url_l"]))
				{
					$returnPhotos[] = array(
						"small" => $photo["url_z"],
						"big" => $photo["url_l"],
					);
				}
			}
		}
		return $returnPhotos;
	}
	
	
	public static function getPicture($tags = "")
	{
		$YF_PICTURE_CACHE = &$_SESSION["YF_PICTURE_CACHE"][md5($tags)];
		if(!is_array($YF_PICTURE_CACHE) || count($YF_PICTURE_CACHE) == 0)
		{
			$YF_PICTURE_CACHE = self::getRandomFlickrPhoto($tags);
		}
		$photo = false;
		if( count($YF_PICTURE_CACHE) > 0 )
			$photo = array_pop($YF_PICTURE_CACHE);
		return $photo;
	}
	
	
	public static function getNewsTexts($themeCode = "marketing")
	{
		$arResult = array();
		
		$yaTextLink = self::$yaLinks["marketing"];
		if(isset(self::$yaLinks[$themeCode]))
			$yaTextLink = self::$yaLinks[$themeCode];
		
		$xmlStr = file_get_contents($yaTextLink);
		
		$qlChar = html_entity_decode("&laquo;",ENT_QUOTES,"utf-8");
		$qrChar = html_entity_decode("&raquo;",ENT_QUOTES,"utf-8");
		
		if(class_exists("DOMDocument"))
		{
			$xml = new DOMDocument();
			$xml->loadHTML($xmlStr);
			
			$arResult["TEXT"] = array();
			foreach($xml->getElementsByTagName("p") as $p)
			{
				$txt = $p->nodeValue;
				if(strlen($txt) > 200)
				{
					$arResult["TEXT"][] = $txt;
				}
			}
			
			$arResult["TITLE"] = "";
			foreach($xml->getElementsByTagName("h1") as $h1)
			{
				$txt = $h1->nodeValue;
				if(preg_match("#".$qlChar."(.+)".$qrChar."#i",$txt,$m))
				{
					$arResult["TITLE"] = $m[1];
				}
			}
		}
		else
		{
			$arResult["TITLE"] = "";
			preg_match("/<h1[^>]*>([^<]*)<\/h1>/us",$xmlStr,$matches);
			if($matches[1])
			{
				list($tmp,$title) = explode(":",$matches[1],2);
				$title = trim($title);
				if(preg_match("#^.{1}(.*).{1}$#u",$title,$m))
				{
					$title = $m[1];
				}
				$arResult["TITLE"] = $title;
			}
			
			$arResult["TEXT"] = array();
			preg_match_all("/<p[^>]*>([^<]*)<\/p>/us",$xmlStr,$matches);
			$i = 0;
			while($i < 3)
			{
				if(!empty($matches[1][$i]))
				{
					$arResult["TEXT"][] = $matches[1][$i];
				}
				$i++;
			}
		}
		
		return $arResult;
	}
	
	public static function yaTranslate($sText, $sLang="ru", $tLang="en")
	{
		$sText = rawurlencode($sText);
		$str = file_get_contents("http://translate.yandex.net/api/v1/tr.json/translate?lang=".$sLang."-".$tLang."&text=".$sText);
		$text = json_decode($str);
		$text = array_pop($text->text);
		return $text;
	}
	
	public static function googleTranslate($sText, $sLang="ru", $tLang="en")
	{
		if(strlen($sText) > 100)
		{
			if(preg_match("#\.#",$sText))
			{
				$resText = array();
				$arTxt = explode(".",$sText);
				foreach($arTxt as $txt)
				{
					$resText[] = self::googleTranslate($txt,$sLang,$tLang);
				}
				return implode(".",$resText);
			}
		}
		
		$sTextUrl = rawurlencode($sText);
		$str = file_get_contents("http://translate.google.ru/translate_a/t?client=t&text=".$sTextUrl."&hl=".$sLang."&sl=".$sLang."&tl=".$tLang."&ie=UTF-8&oe=UTF-8&multires=1&otf=2&trs=1&ssel=5&tsel=5&sc=1");
		
		if(preg_match_all('#^\[\[\[([^\]]*)\]\]#',$str,$m))
		{
			$arr = $m[1][0];
			$arr = split('","',$arr);
			$sText = preg_replace('#^"#',"",$arr[0]);
		}
		return $sText;
	}
	
	function addNews($IBLOCK_ID,$count = 5,$themeCode = "marketing",$lang = "ru", $translateService = "yandex", $randomSection = true, $photoTags = "", $setDetailPhoto = false, $setPreviewPhoto = false, $separatePhoto = false)
	{
		$arResult = array("ITEMS"=>array());
	
		if(CModule::IncludeModule("iblock"))
		{
			$IBLOCK_ID = intval($IBLOCK_ID);
			if($iblock = CIBlock::GetByID($IBLOCK_ID)->fetch())
			{
				if($randomSection)
				{
					$arIBSections = array();
					$arIBSections[] = false;//root
					$rs = CIBlockSection::GetList(array(),array("IBLOCK_ID"=>$IBLOCK_ID));
					while($r = $rs->Fetch())
					{
						$arIBSections[] = $r["ID"];
					}
				}
				
			
				$count = intval($count);
				if($count == 0)$count = 5;
				while($count > 0)
				{
					$sid = false;
					if($randomSection)
					{
						$randKey = array_rand($arIBSections);
						$sid = $arIBSections[$randKey];
					}
					
					// translate
					$newsTexts = self::getNewsTexts($themeCode);
					if($lang != "ru")
					{
						switch($translateService)
						{
							case "google":
								foreach($newsTexts["TEXT"] as $k => $txt)
									$newsTexts["TEXT"][$k] = self::googleTranslate($txt,"ru",$lang);
								$newsTexts["TITLE"] = self::googleTranslate($newsTexts["TITLE"],"ru",$lang);
								break;
							default:
								foreach($newsTexts["TEXT"] as $k => $txt)
									$newsTexts["TEXT"][$k] = self::yaTranslate($txt,"ru",$lang);
								$newsTexts["TITLE"] = self::yaTranslate($newsTexts["TITLE"],"ru",$lang);
								break;
						}
						
						
					}
					
					// encoding
					$SITE = CSite::GetList($by="sort",$ord="asc",array("DEF"=>"Y"))->fetch();
					foreach($newsTexts["TEXT"] as $k => $txt)
						$newsTexts["TEXT"][$k] = iconv('UTF-8', $SITE["CHARSET"], $txt);
					$newsTexts["TITLE"] = iconv('UTF-8', $SITE["CHARSET"], $newsTexts["TITLE"]);
					$arFields = Array(  
						"MODIFIED_BY"    => CUser::GetID(),
						"IBLOCK_SECTION_ID" => $sid,
						"IBLOCK_ID"      => $IBLOCK_ID,  
						"NAME"           => $newsTexts["TITLE"],  
						"ACTIVE"         => "Y",// àêòèâåí  
						"ACTIVE_FROM"	=> date("d.m.Y H:i:s"),
						"PREVIEW_TEXT"   => $newsTexts["TEXT"][0],
						"PREVIEW_TEXT_TYPE" =>"text",
						"DETAIL_TEXT"    => $newsTexts["TEXT"][1]."\r\n".$newsTexts["TEXT"][2],
						"DETAIL_TEXT_TYPE" =>"text",
						"CODE" => Cutil::translit($newsTexts["TITLE"], "ru", array("max_len"=>50,"change_case"=>"L","replace_space"=>"-","replace_other"=>"-","delete_repeat_replace"=>true)),
					);
					
					
					if($setDetailPhoto || $setPreviewPhoto)
					{
						$detailPhoto = $previewPhoto = false;
						if($setDetailPhoto)
						{
							$detailPhoto = self::getPicture($photoTags);
							$arFields["DETAIL_PICTURE"] = CFile::MakeFileArray($detailPhoto["big"]);
						}
						
						if($setPreviewPhoto)
						{
							$previewPhoto = $detailPhoto;
							if($separatePhoto || $detailPhoto === false)
							{
								$previewPhoto = self::getPicture($photoTags);
							}
							$arFields["PREVIEW_PICTURE"] = CFile::MakeFileArray($previewPhoto["small"]);
						}
					}
					
					$el = new CIBlockElement;
					if($ELEMENT_ID = $el->Add($arFields))
					{
						$arResult["ITEMS"][$ELEMENT_ID.""] = $arFields;
					}
					
					$count--;
				}
			}
		}
		
		return $arResult;
		
	}
	
	
}

?>