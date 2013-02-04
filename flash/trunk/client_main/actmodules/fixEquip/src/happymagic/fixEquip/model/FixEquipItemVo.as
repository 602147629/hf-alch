package happymagic.fixEquip.model 
{
	import happymagic.model.vo.classVo.EquipmentClassVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class FixEquipItemVo 
	{
		public var id:String;
		public var cid:int;
		public var wear:int;
		public var fixPrice:int;
		public var base:EquipmentClassVo;
		public var roleId:int = -1; // -1 表示不再身上
		
		public function FixEquipItemVo(id:String, cid:int, wear:int, roleId:int, fixPrice:int, base:EquipmentClassVo)
		{
			this.id = id;
			this.cid = cid;
			this.wear = wear;
			this.roleId = roleId;
			this.base = base;
			this.fixPrice = fixPrice;
		}
		
		public function get inDepot():Boolean { return roleId != -1; }
		
	}

}