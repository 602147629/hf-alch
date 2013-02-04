package model.view 
{
	import event.WorldMapEvent;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.WorldMapClassVo;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class WorldMapAffirmView extends UISprite 
	{
		private var iview:WorldMapAffirmViewUi;
		private var data:WorldMapClassVo;
		public function WorldMapAffirmView() 
		{
			_view = new WorldMapAffirmViewUi();
			iview = _view as WorldMapAffirmViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
					closeMe(true);
					break;
					
				case "ok":
					//if (!isConditionRole(data.roleConditionLevel))
					//{
						//DisplayManager.showSysMsg(LocaleWords.getInstance().getWord("WorldMapWord1"));
						//return;
					//}
					//
					//if (DataManager.getInstance().currentUser.sp >= data.sp)
					//{
					    //EventManager.getInstance().dispatchEvent(new WorldMapEvent(WorldMapEvent.DRAWMAPOPEN));						
					//}
					//else
					//{
						//DisplayManager.showNeedMorePhysicalStrengthView();
					//}
//
					closeMe(true);
					break;
			}
		}
		
		public function setData(_id:int):void
		{
			data = DataManager.getInstance().worldData.getWorldMapClassVo(_id);
			
			iview.sp.text = String(data.sp);
			iview.worldname.text = data.name;
		}
		

		
	}

}