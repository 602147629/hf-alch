package happymagic.mix 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.mix.commands.MixItemCompleteCommand;
	import happymagic.mix.events.MixEvent;
	import happymagic.mix.view.MixImmediateCompleteUISprite;
	import happymagic.mix.view.MixUISprite;
	import happymagic.model.vo.MixVo;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author lite3
	 */
	public class MixMain extends Sprite
	{
		
		public static const MixViewModuleName:String = "mixListView";
		public static const MixCompleteModuleName:String = "mixImmediateCompleteView";
		public static const timer:Timer = new Timer(1000);;
		
		public function MixMain() 
		{
			// MainEvent.MAIN_INIT_COMPLETER
			EventManager.addEventListener("mainInitComplete", init);
		}
		
		private function init(e:Event):void 
		{
			var moduleVo:ModuleVo = new ModuleVo;
			moduleVo.name = MixCompleteModuleName;
			moduleVo.className = "happymagic.mix.view.MixImmediateCompleteUISprite";
			moduleVo.single = false;
			ModuleManager.getInstance().addModule(moduleVo);
			
			moduleVo = new ModuleVo();
			moduleVo.name = MixViewModuleName;
			moduleVo.className = "happymagic.mix.view.MixUISprite";
			ModuleManager.getInstance().addModule(moduleVo);
			
			EventManager.addEventListener(SceneEvent.SCENE_COMPLETE, sceneCompleteHandler);
			EventManager.addEventListener(GameMouseEvent.GAME_MOUSE_EVENT, furnaceClickHandler);
			EventManager.addEventListener(MixEvent.MIX_ITEM, showMixHandler);
			EventManager.addEventListener(MixEvent.MIX_ITEMS, showMixHandler);
			
			timer.addEventListener(TimerEvent.TIMER, timerHandler);
		}
		
		private function timerHandler(e:TimerEvent):void 
		{
			var data:DataManager = DataManager.getInstance();
			var list:Vector.<MixVo> = data.mixData.getCurMixList();
			if (0 == list.length)
			{
				timer.stop();
				return;
			}
			
			var getDecorById:Function = MagicWorld(data.worldState.world).getDecorById;
			
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				var furnace:FurnaceDecor = getDecorById(list[i].furnaceId);
				if (!furnace)
				{
					timer.stop();
					return;
				}
				var state:String = list[i].remainingTime > 0 ? FurnaceDecor.STATE_WORKING : FurnaceDecor.STATE_COMPLETE;
				if (furnace.state != state) furnace.setState(state);
			}
		}
		
		
		private function sceneCompleteHandler(e:Event):void 
		{
			var data:DataManager = DataManager.getInstance();
			var arr:Array = MagicWorld(data.worldState.world).getFurnaceList();
			
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				var furnace:FurnaceDecor = arr[i] as FurnaceDecor;
				var mixVo:MixVo = data.mixData.getCurMix(furnace.decorVo.id);
				var state:String;
				if (!mixVo) state = FurnaceDecor.STATE_IDLE;
				else if (mixVo.remainingTime > 0) state = FurnaceDecor.STATE_WORKING;
				else state = FurnaceDecor.STATE_COMPLETE;
				furnace.setState(state);
			}
			
			// 倒计时
			if (0 == data.mixData.getCurMixList().length) return;
			timer.start();
		}
		
		
		// 工作台点击
		private function furnaceClickHandler(event:Event):void 
		{
			var e:GameMouseEvent = GameMouseEvent(event);
			if (e.mouseEventType != GameMouseEvent.CLICK) return;
			if(e.itemType != "FurnaceDecor") return;
			
			var furnace:FurnaceDecor = e.item as FurnaceDecor;
			var mixVo:MixVo = DataManager.getInstance().mixData.getCurMix(furnace.decorVo.id);
			// 创建合成
			if (!mixVo)
			{
				EventManager.getInstance().dispatchEvent(new MixEvent(MixEvent.MIX_ITEMS, false, false, 0, furnace.decorVo.id));
			}
			// 结束合成
			else if (0 == mixVo.remainingTime)
			{
				new MixItemCompleteCommand().complete(furnace.decorVo.id, true);
			}
			// 加速合成
			else
			{
				var sp:MixImmediateCompleteUISprite = MixImmediateCompleteUISprite(ModuleManager.getInstance().showModule(MixCompleteModuleName));
				DisplayManager.uiSprite.setBg(sp);
				sp.setData(mixVo);
			}
		}
		
		private function showMixHandler(e:MixEvent):void 
		{
			var mixUISprite:MixUISprite = ModuleManager.getInstance().showModule(MixViewModuleName) as MixUISprite;
			DisplayManager.uiSprite.setBg(mixUISprite);
			switch(e.type)
			{
				case MixEvent.MIX_ITEM :
					mixUISprite.showMixByMid(e.mid, e.furnaceId);
					break;
					
				case MixEvent.MIX_ITEMS :
					mixUISprite.showMixByFurnace(e.furnaceId);
					break;
			}
		}
		
	}

}