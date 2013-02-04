package happyfish.editer.control 
{
	import flash.display.Sprite;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditNpcClassVo;
	import happyfish.editer.model.vo.EditNpcVo;
	import happyfish.editer.scene.view.EditNpcView;
	import happyfish.editer.view.Main;
	/**
	 * ...
	 * @author 
	 */
	public class EditNpcControl extends EditDecorControl 
	{
		
		public function EditNpcControl(_main:Main,mapview:Sprite) 
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
			
			var tmpclass:Object = EditDataManager.getInstance().getVar("curSelectClass") as Object;
			var decorvo:EditNpcVo = new EditNpcVo().setValue({id:0,cid:tmpclass.cid});
			decorvo.x = pos.x;
			decorvo.z = pos.z;
			decorvo.sceneId = main.mapSprite.data.sceneId;
			var tmpitem:EditNpcView=new EditNpcView(decorvo,main.mapSprite.itemContainer,addDecor_complete);
		}
	}

}