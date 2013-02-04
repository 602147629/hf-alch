package happymagic.model.data 
{
	import happyfish.manager.EventManager;
	import happyfish.time.Time;
	import happymagic.events.DataManagerEvent;
	import happymagic.model.vo.RoleVo;
	/**
	 * 角色信息管理
	 * @author 
	 */
	public class RoleData 
	{
		// Map<count:[level,coin,gem]>佣兵位的解锁所需的价格
		public var rolePosPriceMap:Object;
		
		private const myRoles:Array = [];
		public function RoleData() 
		{
			
		}
		
		public function getRole(id:int):RoleVo
		{
			for (var i:int = myRoles.length - 1; i >= 0; i--)
			{
				if (RoleVo(myRoles[i]).id == id) return RoleVo(myRoles[i]);
			}
			return null;
		}
		
		public function changeRoles(value:Array):void {
			var addOrRemove:Boolean;
			
			var geted:Boolean;
			for (var i:int = 0; i < value.length; i++) 
			{
				geted = changeRole(value[i],false);
				if (!geted) 
				{
					addOrRemove = true;
					addRole(new RoleVo().setData(value[i]) as RoleVo,false);
				}
			}
			dispatchRoleChange(addOrRemove);
		}
		
		public function removeRoles(value:Array):void {
			for (var i:int = 0; i < value.length; i++) 
			{
				removeRole(value[i],false);
			}
			dispatchRoleChange(true);
		}
		
		/**
		 * 
		 * @param	value
		 * @param	dispatchFlag	是否要广播数据变化事件
		 * @return	是否找到原有此角色
		 */
		public function changeRole(value:Object, dispatchFlag:Boolean = true):Boolean {
			
			var tmp:RoleVo;
			tmp = getRole(value.id);
			if (tmp) 
			{
				tmp.setData(value);
				if (dispatchFlag) 
				{
					dispatchRoleChange();
				}
				return true;
			}else {
				return false;
			}
		}
		
		private function dispatchRoleChange(addOrRemove:Boolean = false):void {
			var event:DataManagerEvent = new DataManagerEvent(DataManagerEvent.ROLEDATA_CHANGE);
			event.role_addOrRemove = addOrRemove;
			EventManager.getInstance().dispatchEvent(event);
		}
		
		public function addRole(role:RoleVo,dispatchFlag:Boolean=true):void
		{
			myRoles.push(role);
			if (dispatchFlag) 
			{
				dispatchRoleChange(true);
			}
		}
		
		public function removeRole(id:int,dispatchFlag:Boolean=true):void
		{
			for (var i:int = myRoles.length - 1; i >= 0; i--)
			{
				if (myRoles[i].id == id)
				{
					myRoles.splice(i, 1);
					if (dispatchFlag) 
					{
						dispatchRoleChange(true);
					}
					break;
				}
			}
		}
		
		public function getCanPvPRoles():Array {
			var tmparr:Array = new Array();
			var occtime:int;
			for (var i:int = 0; i < myRoles.length; i++) 
			{
				occtime = Time.getRemainingTimeByEnd(myRoles[i].occCdTime);
				if (myRoles[i].occCdTime==0 || occtime==0 ) 
				{
					
					tmparr.push(myRoles[i]);
				}
			}
			return tmparr;
		}
		
		public function getMyRoles():Array {
			return myRoles;
		}
		
		public function setRoles(arr:Array):void {
			var tmp:RoleVo;
			for (var i:int = 0; i < arr.length; i++) 
			{
				tmp = new RoleVo().setData(arr[i]) as RoleVo;
				myRoles.push(tmp);
			}
			dispatchRoleChange(true);
		}
		
	}

}