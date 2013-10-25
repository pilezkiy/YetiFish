function initTabsButtons()
{
	$(".tab-selected, .adm-detail-tab-active").click();
}

$(function(){
	$("#btn-generator").live("click",function(e){
		e.preventDefault ? e.preventDefault() : e.returnValue = false;
		var form = $(this).closest("form");
		$("#yf-request-result").html("<img src='/bitrix/themes/.default/images/yeti_fish_loading.gif'/>")
		$("#yf-request-result").show();
		$.post("/bitrix/tools/yetifish_ajax.php?action=newsGenerate",form.serialize(),function(data){
			$("#yf-request-result").html(data);
		});
	});
	
	setTimeout("initTabsButtons()",200);
	
	$("#tab_yf_generator, #tab_cont_yf_generator").click(function(){
		$(".yf_buttons").hide();
		$(".yf_buttons[rel=yf_generator]").show();
		
	});
	
	$("#tab_yf_options, #tab_cont_yf_options").click(function(){
		$(".yf_buttons").hide();
		$(".yf_buttons[rel=yf_options]").show();
		
	});
	
	
});