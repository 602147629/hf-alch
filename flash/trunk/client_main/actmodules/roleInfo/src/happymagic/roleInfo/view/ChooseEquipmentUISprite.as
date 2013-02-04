package happymagic.roleInfo.view 
{
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Pagination;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.roleInfo.view.ui.ChooseEquipmentUI;
	import happymagic.roleInfo.view.ui.EquipItem;
	import happymagic.roleInfo.view.ui.render.EquiItemRender;
	import happymagic.roleInfo.vo.EquiCompareVo;
	import happymagic.roleInfo.vo.EquiRoleVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ChooseEquipmentUISprite extends UISprite 
	{
		
		private var vo:EquiRoleVo;
		private var type2:int;
		private var job:int;
		
		private var equipFirstIdx:int;
		
		private var iview:ChooseEquipmentUI;
		private var listView:DefaultListView;
		
		public function ChooseEquipmentUISprite() 
		{
			iview = new ChooseEquipmentUI();
			_view = iview;
			
			listView = new DefaultListView(iview, iview, 6, false, false);
			listView.init(350, 300, 170, 95, -174, -144);
			listView.setGridItem(EquipItem, EquiItemRender);
			listView.pagination = new Pagination();
			listView.pagination.y = 169;
			listView.pagination.x = 0;
			
			iview.closeBtn.addEventListener(MouseEvent.CLICK, closeWindow);
		}
		
		public function getFirstAvailableView():DisplayObject
		{
			var arr:Array = listView.data;
			var len:int = arr.length;
			if (len > listView.pageLength) len = listView.pageLength;
			for (var i:int = equipFirstIdx; i < len; i++)
			{
				var vo:EquiCompareVo = EquiCompareVo(arr[i]);
				if (vo.roleLevel >= vo.now.level)
				{
					var item:GridItem = listView.getItemByIndex(i);
					return item ? item.view : null;
				}
			}
			return null;
		}
		
		private function closeWindow(e:Event):void 
		{
			EventManager.removeEventListener(DataManagerEvent.ROLEDATA_CHANGE, freshView);
			closeMe();
		}
		
		public function setData(vo:EquiRoleVo, type2:int, job:int):void
		{
			this.vo = vo;
			this.type2 = type2;
			this.job = job;
			
			var equipments:Array = DataManager.getInstance().roleData.getRole(vo.roleId).equipments || [];
			var arr:Array = equipments[type2 - 61];
			var itemVo:ItemVo = null;
			if (arr)
			{
				itemVo = new ItemVo();
				itemVo.setData( { id:arr[0], cid:arr[1], wear:arr[2] } );
			}
			vo.equi = itemVo ? itemVo._base : null;
			vo.wear = itemVo ? itemVo.wear : 0;
			
			arr = DataManager.getInstance().itemData.getItemListByType2(type2);
			equipFirstIdx = vo.equi ? 1 : 0;
			var tmp:Array = [];
			if (vo.equi)
			{
				tmp[0] = new EquiCompareVo(vo.roleId, vo.roleLevel, vo.equi, vo.equi, "", vo.wear);
			}
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				var equipVo:EquipmentClassVo = arr[i].base as EquipmentClassVo;
				if (equipVo.jobs && -1 == equipVo.jobs.indexOf(job)) continue;
				tmp.push(new EquiCompareVo(vo.roleId, vo.roleLevel, vo.equi, arr[i].base, arr[i].id, arr[i].wear));
			}
			listView.setData(tmp);
			
			EventManager.addEventListener(DataManagerEvent.ROLEDATA_CHANGE, freshView);
		}
		
		private function freshView(e:DataManagerEvent):void 
		{
			setData(vo, type2, job);
		}
	}
}