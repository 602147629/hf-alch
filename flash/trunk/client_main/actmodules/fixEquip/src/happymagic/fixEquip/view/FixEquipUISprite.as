package happymagic.fixEquip.view 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.events.GridPageEvent;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.fixEquip.commands.FixEquipCommand;
	import happymagic.fixEquip.model.Data;
	import happymagic.fixEquip.model.FixEquipItemVo;
	import happymagic.fixEquip.view.ui.FixEquipItemUI;
	import happymagic.fixEquip.view.ui.FixEquipUI;
	import happymagic.fixEquip.view.ui.render.FixEquipItem;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class FixEquipUISprite extends UISprite 
	{
		private var iview:FixEquipUI;
		private var listView:DefaultListView;
		private var fixWearCoin:int;
		private var fixAllCoin:int;
		
		private var icon:IconView;
		private var list:Array = [];
		
		public function FixEquipUISprite() 
		{
			iview = new FixEquipUI();
			_view = iview;
			
			var rect:Rectangle = new Rectangle(iview.faceBorder.x, iview.faceBorder.y, iview.faceBorder.width, iview.faceBorder.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.faceBorder));
			iview.removeChild(iview.faceBorder);
			iview.faceBorder = null;
			icon.setData(Data.instance.npcFace);
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			iview.fixAllTxt.mouseEnabled = false;
			iview.fixWearTxt.mouseEnabled = false;
			
			
			listView = new DefaultListView(iview, iview, 8, false, false);
			listView.init(460, 270, 110, 132, -227, -70);
			listView.setGridItem(FixEquipItem, FixEquipItemUI);
			listView.selectCallBack = fixOne;
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			
			show();
		}
		
		public function show():void
		{
			getList();
			listView.setData(list);
			iview.fixAllTxt.text = fixAllCoin + "";
			iview.fixWearTxt.text = fixWearCoin + "";
			var idx:int = Math.random() * Data.instance.chatList.length;
			iview.chatTxt.text = Data.instance.chatList[idx];
		}
		
		private function getList():void 
		{
			list.length = 0;
			fixAllCoin = 0;
			fixWearCoin = 0;
			Data.instance.smithyBuildLevel = DataManager.getInstance().currentUser.viliageInfo[DataManager.getInstance().gameSetting.smithyBuildId];
			var getClass:Function = DataManager.getInstance().itemData.getItemClass;
			var arr:Array = DataManager.getInstance().roleData.getMyRoles();
			var len:int = arr.length;
			for (var i:int = 0; i < len; i++)
			{
				var role:RoleVo = RoleVo(arr[i]);
				var equipLen:int = role.equipments ? role.equipments.length : 0;
				for (var j:int = 0; j < equipLen; j++)
				{
					var tmp:Array = role.equipments[j] as Array;
					if (!tmp) continue;
					var equipVo:EquipmentClassVo = EquipmentClassVo(getClass(tmp[1]));
					if (0 == equipVo.maxWear || tmp[2] == equipVo.maxWear) continue;
					var price:int = Math.round(equipVo.worth * 0.01) * (equipVo.maxWear - tmp[2]);
					fixWearCoin += price;
					fixAllCoin += price;
					list.push(new FixEquipItemVo(tmp[0], tmp[1], tmp[2], role.id, price, equipVo));
				}
			}
			arr = DataManager.getInstance().itemData.getItemListByType(ItemType.Equipment);
			len = arr.length;
			for (i = 0; i < len; i++)
			{
				var itemVo:ItemVo = arr[i] as ItemVo;
				if (0 == itemVo._base.maxWear || itemVo.wear == itemVo._base.maxWear) continue;
				price = Math.round(itemVo._base.worth * 0.01) * (itemVo._base.maxWear - itemVo.wear);
				fixAllCoin += price;
				list.push(new FixEquipItemVo(itemVo.id, itemVo.cid, itemVo.wear, -1, price, EquipmentClassVo(itemVo.base)));
			}
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.closeBtn :
					closeMe(true);
					break;
					
				case iview.fixAllBtn :
					iview.mouseChildren = false;
					new FixEquipCommand().fixAll(fixCompleteHandler);
					break;
					
				case iview.fixWearBtn :
					iview.mouseChildren = false;
					new FixEquipCommand().fixAllWear(fixCompleteHandler);
					break;
				
			}
		}
		
		private function fixOne(e:GridPageEvent):void
		{
			iview.mouseChildren = false;
			var vo:FixEquipItemVo = FixEquipItemVo(FixEquipItem(e.item).data);
			new FixEquipCommand().fixOne(vo.id, vo.roleId, fixCompleteHandler);
		}
		
		private function fixCompleteHandler():void 
		{
			iview.mouseChildren = true;
			show();
		}
		
	}

}