package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.utils.Dictionary;
	import happyfish.scene.astar.Node;
	import happyfish.scene.astar.NodesUtil;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.Point3DUtil;
	import happymagic.scene.world.grid.item.Door;
	/**
	 * ...
	 * @author lite3
	 */
	public class DoorForPersonControl
	{
		private var _state:WorldState;
		private const personMap:Dictionary = new Dictionary(true);
		private const doorArr:Vector.<Door> = new Vector.<Door>();
		private const nodeArr:Vector.<Node> = new Vector.<Node>();
		
		public function DoorForPersonControl(__state:WorldState) 
		{
			_state = __state;
		}
		
		public function removePerson(person:Person):void {
			
			delete personMap[person];
		}
		
		public function addPerson(person:Person):void {
			if (0 == doorArr.length) return;
			
			var pathEnd:int = person.path.length - 1;
			if ( -1 == pathEnd) return;
			
			for (var i:int = doorArr.length - 1; i >= 0; i--)
			{
				if (!NodesUtil.equalsPos(nodeArr[i], person.path[pathEnd])) continue;
				if (0 == pathEnd)
				{
					openDoor(doorArr[i]);
					return;
				}
				var node:Node = person.path[pathEnd - 1];
				personMap[person] = new DoorOpenInfo(person, doorArr[i], new Point3D(node.x, 0, node.y));
			}
		}
		
		public function checkPerson(person:Person):void
		{
			var info:DoorOpenInfo = personMap[person] as DoorOpenInfo;
			if (!info) return;
			if (Point3DUtil.equals(person.gridPos, info.p))
			{
				removePerson(person);
				openDoor(info.door);
			}
		}
		
		private function openDoor(door:Door):void
		{
			door.openDoor();
		}
		
		/**
		 * 整理获取当前场景内所有的门
		 */
		public function getAllDoorNodes():void
		{
			doorArr.length = 0;
			nodeArr.length = 0;
			var tmplist:Array = _state.world.items;
			for (var i:int = tmplist.length - 1; i >= 0; i--) 
			{
				var door:Door = tmplist[i] as Door;
				if (!door) continue;
				doorArr.push(door);
				nodeArr.push(door.getNode());
			}
		}
		
		//public function closeDoor(door:Door):void {
			//door.closeDoor();
			//delete disableList[door];
		//}
		
	}

}
import com.friendsofed.isometric.Point3D;
import happyfish.scene.world.grid.Person;
import happymagic.scene.world.grid.item.Door;

final class DoorOpenInfo
{
	public var person:Person;
	public var door:Door;
	public var p:Point3D;
	
	public function DoorOpenInfo(person:Person, door:Door, p:Point3D)
	{
		this.person = person;
		this.door = door;
		this.p = p;
	}
}