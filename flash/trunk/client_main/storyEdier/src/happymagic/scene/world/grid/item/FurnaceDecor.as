package happymagic.scene.world.grid.item 
{
	import flash.utils.getQualifiedClassName;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.ui.PersonPaoView;
	/**
	 * ...
	 * @author 
	 */
	public class FurnaceDecor extends Decor 
	{
		private var bubbleUI:PersonPaoView;
		public static const STATE_IDLE:String = "idleState";
		public static const STATE_WORKING:String = "workingState";
		public static const STATE_COMPLETE:String = "completeState";
		
		//当前状态
		public var state:String;
		public function FurnaceDecor($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState, __callBack);
			typeName = "FurnaceDecor";
			
			mouseEvent = true;
		}
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			_view.container.addEventListener(GameMouseEvent.GAME_MOUSE_EVENT, gameMouseEventHandler);
			view.container.buttonMode = true;
			return _view;
		}
		
		private function gameMouseEventHandler(e:GameMouseEvent):void 
		{
			switch(e.mouseEventType)
			{
				case GameMouseEvent.OVER :
					showGlow();
					break;
					
				case GameMouseEvent.OUT :
					hideGlow();
					break;
			}
		}
		
		/**
		 * 设置当前状态
		 * @param	stateType
		 */
		public function setState(stateType:String):void {
			if (state!=stateType) 
			{
				clearIcon();
			}
			state = stateType;
			switch (stateType) 
			{
				case STATE_IDLE:
					asset.bitmap_movie_mc.gotoAndPlayLabels("normal");
					showPao();
				break;
				
				case STATE_WORKING:
					asset.bitmap_movie_mc.gotoAndPlayLabels("working");
				break;
				
				case STATE_COMPLETE:
					asset.bitmap_movie_mc.gotoAndPlayLabels("normal");
					//asset.bitmap_movie_mc.gotoAndPlayLabels("complete");
					showPao();
				break;
				
			}
		}
		
		/**
		 * 设置人物头顶的泡泡
		 * @param	iconClass	泡泡内图标的类名
		 * @param	showPao		是否显示泡泡背景
		 * @param	float		漂浮的距离
		 * @param	duration	漂浮的间隔时间
		 */
		public function showMood(iconClass:String,showPao:Boolean = false, float:int = 0, duration:Number = 2 ):void {
			removeMood();
			bubbleUI = new PersonPaoView(this, iconClass, showPao);
			if (float) bubbleUI.setFloat(float);
		}
		
		public function removeMood():void {
			if (bubbleUI) 
			{
				bubbleUI.remove();
				bubbleUI = null;
				return;
			}
		}
		
		private function clearIcon():void 
		{
			removeMood();
		}
		
		private function showPao():void 
		{
			switch (state) 
			{
				case STATE_IDLE:
					showMood(getQualifiedClassName(IdleIcon), true);
				break;
				
				
				case STATE_COMPLETE:
					showMood(getQualifiedClassName(HarvestingIcon), true, 10);
				break;
			}
		}
		
	}

}