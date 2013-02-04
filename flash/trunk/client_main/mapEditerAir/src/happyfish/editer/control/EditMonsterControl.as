package happyfish.editer.control 
{
	import flash.display.Sprite;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditMonsterClassVo;
	import happyfish.editer.model.vo.EditMonsterVo;
	import happyfish.editer.scene.view.EditMonsterView;
	import happyfish.editer.view.Main;
	/**
	 * ...
	 * @author ...
	 */
	public class EditMonsterControl extends EditDecorControl 
	{
		
		public function EditMonsterControl(_main:Main,mapview:Sprite) 
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
			
			var tmpclass:EditMonsterClassVo = EditDataManager.getInstance().getVar("curSelectClass") as EditMonsterClassVo;
			var decorvo:EditMonsterVo = new EditMonsterVo().setValue({id:0,cid:tmpclass.cid,avatarId:tmpclass.avatarId});
			decorvo.x = pos.x;
			decorvo.z = pos.z;
			decorvo.sceneId = main.mapSprite.data.sceneId;
			var tmpitem:EditMonsterView=new EditMonsterView(decorvo,main.mapSprite.itemContainer,addDecor_complete);
		}
		
	}

}