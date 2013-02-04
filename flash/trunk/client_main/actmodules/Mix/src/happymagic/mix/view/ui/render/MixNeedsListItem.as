package happymagic.mix.view.ui.render 
{
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happymagic.mix.view.ui.MixNeedsRender;
	import happymagic.mix.vo.MixNeedVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MixNeedsListItem extends GridItem 
	{
		private var iview:MixNeedsRender;
		private var icon:IconView;
		private var data:MixNeedVo;
		
		public function MixNeedsListItem(ui:MovieClip) 
		{
			super(ui);
			
			iview = ui as MixNeedsRender;
			iview.buttonMode = false;
			iview.mouseChildren = true;
			
			iview.border.visible = false;
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.border));
		}
		
		public function setNeed(value:int):void
		{
			data.need = value;
			var s:String = "x " +
				(data.need > data.has? "<font color='#FF0000'>" + data.need + "</font>" : data.need) +
				"/" + data.has;
			//iview.numTxt.text = "x " + data.need + "/" +data.has;
			iview.numTxt.text = data.need + "";
			iview.numTxt.textColor = 0xFFFF00;
		}
		
		public function setHas(value:int):void
		{
			data.has = value;
			var s:String = "x " +
				(data.need > data.has? "<font color='#FF0000'>" + data.need + "</font>" : data.need) +
				"/" + data.has;
			iview.numTxt.text = data.need+"";
			iview.numTxt.textColor = 0xFFFF00;
		}
		
		override public function setData(value:Object):void 
		{
			this.data = value as MixNeedVo;
			var s:String = data.need > data.has? "<font color='#FF0000'>" + data.need + "</font>" : data.need+"";
				//var s:String = "x " +
				//(data.need > data.has? "<font color='#FF0000'>" + data.need + "</font>" : data.need) +
				//"/" + data.has;
			iview.numTxt.htmlText = s;
			icon.setData(data.vo.className);
			
			Tooltips.getInstance().register(icon, data.vo.content, Tooltips.getInstance().getBg("defaultBg"));
		}
	}

}