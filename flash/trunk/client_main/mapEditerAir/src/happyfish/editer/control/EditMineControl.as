package happyfish.editer.control 
{
	import flash.display.Sprite;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditMineClassVo;
	import happyfish.editer.model.vo.EditMineVo;
	import happyfish.editer.scene.view.EditMineView;
	import happyfish.editer.view.Main;
	/**
	 * ...
	 * @author ...
	 */
	public class EditMineControl extends EditDecorControl 
	{
		
		public function EditMineControl(_main:Main,mapview:Sprite) 
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
			
			var tmpclass:EditMineClassVo = EditDataManager.getInstance().getVar("curSelectClass") as EditMineClassVo;
			var decorvo:EditMineVo = new EditMineVo().setValue({id:0,cid:tmpclass.cid,avatarId:tmpclass.avatarId});
			decorvo.x = pos.x;
			decorvo.z = pos.z;
			decorvo.sceneId = main.mapSprite.data.sceneId;
			var tmpitem:EditMineView=new EditMineView(decorvo,main.mapSprite.itemContainer,addDecor_complete);
		}
		
	}

}