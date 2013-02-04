package happymagic.battle.view.ui 
{
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happymagic.display.view.ui.ItemIcon;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author 
	 */
	public class BattleWinAwardItemView extends GridItem 
	{
		private var icon:ItemIcon;
		private var data:ConditionVo;
		private var iview:battleWinItemUi;
		public function BattleWinAwardItemView(_view:MovieClip) 
		{
			view = _view;
			iview = view as battleWinItemUi;
			super(view);
			view.mouseChildren = false;
		}
		
		override public function setData(value:Object):void 
		{
			data = value as ConditionVo;
			loadIcon();
			
			iview.nameTxt.text = data.getName();
			iview.numTxt.text = data.num.toString();
		}
		
		private function loadIcon():void 
		{
			if (!icon) 
			{
				icon = new ItemIcon(40, 40, new Rectangle(10, 20, 40, 40));
			}
			
			icon.setCondition(data);
			view.addChildAt(icon,0);
		}
		
	}

}