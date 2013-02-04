package happymagic.roleInfo.view 
{
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.utils.getQualifiedClassName;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.roleInfo.events.RoleEvent;
	import happymagic.roleInfo.events.RoleInfoActEvent;
	import happymagic.roleInfo.view.ui.MySoldierListUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MySoldierListUISprite extends UISprite 
	{
		private var iview:MySoldierListUI;
		
		private var table:TabelView;
		public var roleView:RoleInfoView;
		public var formationView:FormationInfoView;
		
		public function MySoldierListUISprite() 
		{
			iview = new MySoldierListUI();
			_view = iview;
			
			table = new TabelView(false);
			iview.addChild(table);
			table.addTabs([view.roleTab], [view.formationTab]);
			table.addEventListener(Event.SELECT, selectHandler);
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			EventManager.addEventListener(DataManagerEvent.ROLEDATA_CHANGE, roleChangeHandler);
			showRoleNum();
			
		}
		
		private function roleChangeHandler(e:DataManagerEvent):void 
		{
			if(e.role_addOrRemove) showRoleNum();
		}
		
		public function show():void
		{
			table.select(table.selectIndex, true);
			showRoleNum();
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case view.closeBtn :
					closeMe();
					break;
					
				case view.expandBtn :
					expandRoleLimit();
					break;
			}
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			super.closeMe(del);
			EventManager.dispatchEvent(new RoleInfoActEvent(RoleInfoActEvent.ROLE_INFO_CLOSE));
			if(roleView) roleView.changeTrainPanel(null);
		}
		
		private function expandRoleLimit():void 
		{
			var count:int = DataManager.getInstance().currentUser.maxRoleNum + 1;
			var info:Array = DataManager.getInstance().roleData.rolePosPriceMap[count];
			if (!info) return;
			
			var moduleVo:ModuleVo = new ModuleVo();
			
			var level:int = DataManager.getInstance().currentUser.viliageInfo[DataManager.getInstance().gameSetting.homeBuildId];
			
			if (info[0] > level)
			{
				moduleVo.className = getQualifiedClassName(ExpandRoleErrorUISprite);
				moduleVo.name = moduleVo.className;
				ExpandRoleErrorUISprite(ModuleManager.getInstance().addModule(moduleVo)).setData(info[0]);
			}else
			{
				moduleVo.className = getQualifiedClassName(ExpandRoleSelectPriceUISprite);
				moduleVo.name = moduleVo.className;
				ExpandRoleSelectPriceUISprite(ModuleManager.getInstance().addModule(moduleVo)).setData(count, info[1], info[2], showRoleNum);
			}
			moduleVo.name = moduleVo.className;
			var module:IModule = ModuleManager.getInstance().showModule(moduleVo.name);
			DisplayManager.uiSprite.setBg(module)
		}
		
		private function showRoleNum():void 
		{
			var has:int = DataManager.getInstance().roleData.getMyRoles().length;
			var max:int = DataManager.getInstance().currentUser.maxRoleNum;
			iview.numTxt.text = has + "/" + max;
			var info:Array = DataManager.getInstance().roleData.rolePosPriceMap[max + 1];
			iview.expandBtn.visible = info != null;
		}
		
		private function selectHandler(e:Event):void 
		{
			switch(table.selectIndex)
			{
				case 0 :
					if (!roleView)
					{
						roleView = new RoleInfoView();
						view.addChild(roleView);
					}
					roleView.show();
					if (formationView) formationView.hide();
					break;
					
				case 1 :
					if (!formationView)
					{
						formationView = new FormationInfoView();
						view.addChild(formationView);
					}
					formationView.show();
					if (roleView) roleView.hide();
					break;
			}
			iview.addChild(iview.topMc);
		}
		
	}

}