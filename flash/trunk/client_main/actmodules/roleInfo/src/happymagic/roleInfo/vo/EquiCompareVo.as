package happymagic.roleInfo.vo 
{
	import happymagic.model.vo.classVo.EquipmentClassVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class EquiCompareVo 
	{
		public var roleId:int;
		public var roleLevel:int;
		public var old:EquipmentClassVo;
		public var now:EquipmentClassVo;
		public var nowId:String;
		public var nowWear:int;
		
		public function EquiCompareVo(roleId:int, roleLevel:int, old:EquipmentClassVo, now:EquipmentClassVo, nowId:String, nowWear:int)
		{
			this.roleId = roleId;
			this.roleLevel = roleLevel;
			this.old = old;
			this.now = now;
			this.nowId = nowId;
			this.nowWear = nowWear;
		}
	}

}