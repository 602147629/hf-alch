package happymagic.scene.world.grid.item 
{
	import flash.events.MouseEvent;
	import happyfish.display.ui.Tooltips;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.WorldState;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.manager.DataManager;
	/**
	 * 传送门 2011.11.14
	 * @author XiaJunJie
	 */
	public class Portal extends Decor
	{
		public var targetSceneId:int;
		
		public function Portal($data:Object, $worldState:WorldState,__callBack:Function=null)  
		{
			super($data, $worldState, __callBack);
			targetSceneId = $data.targetSceneId;
			typeName = "Portal";
			
			
			
			
		}
		
		override protected function makeView():IsoSprite 
		{
			return super.makeView();
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
		}
		
		override protected function bodyComplete():void 
		{
			super.bodyComplete();
			mouseEvent = true;
			Tooltips.getInstance().register(this.asset, _data.targetSceneName, Tooltips.getInstance().getBg("defaultBg"));
		}
		
	}

}