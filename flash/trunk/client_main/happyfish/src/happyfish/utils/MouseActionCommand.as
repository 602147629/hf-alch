package happyfish.utils 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.Stage;
	import flash.events.MouseEvent;
	import happyfish.manager.EventManager;
	/**
	 * 鼠标点击等一次操作command
	 * @author 
	 */
	public class MouseActionCommand
	{
		private var _except:DisplayObject;
		private var _callBack:Function;
		private var _callParams:Array;
		private var stageDisplay:DisplayObjectContainer;
		
		public function MouseActionCommand() 
		{
			
		}
		
		/**
		 * 点击目标外物体会触发回调,同时任意点击都会使command结束
		 * @param	except			不会触发回调的物件
		 * @param	stageDisplay	要侦听点击事件的物件,一般会用stage
		 * @param	callBack		回调方法
		 * @param	callParams		回调参数
		 */
		public function outSideClickCommand(_except:DisplayObject, stageDisplay:DisplayObjectContainer, _callBack:Function, _callParams:Array = null):void {
			this.stageDisplay = stageDisplay;
			this._callParams = _callParams;
			this._callBack = _callBack;
			this._except = _except;
			
			stageDisplay.addEventListener(MouseEvent.CLICK, clickFun, true);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			
			
			if (e.target == _except) 
			{
				return;
			}else {
				var tmp:DisplayObject;
				tmp = e.target as DisplayObject;
				while (tmp) 
				{
					if (tmp == _except) 
					{
						return;
					}
					
					if (tmp.hasOwnProperty("parent")) 
					{
						tmp = tmp.parent;
					}else {
						tmp = null;
					}
				}
				
				
				stageDisplay.removeEventListener(MouseEvent.CLICK, clickFun, true);
				callBack();
			}
			
			
		}
		
		private function callBack():void 
		{
			if (_callParams) 
			{
				_callBack.apply(null,_callParams);
			}else {
				_callBack.apply();
			}
		}
		
		
		
	}

}