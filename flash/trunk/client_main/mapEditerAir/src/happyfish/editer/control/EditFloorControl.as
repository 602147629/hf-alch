package happyfish.editer.control 
{
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditFloorVo;
	import happyfish.editer.view.Main;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditFloorControl extends EditerControl 
	{
		private var layer:int;
		
		public function EditFloorControl(_main:Main,mapview:Sprite,_layer:int=1) 
		{
			layer = _layer;
			super(_main,mapview);
		}
		
		override protected function beginDrag(e:MouseEvent):void 
		{
			super.beginDrag(e);
			
			action();
		}
		
		override protected function mouseMoveFun(e:MouseEvent):void 
		{
			super.mouseMoveFun(e);
			
			if (!e.buttonDown) 
			{
				return;
			}
			
			if (pos.x == oldPos.x && pos.z == oldPos.z) 
			{
				return;
			}
			action();
		}
		
		private function action():void 
		{
			switch (main.editer.editTypeList.selectedItem.data) 
			{
				case "4":
				if (layer==1) 
				{
					addFloor();
				}else {
					addFloor2();
				}
				
				break;
				
				case "5":
				if (layer==1) 
				{
					removeFloor();
				}else {
					removeFloor2();
				}
				break;
			}
		}
		
		private function addFloor():void {
			//trace("addFloor",new Date().getTime());
			var floor:EditFloorVo = EditDataManager.getInstance().getVar("curSelectClass") as EditFloorVo;
			floor = new EditFloorVo().setClass(floor);
			main.mapSprite.setFloor(pos.x, pos.z, floor);
			
		}
		
		private function addFloor2():void {
			//trace("addFloor",new Date().getTime());
			var floor:EditFloorVo = EditDataManager.getInstance().getVar("curSelectClass") as EditFloorVo;
			floor = new EditFloorVo().setClass(floor);
			main.mapSprite.setFloor2(pos.x, pos.z, floor);
			
		}
		
		private function removeFloor():void {
			var floor:EditFloorVo = new EditFloorVo();
			floor.className = "";
			floor.cid = 0;
			main.mapSprite.setFloor(pos.x, pos.z, floor);
		}
		
		private function removeFloor2():void {
			var floor:EditFloorVo = new EditFloorVo();
			floor.className = "";
			floor.cid = 0;
			main.mapSprite.setFloor2(pos.x, pos.z, floor);
		}
		
	}

}