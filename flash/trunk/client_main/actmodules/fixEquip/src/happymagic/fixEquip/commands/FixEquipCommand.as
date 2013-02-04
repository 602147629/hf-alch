package happymagic.fixEquip.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.data.ItemData;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class FixEquipCommand extends BaseDataCommand 
	{
		private var callback:Function;
		private var wid:String;
		private var type:int;
		private var roleId:int;
		
		public function fixOne(id:String, roleId:int, callback:Function):void
		{
			type = 0;
			wid = id;
			this.roleId = roleId;
			this.callback = callback;
			load();
			
		}
		
		public function fixAll(callback:Function):void
		{
			type = 1;
			wid = null;
			roleId = -1;
			this.callback = callback;
			load();
		}
		
		public function fixAllWear(callback:Function):void
		{
			type = 2;
			wid = null;
			roleId = -1;
			this.callback = callback;
			load();
		}
		
		private function load():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("fixEquip"), { type:type, wid:wid });
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (result.isSuccess)
			{
				var itemData:ItemData = DataManager.getInstance().itemData;
				var item:ItemVo = null;
				var role:RoleVo = null;
				if (0 == type)
				{
					if (roleId != -1)
					{
						role = DataManager.getInstance().roleData.getRole(roleId);
						for (var i:int = role.equipments.length -1; i >= 0; i--)
						{
							if (role.equipments[i] && role.equipments[i][0] == wid)
							{
								var equipVo:EquipmentClassVo = EquipmentClassVo(itemData.getItemClass(role.equipments[i][1]));
								role.equipments[i][2] = equipVo.maxWear;
								break;
							}
						}
					}else
					{
						item = itemData.getItem(wid);
						item.wear = EquipmentClassVo(item.base).maxWear;
					}
				}else if (1 == type)
				{
					fixedAllRoles();
					fixedAllDepot();
				}else if (2 == type)
				{
					fixedAllRoles();
				}
			}
			
			if (callback != null) callback();
			commandComplete();
		}
		
		private function fixedAllRoles():void
		{
			var list:Array = DataManager.getInstance().roleData.getMyRoles();
			var getClass:Function = DataManager.getInstance().itemData.getItemClass;
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				var arr:Array = RoleVo(list[i]).equipments;
				for (var j:int = (arr ? arr.length - 1 : -1); j >= 0; j--)
				{
					if (!arr[j]) continue;
					var vo:EquipmentClassVo = EquipmentClassVo(getClass(arr[j][1]));
					arr[j][2] = vo.maxWear;
				}
			}
		}
		
		private function fixedAllDepot():void
		{
			var list:Array = DataManager.getInstance().itemData.getItemListByType(ItemType.Equipment);
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				list[i].wear = list[i]._base.maxWear;
			}
		}
		
	}

}