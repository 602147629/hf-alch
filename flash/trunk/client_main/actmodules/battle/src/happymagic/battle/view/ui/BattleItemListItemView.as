package happymagic.battle.view.ui 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happymagic.battle.events.BattleEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * ...
	 * @author 
	 */
	public class BattleItemListItemView extends GridItem 
	{
		private var icon:IconView;
		public var data:ItemVo;
		public var iview:battleItemListItemUi;
		public function BattleItemListItemView(_view:MovieClip) 
		{
			
			view = _view;
			iview = view as battleItemListItemUi;
			
			super(view);
			view.mouseChildren = false;
			view.addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var event:BattleEvent = new BattleEvent(BattleEvent.REQUEST_SKILL);
			event.itemId = Number(data.id);
			event.skillAndItemVo = DataManager.getInstance().getSkillAndItemVo(data.cid);
			EventManager.getInstance().dispatchEvent(event);
			EventManager.getInstance().dispatchEvent(new BattleEvent(BattleEvent.CLOSE_BAG));
		}
		
		override public function setData(value:Object):void 
		{
			data = value as ItemVo;
			iview.numTxt.text = data.num.toString();
			
			var effect:SkillAndItemVo = DataManager.getInstance().getSkillAndItemVo(data.cid);
			//effect.
			Tooltips.getInstance().register(iview, effect.name);
			
			loadIcon();
		}
		
		private function loadIcon():void 
		{
			if (!icon) 
			{
				icon = new IconView(34, 34, new Rectangle(0, 0, 40, 40));
			}
			
			icon.setData(data.base.className);
			view.addChild(icon);
		}
		
	}

}