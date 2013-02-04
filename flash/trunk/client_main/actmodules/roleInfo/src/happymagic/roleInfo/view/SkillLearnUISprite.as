package happymagic.roleInfo.view 
{
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.Pagination;
	import happyfish.display.view.UISprite;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.ScrollClassVo;
	import happymagic.model.vo.ItemType;
	import happymagic.roleInfo.view.ui.ChooseEquipmentUI;
	import happymagic.roleInfo.view.ui.render.EquiItemRender;
	import happymagic.roleInfo.view.ui.render.SkillLearnItemRender;
	import happymagic.roleInfo.view.ui.SkillLearnItem;
	import happymagic.roleInfo.view.ui.SkillLearnUI;
	import happymagic.roleInfo.vo.EquiCompareVo;
	import happymagic.roleInfo.vo.EquiRoleVo;
	import happymagic.roleInfo.vo.SkillPosVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class SkillLearnUISprite extends UISprite 
	{
		
		private var roleId:int;
		private var pos:int;
		
		private var iview:SkillLearnUI;
		private var listView:DefaultListView;
		
		public function SkillLearnUISprite() 
		{
			iview = new SkillLearnUI();
			_view = iview;
			
			listView = new DefaultListView(iview, iview, 4, false, false);
			listView.init(380, 300, 344, 70, -171, -133);
			listView.setGridItem(SkillLearnItem, SkillLearnItemRender);
			listView.selectCallBack = closeWindow;
			listView.pagination = new Pagination();
			listView.pagination.y = 162;
			listView.pagination.x = 0;
			
			iview.closeBtn.addEventListener(MouseEvent.CLICK, closeWindow);
		}
		
		public function getFirstAvailableView():DisplayObject
		{
			return listView.getItemByIndex(0).view;
		}
		
		private function closeWindow(e:Event):void 
		{
			closeMe();
		}
		
		public function setData(roleId:int, roleLevel:int, pos:int, job:int, prop:int):void
		{
			this.roleId = roleId;
			this.pos = pos;
			
			var getSkillAndItemVo:Function = DataManager.getInstance().getSkillAndItemVo;
			var arr:Array = DataManager.getInstance().itemData.getItemListByType2(ItemType.Skill);
			
			var tmp:Array = [];
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				var vo:ScrollClassVo = arr[i].base as ScrollClassVo;
				if (vo.level > roleLevel) continue;
				if (vo.jobs && -1 == vo.jobs.indexOf(job)) continue;
				if (vo.props && -1 == vo.props.indexOf(prop)) continue;
				tmp.push(new SkillPosVo(roleId, pos, getSkillAndItemVo(arr[i].base.cid), vo));
			}
			listView.setData(tmp);
		}
	}
}