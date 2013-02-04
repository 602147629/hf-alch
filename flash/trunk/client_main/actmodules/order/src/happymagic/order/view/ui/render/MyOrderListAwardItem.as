package happymagic.order.view.ui.render 
{
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.manager.local.LocaleWords;
	import happymagic.display.view.ui.ItemIcon;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MyOrderListAwardItem extends GridItem 
	{
		private var iview:OrderConditionItemRender;
		private var icon:ItemIcon;
		public var data:ConditionVo;
		
		public function MyOrderListAwardItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as OrderConditionItemRender;
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new ItemIcon(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.border));
			iview.border.visible = false;
		}
		
		override public function setData(value:Object):void 
		{
			var vo:ConditionVo = value as ConditionVo;
			icon.setCondition(vo);
			data = vo;
			iview.nameTxt.text = vo.getName();
			iview.numTxt.text = vo.currentNum + "/" + vo.num;
			iview.numTxt.textColor = vo.num <= vo.currentNum ? 0x005E2F : 0xA02001;
			
			var str:String = (ConditionType.ITEM == vo.type && "icon" in view)
								? view.nameTxt.text
								: LocaleWords.getInstance().getWord("clickViewXX", view.nameTxt.text);
			Tooltips.getInstance().register(view, str);
		}
		
	}

}