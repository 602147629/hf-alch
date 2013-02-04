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
	import happymagic.model.vo.order.OrderVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderAwardsItem extends GridItem 
	{
		private var itemIcon:ItemIcon;
		public var data:ConditionVo;
		
		public function OrderAwardsItem(ui:MovieClip) 
		{
			super(ui);
			
			if ("icon" in view && view.icon is ItemIcon)
			{
				itemIcon = view.icon;
			}else
			{
				var rect:Rectangle = new Rectangle(view.border.x, view.border.y, view.border.width, view.border.height);
				itemIcon = new ItemIcon(rect.width, rect.height, rect);
				view.addChildAt(itemIcon, view.getChildIndex(view.border));
				view.border.visible = false;
			}
		}
		
		override public function setData(value:Object):void 
		{
			var vo:ConditionVo = value as ConditionVo;
			data = vo;
			itemIcon.setCondition(vo);
			view.numTxt.text = vo.num + "";
			view.nameTxt.text = vo.getName();
			
			var str:String = (ConditionType.ITEM == vo.type && "icon" in view)
								? view.nameTxt.text
								: LocaleWords.getInstance().getWord("clickViewXX", view.nameTxt.text);
			Tooltips.getInstance().register(view, str, Tooltips.getInstance().getBg("defaultBg"));
		}
		
	}

}