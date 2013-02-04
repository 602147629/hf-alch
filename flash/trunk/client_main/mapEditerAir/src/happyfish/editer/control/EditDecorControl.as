package happyfish.editer.control 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditDecorClassVo;
	import happyfish.editer.model.vo.EditDecorVo;
	import happyfish.editer.scene.view.EditDecorView;
	import happyfish.editer.scene.view.EditIsoItem;
	import happyfish.editer.view.Main;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditDecorControl extends EditerControl 
	{
		private var dragControl:EditDragIsoItemCommand;
		
		public function EditDecorControl(_main:Main,mapview:Sprite) 
		{
			super(_main, mapview);
			
			//mapview.addEventListener(MouseEvent.CLICK, mapClickFun,true);
		}
		
		override protected function beginDrag(e:MouseEvent):void 
		{
			super.beginDrag(e);
			
			action();
			
		}
		
		override protected function upDrag(e:MouseEvent):void 
		{
			super.upDrag(e);
			
			if (dragControl) {
				dragControl.stop();
				dragControl = null;
			}
		}
		
		override protected function mouseMoveFun(e:MouseEvent):void 
		{
			super.mouseMoveFun(e);
			
			if (!e.buttonDown) 
			{
				return;
			}
			
			action();
		}
		
		protected function action():void {
			switch (main.editer.editTypeList.selectedItem.data) 
			{
				case "4":
				addDecor();
				break;
				
				case "5":
				removeDecor();
				break;
				
				case "8":
				moveDecor();
				break;
			}
		}
		
		private function moveDecor():void 
		{
			var tmp:EditIsoItem = main.mapSprite.itemContainer.getItemByPos(pos.x,pos.z);
			if (!tmp) 
			{
				return;
			}
			if (!dragControl) 
			{
				dragControl = new EditDragIsoItemCommand(tmp, main);
			}
		}
		
		private function stopDrag():void 
		{
			dragControl.stop();
			dragControl = null;
		}
		
		public function removeDecor():void {
			if (!main.mapSprite.itemContainer.getItemByPos(pos.x,pos.z)) 
			{
				return;
			}
			main.mapSprite.itemContainer.removeIsoChild(main.mapSprite.itemContainer.getItemByPos(pos.x,pos.z));
		}
		
		public function addDecor():void {
			if (pos.x<0 || pos.x>mapClass.numCols-1 || pos.z<0 || pos.z>mapClass.numCols-1) 
			{
				return;
			}
			
			if (main.mapSprite.itemContainer.getItemByPos(pos.x,pos.z)) 
			{
				return;
			}
			
			var tmpclass:EditDecorClassVo = EditDataManager.getInstance().getVar("curSelectClass") as EditDecorClassVo;
			var decorvo:EditDecorVo = new EditDecorVo().setValue({id:0,cid:tmpclass.cid});
			decorvo.x = pos.x;
			decorvo.z = pos.z;
			decorvo.sceneId = main.mapSprite.data.sceneId;
			var tmpitem:EditDecorView=new EditDecorView(decorvo,main.mapSprite.itemContainer,addDecor_complete);
		}
		
		protected function addDecor_complete():void 
		{
			main.mapSprite.itemContainer.sort();
		}
		
	}

}