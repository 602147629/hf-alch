/**!
 * Hapyfish Client Js Lib (jQuery lib required)
 * Version: 1.0.0
 *
 * Copyright (c) 2011 hapyfish.com
 * Author: hf
 */

var HFApp = {
		appId: "",
		appName: "",
		appHostUrl: "",
		appStaticUrl: "",
		sns: "",
		gameUrl: "",
		uid: "",
		puid: "",
		sessionkey: "",
		sessionid: "",
		curFcode: '0',

		init: function(config) {
			this.gameUrl = "http://yingyong.taobao.com/show.htm?app_id=109001",
			this.appId = config.appId;
			this.appHostUrl = config.appHostUrl;
			this.appStaticUrl = config.appStaticUrl;
			this.sns = config.sns;
			this.uid = config.uid;
			this.puid = config.puid;
			this.sessionkey = config.sessionkey;
			this.sessionid = config.sessionid;
		},

	    feed: function(feedSettings) {
			var title = feedSettings.title;
			var comment = feedSettings.comment;
			var description = feedSettings.description;
			var imgUrl = feedSettings.img;
			var linkUrl = feedSettings.link;
	    	//'{"title":"点击进入快乐森林","linkurl":"http://yingyong.taobao.com/show.htm?app_id=109001","comment":"快乐森林的建设需要你的帮助，赶快来加入吧~！来见证大熊猫的成长~为大熊猫和他的伙伴们创建家园吧!","itempic":"http://tbipandastatic.hapyfish.com/ipanda/image/share/feed.jpg","props":{"description":"快乐森林急需管理员，赶快加入我们~！"}}'
	    	var shareParam = ['{"title":"'+title+'"'];
	    	shareParam.push('"linkurl":"'+linkUrl+'"');
	    	shareParam.push('"comment":"'+comment+'"');
	    	shareParam.push('"itempic":"'+imgUrl+'"');
	    	shareParam.push('"props":{"description":"'+description+'"}}');
	    	if (linkUrl) {
	    		var pos = linkUrl.indexOf('fcode=');
	    		if (pos > 0) {
	    			this.curFcode = linkUrl.substr(pos+6);
	    		}
	    	}
	    	
	    	var attrObj = shareParam.join(',');
	    	//var obj = $('#topShareFeed');
	    	var obj = $('<a />'); 
    	    obj.attr('data-shareparam', attrObj);

	    	TS.require('Share', '2.0', function() {
        	    new TS.Share(obj[0]).show('');
    	    });
    	    return false;
	    },

		feedSuccess: function() {
			var url = this.appHostUrl + '/log/report';
			var param = {'type': 'sendfeed', 'fcode': this.curFcode};
			this.reportReq(url, param);
			return;
		},

	    resize: function() {

		},

	    invite: function(sig) {

	    },

	    pay: function() {

	    },

	    home: function() {
	    	top.location.href = this.gameUrl;
	    },

		reportReq: function(url, objParam) {
			var strParam = '';
			var reqUrl = url;
			if (objParam) {
				for (skey in objParam) {
					if (strParam == '') {
						strParam += skey + '=' + objParam[skey];
					}
					else {
						strParam += '&' + skey + '=' + objParam[skey];
					}
				}
				reqUrl += '?' + strParam;
			}
			var img = new Image();
			img.src = reqUrl;
		},

		loadHtml: function(url, method, pardata, dataType, id) {//require jquery api support
			if (!method) { //("POST"  or  "GET")
				method = 'GET';
			}
			if (!dataType) {//xml html script json jsonp text
				dataType = "text";
			}
			$.ajax({
                type: method,
                url: url,
                data: pardata,
                dataType: dataType,
                success: function(resp){
                	$("#" + id).html(resp);
                	HFApp.resize();
                }
	 		});
		}
}
