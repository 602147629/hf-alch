package happymagic.order 
{
	import com.friendsofed.isometric.Point3D;
	import happyfish.scene.world.WorldState;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.view.Customer;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.MagicWorld;
	import happymagic.scene.world.SceneType;
	/**
	 * ...
	 * @author lite3
	 */
	public class CustomerFactory 
	{
		
		public static function createCustomer(order:OrderVo):Customer 
		{
			var curSceneType:int = DataManager.getInstance().curSceneType;
			if (curSceneType != SceneType.TYPE_HOME) return null;
			var worldState:WorldState = DataManager.getInstance().worldState;
			var door:Door = MagicWorld(worldState.world).getCustomDoor();
			var p:Point3D = door.getOutIsoPosition();
			var customer:Customer = new Customer(order, worldState, p.x, p.z); 
			worldState.world.addItem(customer);
			door.openDoor();
			return customer;
		}
		
	}

}