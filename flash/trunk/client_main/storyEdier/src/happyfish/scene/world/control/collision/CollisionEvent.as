package happyfish.scene.world.control.collision
{
	import flash.events.Event;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class CollisionEvent extends Event
	{
		public var objId:String; //碰撞物的ID
		public var targetObjId:String; //另一个碰撞物的ID
		public var collisionArea:Rectangle;
		
		public function CollisionEvent(type:String,id1:String,id2:String,area:Rectangle = null) 
		{
			this.objId = id1;
			this.targetObjId = id2;
			this.collisionArea = area;
			
			super(type);
		}
		
		override public function toString():String
		{
			var result:String = objId + " " + type + " " + targetObjId;
			if(collisionArea) result += " " + collisionArea.toString();
			return result;
		}
		
		public static const COLLISION_IN:String = "collisionIn";
		public static const COLLISION_OUT:String = "collisionOut";
		public static const COLLISION_OVER:String = "collisionOver";
	}

}