package happyfish.scene.fog
{

	public class FogNode
	{
		public var x:int;
		public var z:int;
		public var explored:Boolean; //是否已被探索
		public var maskableObjects:Object;
		
		public function FogNode(x:int,z:int)
		{
			this.x = x;
			this.z = z;
			
			explored = false;
			maskableObjects = new Object;
		}
		
		public function clone():FogNode
		{
			var result:FogNode = new FogNode(x,z);
			result.explored = explored;
			return result;
		}
	}
}