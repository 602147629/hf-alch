package happymagic.guide.api 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.events.Event;
	import happyfish.guide.GuideManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleAPI 
	{
		private var dragGuideId:String;
		private var dragStepId:String;
		private var dropGuideId:String;
		private var dropStepId:String;
		
		private var curDragId:int;
		
		public function mainHasEquip():Boolean 
		{
			var role:RoleVo = DataManager.getInstance().roleData.getRole(0);
			if (!role.equipments) return false;
			var arr:Array = role.equipments[0];
			return arr != null;
		}
		
		public function get roleFormationNodeView():DisplayObject
		{
			var formation:DisplayObjectContainer = ModuleManager.getInstance().getModule("MySoldierListUISprite")["formationView"];
			for (var i:int = 9; i <= 17; i++)
			{
				var tile:DisplayObject = formation.getChildByName("tile" + i);
				if (tile["avatar"])
				{
					curDragId = i;
					return tile;
				}
			}
			return null;
		}
		
		public function get freeFormationNodeView():DisplayObject
		{
			var formation:DisplayObjectContainer = ModuleManager.getInstance().getModule("MySoldierListUISprite")["formationView"];
			for (var i:int = 9; i <= 17; i++)
			{
				var tile:DisplayObject = formation.getChildByName("tile" + i);
				if (curDragId != i && !tile["avatar"]) return tile;
			}
			return null;
		}
		
		public function get isRoleInfoSet():Boolean
		{
			var ui:DisplayObject = ModuleManager.getInstance().getModule("MySoldierListUISprite")["roleView"];
			return ui.stage && ui.visible;
		}
		
		public function get isShowFormationSet():Boolean
		{
			var ui:DisplayObject = ModuleManager.getInstance().getModule("MySoldierListUISprite")["formationView"];
			return ui.stage && ui.visible;
		}
		
		public function formationDragDrop2(dragGuideId:String, dragStepId:String, dropGuideId:String, dropStepId:String):void
		{
			EventManager.addEventListener("roleFormationDrag", formationDragDropHandler);
			EventManager.addEventListener("roleFormationDrop", formationDragDropHandler);
			this.dragGuideId = dragGuideId;
			this.dragStepId  = dragStepId;
			this.dropGuideId = dropGuideId;
			this.dropStepId  = dropStepId;
		}
		
		public function removeFormationDragDrop2():void
		{
			EventManager.removeEventListener("roleFormationDrag", formationDragDropHandler);
			EventManager.removeEventListener("roleFormationDrop", formationDragDropHandler);
		}
		
		private function formationDragDropHandler(e:Event):void 
		{
			removeFormationDragDrop2();
			var guideId:String = "roleFormationDrag" == e.type ? dragGuideId : dropGuideId;
			var stepId:String  = "roleFormationDrag" == e.type ? dragStepId : dropStepId;
			GuideManager.getInstance().gotoGuide(guideId);
			GuideManager.getInstance().gotoGuideStepById(stepId);
		}
	}

}