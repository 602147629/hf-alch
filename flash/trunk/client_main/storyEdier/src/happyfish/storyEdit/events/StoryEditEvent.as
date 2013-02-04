package happyfish.storyEdit.events 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.storyEdit.model.vo.EditStoryActionVo;
	import happyfish.storyEdit.model.vo.EditStoryActorVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class StoryEditEvent extends Event 
	{
		public static const REQUEST_ADD_ACTION:String = "requestAddAction";
		static public const REQUEST_EDIT_ACTION:String = "requestEditAction";
		static public const SHOW_ACTOR_EDIT:String = "showActorEdit";
		static public const GET_NODE:String = "getNode";
		
		
		public var actor:EditStoryActorVo;
		public var actorId:int;
		public var actionStep:int;
		public var action:EditStoryActionVo;
		
		public var point:Point;
		public var node:Point3D;
		public function StoryEditEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new StoryEditEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("StoryEditEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}