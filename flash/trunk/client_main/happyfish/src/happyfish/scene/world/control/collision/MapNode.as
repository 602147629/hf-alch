package happyfish.scene.world.control.collision
{
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class MapNode 
	{
		public var x:int;
		public var z:int;
		
		public var objList:Object;
		
		public function MapNode(x:int = 0, z:int = 0):void
		{
			this.x = x;
			this.z = z;
			
			objList = new Object;
		}
		
		public function addObj(obj:CollisionObject):void
		{
			objList[obj.id] = obj;
		}
		
		public function deleteObj(id:String):void
		{
			if (objList[id]) delete objList[id];
		}
	}

}