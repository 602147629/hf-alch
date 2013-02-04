/**!
 * Hapyfish Client Page Platform Session Timeout Checker (jQuery lib required)
 * Version: 1.0.0
 *
 * Copyright (c) 2011 hapyfish.com
 * Author: zx
 */

var HFcOnlineChk = {
		checkInterval: 900000, //15min
		//checkInterval: 3000, //10sec

		init: function() {
			this.start(this.checkInterval);
		},

		start: function(intervalTm) {
			//window.setInterval("HFcOnlineChk.check()", intervalTm);
			window.setTimeout("HFcOnlineChk.check()", intervalTm);
		},

		check: function() {
			var url = 'check/online';
			var param = {};
			HFcOnlineChk.sendReq(url, param);
		},

		sendReq: function(url, objParam) {
			var strParam = '';
			var reqUrl = url;
			/*if (objParam) {
				for (skey in objParam) {
					if (strParam == '') {
						strParam += skey + '=' + objParam[skey];
					}
					else {
						strParam += '&' + skey + '=' + objParam[skey];
					}
				}
				reqUrl += '?' + strParam;
			}*/

			//ajax req
			$.ajax({
                type: "GET",
                url: reqUrl,
                data: objParam,
                dataType: "json",
                success: function(resp){
                	if(resp && resp.status==1) {
                    	$("#main_content").append('<div id="dlgRefresh">页面也开太久了吧，请刷新一下再回来继续游戏吧。</div>');
                    	$("#dialog:ui-dialog").dialog( "destroy" );
    		    		$("#dlgRefresh").dialog({
    		    			width: 300,
    		    			modal: true,
    		    			resizable: false,
    		    			title: '提示',
    		    			buttons: [{
                                        text: " 刷新 ",
                                        click: function() { $(this).dialog("close"); }
    		    			         }],
    		    			close: function(event, ui) { history.go(0); }
    		    		});
    				}
    				else {
    					HFcOnlineChk.start(HFcOnlineChk.checkInterval);
    				}
                }
	 		});
		}
}

HFcOnlineChk.init();