package happymagic.guide.api 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.guide.GuideManager;
	import happyfish.manager.EventManager;
	import happymagic.display.control.NpcChatCommand;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.MixVo;
	import happymagic.scene.world.bigScene.NpcView;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author lite3
	 */
	public class SceneAPI 
	{
		private var guideId:String;
		private var stepId:String;
		
		public static const FurnaceIdl:String = "idl";
		public static const FurnaceWorking:String = "working";
		public static const FurnaceComplete:String = "complete";
		
		public function get camera():Sprite { return DisplayManager.camera; }
		
		public function getNpc(id:int):NpcView
		{
			return MagicWorld(DataManager.getInstance().worldState.world).getNpcById(id);
		}
		
		public function getFurnace(cid:int):FurnaceDecor
		{
			var arr:Array = MagicWorld(DataManager.getInstance().worldState.world).getFurnaceList();
			for (var i:int = arr ? arr.length - 1 : -1; i >= 0; i--)
			{
				var vo:FurnaceDecor = arr[i];
				if (vo.data.id == cid) return vo;
			}
			return null;
		}
		
		public function furnaceState(cid:int):String
		{
			var furnace:FurnaceDecor = getFurnace(cid);
			if (!furnace) return null;
			var vo:MixVo = DataManager.getInstance().mixData.getCurMix(furnace.decorVo.id);
			if (!vo) return FurnaceIdl;
			if (vo.remainingTime > 0) return FurnaceWorking;
			return FurnaceComplete;
		}
		
		public function dataEnd2(guideId:String, stepId:String):void
		{
			this.guideId = guideId;
			this.stepId = stepId;
			EventManager.addEventListener(SceneEvent.SCENE_DATA_COMPLETE, sceneCompleteHandler);
		}
		
		public function end2(guideId:String, stepId:String):void
		{
			this.guideId = guideId;
			this.stepId = stepId;
			if (MagicWorld(DataManager.getInstance().worldState.world).initFlg)
			{
				EventManager.addEventListener(SceneEvent.SCENE_COMPLETE, sceneCompleteHandler);
			}else
			{
				sceneCompleteHandler(null);
			}
		}
		
		private function sceneCompleteHandler(e:SceneEvent):void 
		{
			EventManager.removeEventListener(SceneEvent.SCENE_COMPLETE, sceneCompleteHandler);
			EventManager.removeEventListener(SceneEvent.SCENE_DATA_COMPLETE, sceneCompleteHandler);
			GuideManager.getInstance().gotoGuide(guideId);
			GuideManager.getInstance().gotoGuideStepById(stepId);
		}
		
	}

}