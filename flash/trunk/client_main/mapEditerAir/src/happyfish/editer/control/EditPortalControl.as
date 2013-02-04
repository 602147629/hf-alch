package happyfish.editer.control 
{
	import flash.display.Sprite;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditPortalClassVo;
	import happyfish.editer.model.vo.EditPortalVo;
	import happyfish.editer.scene.view.EditPortalView;
	import happyfish.editer.view.Main;
	/**
	 * ...
	 * @author ...
	 */
	public class EditPortalControl extends EditDecorControl 
	{
		
		public function EditPortalControl(_main:Main,mapview:Sprite) 
		{
			super(_main, mapview);
		}
		
		override public function addDecor():void 
		{
			if (pos.x<0 || pos.x>mapClass.numCols-1 || pos.z<0 || pos.z>mapClass.numCols-1) 
			{
				return;
			}
			
			if (main.mapSprite.itemContainer.getItemByPos(pos.x,pos.z)) 
			{
				return;
			}
			
			var tmpclass:EditPortalClassVo = EditDataManager.getInstance().getVar("curSelectClass") as EditPortalClassVo;
			var decorvo:EditPortalVo = new EditPortalVo().setValue({id:0,cid:tmpclass.cid});
			decorvo.x = pos.x;
			decorvo.z = pos.z;
			decorvo.sceneId = main.mapSprite.data.sceneId;
			var tmpitem:EditPortalView=new EditPortalView(decorvo,main.mapSprite.itemContainer,addDecor_complete);
		}
		
	}

}