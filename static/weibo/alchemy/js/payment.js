if (Hapyfish === undefined) {
	var Hapyfish = {};
}
if (Hapyfish.Payment === undefined) {
	Hapyfish.Payment = {};
}

(function() {
	Hapyfish.Payment = {
		lang:{},
		Config:{
			userId:"",
			platformUid:"",
			staticUrl:"",
			payOrderUrl:""
		},

        init:function(lang, config) {
            var that = this;
            that.Config = $.extend({}, that.Config, config);
            that.lang = $.extend({}, that.lang, lang);
        },
                
        payOrder:function(type) {
            var that = this;
            var params = {'type':type};
            var url = that.Config.payOrderUrl;
            $.ajax({type:'post',
                url:url,
                data:params,
                dataType:'json',
                success:function(data) {
                    if (data) {
	                    if (data.status) {
	                        that._showPayDialog(data.para);
	                    } else {
	                        alert(that.lang.error);
	                    }
                    } else {
                        alert(that.lang.error);
                    }
                },
				error:function() {
					alert(that.lang.error);
				}
            });
        },

        _showPayDialog:function(p) {
			p.display = 'iframe';
			p.cb = function(v){console.log(v);};
			KX.pay(p);
        }

    };
})();
