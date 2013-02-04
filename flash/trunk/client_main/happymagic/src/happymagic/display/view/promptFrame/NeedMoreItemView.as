package happymagic.display.view.promptFrame 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	/**
	 * ...
	 * @author ZC
	 */
	//需要更多的道具
	public class NeedMoreItemView extends UISprite
	{
		
		private var iview:NeedMoreItemViewUi;
		private var cid:int;
		private var itemvo:BaseItemClassVo;
        private var moduleVo:ModuleVo;
		
		public function NeedMoreItemView() 
		{
			_view = new NeedMoreItemViewUi();
			
			iview = _view as NeedMoreItemViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			moduleVo = DataManager.getInstance().getModuleVo("buyItemPopUp");
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
					closeMe(true);
					break;
					
				case "askfor":
					EventManager.getInstance().dispatchEvent(new Event("giftActEventStart"));
					break;
					
				case "buybtn":
					  itemvo = DataManager.getInstance().itemData.getItemClass(cid);
					  DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](itemvo);
					  closeMe(true)
					break;					
			}
		}
		
		public function setData(_cid:int):void
		{
			cid = _cid;
			
			if (DataManager.getInstance().isAskForGift(cid) == 0)
			{
				iview.askfor.visible = false;
				iview.buybtn.x -= 55;
			}
			else
			{
				if (DataManager.getInstance().curSceneUser.level >= DataManager.getInstance().isAskForGift(cid))
				{
					
				}
				else
				{
					iview.askfor.visible = false;
				    iview.buybtn.x -= 55;
				}
			}
			
			loadIcon(cid);
		}
		
		private function loadIcon(_cid:int):void 
		{
			var icon:IconView = new IconView(50, 50, new Rectangle(-15, -10, 50, 50));
			icon.setData(DataManager.getInstance().itemData.getItemClass(_cid).className);
			
			iview.nametxt.text = DataManager.getInstance().itemData.getItemClass(_cid).name;
			iview.addChild(icon);
		}
		
	}

}