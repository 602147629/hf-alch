package happymagic.roleInfo.vo 
{
	import happymagic.model.vo.classVo.EquipmentClassVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class EquiRoleVo 
	{
		public var roleId:int;
		public var roleLevel:int;
		public var equi:EquipmentClassVo;
		public var wear:int;
		
		public function EquiRoleVo(id:int, roleLevel:int, equi:EquipmentClassVo, wear:int)
		{
			this.roleId = id;
			this.roleLevel = roleLevel;
			this.equi = equi;
			this.wear = wear;
		}
	}

}