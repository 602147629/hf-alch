package happymagic.display.view.ui.personMsg 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.scene.world.grid.IsoItem;
	import happymagic.display.ui.TipsBgSmall;
	/**
	 * ...
	 * @author jj
	 */
	public class PersonMsgView extends Sprite
	{
		
		private var maxTxtWidth:int = 120;
		private var target:IsoItem;
		private var closeId:uint;
		private var hasInit:Boolean;
		private var callback:Function;
		
		private var txt:TextField;
		private var tipBg:TipsBgSmall;
		
		public function PersonMsgView(_target:IsoItem,str:String,__time:uint,_callback:Function=null) 
		{
			callback = _callback;
			mouseChildren = false;
			mouseEnabled = false;
			cacheAsBitmap = true;
			
			target = _target;
			
			tipBg = new TipsBgSmall();
			addChild(tipBg);
			
			var format:TextFormat = new TextFormat("微软雅黑", 12, 0x391D0B);
			txt = new TextField();
			txt.wordWrap = true;
			txt.defaultTextFormat = format;
			txt.textColor = 0x391D0B;
			addChild(txt);
			
			setData(str,__time,callback);
		}
		
		public function setData(value:String, time:uint,_callback:Function=null):void {
			if (!hasInit) {
				var selfrect:Rectangle = target.view.container.getRect(target.view.container);
				y = selfrect.top - 5;
				//y = - target.view.container.height+25;
				hasInit = true;
			}
			callback = _callback;
			txt.width = maxTxtWidth;
			txt.htmlText = value;
			
			
			if (closeId) 
			{
				clearTimeout(closeId);
				closeId = 0;
			}
			closeId = setTimeout(closeMe, time);
			
			target.view.container.addChild(this);
			
			txtChange();
		}
		
		private function txtChange(e:Event=null):void 
		{
			txt.width = txt.textWidth + 4;
			txt.height = txt.textHeight + 4;
			var bgW:int = txt.textWidth + 4 + tipBg.padding.left + tipBg.padding.right;
			var bgH:int = txt.textHeight + 4 + tipBg.padding.top + tipBg.padding.bottom;
			
			var textWidth:Number = Math.max(txt.textWidth, txt.width);
			txt.x = -bgW * 0.5 + tipBg.padding.left;
			txt.y = -bgH + tipBg.padding.top;
			
			tipBg.width = bgW
			tipBg.height = bgH;
		}
		
		public function closeMe():void
		{
			if (closeId)
			{
				clearTimeout(closeId);
				closeId = 0;
			}
			
			if (parent) 
			{
				parent.removeChild(this);
			}
			
			if (callback!=null) 
			{
				callback.apply();
			}
			
			PersonMsgManager.getInstance().delMsg(target.view.name,true);
		}
		
		
		
	}

}