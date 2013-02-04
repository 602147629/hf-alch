package happyfish.scene.world.control.collision
{
	import flash.geom.Rectangle;
	/**
	 * 参与碰撞的物体
	 * @author XiaJunJie
	 */
	public class CollisionObject
	{
		public var id:String;
		public var selfRange:Rectangle; //自身的占地范围
		public var collisionRadius:int; //碰撞半径
		public var collisionRange:Rectangle; //碰撞范围 = 自身范围+周围(碰撞半径)圈
		public var collisionInfo:Object; //碰撞信息 格式[key:目标碰撞物ID, value:碰撞区域(Rectangle)]
		
		public var changed:Boolean; //一个标记 自从上一次检测以来 该物件的位置是否发生过改变
		public var exist:Boolean;
		
		public function CollisionObject(id:String, size_x:int, size_z:int, collisionRadius:int = 0)
		{
			this.id = id;
			this.collisionRadius = collisionRadius;
			
			selfRange = new Rectangle(0, 0, size_x, size_z);
			collisionRange = new Rectangle();
			collisionInfo = new Object;
			
			changed = false;
			exist = true;
		}
		
	}

}