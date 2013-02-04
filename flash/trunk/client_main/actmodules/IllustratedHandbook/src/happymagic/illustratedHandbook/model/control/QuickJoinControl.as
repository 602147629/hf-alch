package happymagic.illustratedHandbook.model.control 
{
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happymagic.illustratedHandbook.event.IllustratedHandbookEvent;
	import happymagic.illustratedHandbook.model.view.IllustratedHandbookHomepageView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	/**
	 * ...
	 * @author ZC
	 */
	
	 //快速搜索
	public class QuickJoinControl 
	{
		private var name:String;
		private var cid:int;
		private var data:IllustrationsClassVo;
		
		public function QuickJoinControl(_str:String) 
		{
			name = _str;
			
			setdata();
		}
		
		private function setdata():void 
		{
			var arr:Array = DataManager.getInstance().illustratedData.illustratedHandbookStatic;
			
			for (var i:int = 0; i < arr.length; i++ )
			{
				if (arr[i].name == name )
				{
					cid = arr[i].cid;
					data = arr[i];
				}
			}
			
			if (cid)
			{
				for (var j:int = 0; j < DataManager.getInstance().illustratedData.illustratedHandbookInit.length; j++ )
				{
					if (DataManager.getInstance().illustratedData.illustratedHandbookInit[j].cid == cid )
					{
						DataManager.getInstance().setVar("HandBookSelect", cid);
						var illustratedHandbookHomepageView:IllustratedHandbookHomepageView = DisplayManager.uiSprite.addModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKHOMEPAGEVIEW, 
			                                        IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKHOMEPAGEVIEW_CLASS, false, AlginType.CENTER, 0, -5,0,0,"fromCenter",0) as IllustratedHandbookHomepageView;
						illustratedHandbookHomepageView.setData(data.type2,1);
					    DisplayManager.uiSprite.setBg(illustratedHandbookHomepageView);						
						
						return;
					}					
				}
				
			// "系统提示框"
			DisplayManager.showSysMsg(LocaleWords.getInstance().getWord("IllustratedHandbookword26"));
			
            EventManager.getInstance().dispatchEvent(new IllustratedHandbookEvent(IllustratedHandbookEvent.ILLUSTRATEMODULECLOSE));					
				
			}
			//else
			//{
				//DisplayManager.showSysMsg(LocaleWords.getInstance().getWord("IllustratedHandbookword26"));
			//}
			
					   
			//}	
			
		}
		
	}

}