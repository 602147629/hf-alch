package happyfish.utils.display 
{
	import com.greensock.TweenMax;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.display.view.UISprite;
	import happymagic.manager.DataManager;
	import happymagic.scene.world.SceneType;
	/**
	 * ...
	 * @author ZC
	 */
	public class TitleSprite extends UISprite
	{
		private var iview:TitleControlUi;
		private var timeid:int;
		public function TitleSprite() 
		{
			_view = new TitleControlUi();
			iview = _view as TitleControlUi;
			
			iview.mouseChildren = false;
			iview.mouseEnabled = false;
		}
		
		override public function init():void 
		{
			super.init();
			
			if (DataManager.getInstance().curSceneType == SceneType.TYPE_HOME)
			{
				closeMe(true);
			}
			else
			{
				timeid = setTimeout(setData, 1500);
			}
		}
		
		public function setData():void
		{
			 timeid = 0;
			 var namestr:String = DataManager.getInstance().getSceneClassById(DataManager.getInstance().currentUser.currentSceneId).name;
			
			 var mcshower:McShower = new McShower(TitleControlMc, iview, null, null, complete);	
			 TextFieldUtil.autoSetTxtDefaultFormat(mcshower.mc);
			
			 (mcshower.mc as TitleControlMc).body["nameTxt"].text = namestr;
			 mcshower.setMcScaleXY(0.8,0.8);
			 //thismc.addFrameScript(thismc.totalFrames - 1, function():void
			 //{
				//thismc.stop();
				//closeMe(true);
			 //});
			 //iview.addChild(thismc);
		}
		
		private function complete():void 
		{
			closeMe(true);
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			if (timeid)
			{
				clearTimeout(timeid);
			}
			
			super.closeMe(del);
		}
	}

}