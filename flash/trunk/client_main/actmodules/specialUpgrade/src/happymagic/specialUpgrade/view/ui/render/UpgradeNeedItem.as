package happymagic.specialUpgrade.view.ui.render 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.ItemIcon;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.specialUpgrade.view.ModuleName;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class UpgradeNeedItem extends GridItem 
	{
		private var cid:int;
		private var lackNum:int;
		private var icon:ItemIcon;
		private var iview:UpgradeNeedItemRender;
		
		public function UpgradeNeedItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as UpgradeNeedItemRender;
			iview.mouseChildren = true;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new ItemIcon(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.border));
			iview.border.visible = false;
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		override public function setData(value:Object):void 
		{
			var vo:ConditionVo = value as ConditionVo;
			icon.setCondition(vo);
			
			cid = int(vo.id);
			lackNum = vo.num - vo.curNum;
			if (lackNum < 0) lackNum = 0;
			
			iview.nameTxt.text = vo.getName();
			iview.numTxt.text = vo.curNum +"/" + vo.num;
			iview.finishFlag.visible = 0 == lackNum;
			iview.buyBtn.visible = lackNum > 0 && DataManager.getInstance().itemData.getShopItemClass(cid);
			
			var canDemand:Boolean = lackNum > 0;
			if (canDemand)
			{
				var result:int = DataManager.getInstance().isAskForGift(cid);
				canDemand = result != 0 && result <= DataManager.getInstance().currentUser.level;
			}
			iview.demandBtn.visible = lackNum > 0 && DataManager.getInstance().isAskForGift(cid);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.buyBtn :
					buyItem();
					break;
					
				case iview.demandBtn :
					EventManager.dispatchEvent(new Event("giftActEventStart"));
					//iview.dispatchEvent(new Event(Event.CLOSE, true));
					UISprite(ModuleManager.getInstance().getModule(ModuleName.UpgradeUISpriteName)).closeMe();
					break;
			}
		}
		
		private function buyItem():void 
		{
			var vo:BaseItemClassVo = DataManager.getInstance().itemData.getItemClass(cid);
			var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("buyItemPopUp");
			DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](vo, lackNum);
		}
		
	}

}