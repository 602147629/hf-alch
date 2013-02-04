package happymagic.model.vo.data 
{
	import happymagic.model.vo.RoleWorkMapVo;
	import happymagic.model.vo.RoleWorkPointClassVo;
	import happymagic.model.vo.RoleWorkPointVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkData 
	{
		//打工的静态数据
		private var _roleWorkDataStatic:RoleWorkMapVo;
		
		//打工的动态数据	
		private var _roleWorkDataInit:Array = new Array();		
		
		public function RoleWorkData() 
		{
			
		}

		//根据ID获取打工的动态数据
		public function getRoleWorkVo(_id:int):RoleWorkPointVo
		{
			for (var i:int = 0; i < _roleWorkDataInit.length; i++)
			{
				if (_roleWorkDataInit[i].id == _id )
				{
					return _roleWorkDataInit[i];
				}
			}
			return null;
		}	
		
		//根据ID获取打工的静态数据
		public function getRoleWorkClassVo(_id:int):RoleWorkPointClassVo
		{
			for (var i:int = 0; i < roleWorkDataStatic.pointClass.length; i++)
			{
				if (roleWorkDataStatic.pointClass[i].id == _id )
				{
					return roleWorkDataStatic.pointClass[i];
				}
			}
			return null;
		}			
		
		
		public function get roleWorkDataStatic():RoleWorkMapVo 
		{
			return _roleWorkDataStatic;
		}
		
		public function set roleWorkDataStatic(value:RoleWorkMapVo):void 
		{
			_roleWorkDataStatic = value;
		}
		
		public function get roleWorkDataInit():Array 
		{
			return _roleWorkDataInit;
		}
		
		public function set roleWorkDataInit(value:Array):void 
		{
			_roleWorkDataInit = value;
		}
		
	}

}