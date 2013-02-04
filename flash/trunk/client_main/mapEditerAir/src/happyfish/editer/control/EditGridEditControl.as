package happyfish.editer.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.editer.view.Main;
	import happyfish.editer.view.TileView;
	import happyfish.scene.iso.IsoUtil;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditGridEditControl extends EditerControl 
	{
		
		public function EditGridEditControl(_main:Main,mapview:Sprite) 
		{
			super(_main,mapview);
		}
		
		override protected function beginDrag(e:MouseEvent):void 
		{
			super.beginDrag(e);
			
			setWalkAble(pos);
			
		}
		
		private function setWalkAble(_pos:Point3D):void {
			if (main.editer.editTypeList.selectedItem.label=="可行") 
			{
				main.mapSprite.gridView.setWalkAble(_pos.x,_pos.z,true);
			}else if (main.editer.editTypeList.selectedItem.label=="不可行") {
				main.mapSprite.gridView.setWalkAble(_pos.x,_pos.z,false);
			}
		}
		
		override protected function mouseMoveFun(e:MouseEvent):void 
		{
			super.mouseMoveFun(e);
			
			if (!e.buttonDown) 
			{
				return;
			}
			
			setWalkAble(pos);
		}
		
	}

}