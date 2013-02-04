package happyfish.guide.view 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.text.TextFormat;
	import flash.utils.getDefinitionByName;
	import happyfish.guide.interfaces.IGuideDialog;
	import happyfish.guide.utils.AfterCall;
	import happyfish.guide.vo.DialogVo;
	import happyfish.utils.display.MoviePlayer;
	import happyfish.utils.display.TextFieldTools;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideDialog extends GuideDialogUI implements IGuideDialog
	{
		public static var loopPlaySoundFun:Function;
		public static var stopSoundFun:Function;
		
		private var stageW:int = 0;
		private var stageH:int = 0;
		
		private var player:MoviePlayer;
		private var avatar:MovieClip;
		private var bg:Sprite;
		
		private var currShowComplete:Boolean;
		private var showIdx:int = -1;
		private var callback:Function;
		private var list:Vector.<DialogVo>;
		
		private var effect:TextFieldTools;
		
		public function GuideDialog() 
		{
			var format:TextFormat = txt.defaultTextFormat;
			format.bold = true;
			format.letterSpacing = 1;
			txt.defaultTextFormat = format;
			txt.mouseEnabled = false;
			bg = createBg();
			addChildAt(bg, 0);
			mouseChildren = false;
			effect = new TextFieldTools();
			player = new MoviePlayer();
			//addEventListener(MouseEvent.CLICK, clickHandler, true, int.MAX_VALUE);
		}
		
		/**
		 * 当对话结束后的回调函数
		 * @param	callback function (vo:DialogVo):void;
		 */
		public function setEndCallback(callback:Function):void
		{
			this.callback = callback;
		}
		/**
		 * 显示对话框
		 * @param	list
		 * @param	container
		 */
		public function showDialog(list:Vector.<DialogVo>, container:DisplayObjectContainer):void
		{
			this.list = list;
			showIdx = -1;
			addEventListener(Event.ADDED_TO_STAGE, addToStage);
			addEventListener(Event.REMOVED_FROM_STAGE, removedFromStage);
			container.addChild(this);
			showNextDialog();
		}
		
		private function showNextDialog():void 
		{
			if (avatar)
			{
				if (avatar.parent) avatar.parent.removeChild(avatar);
				player.stop();
				player.setMovie(null);
				avatar = null;
			}
			
			showIdx++;
			if (showIdx >= list.length)
			{
				clear();
				return;
			}
			
			var Def:Class = getDefinitionByName(list[showIdx].avatarRef) as Class;
			avatar = new Def() as MovieClip;
			player.setMovie(avatar);
			player.playLoop(list[showIdx].label, list[showIdx].label + "_end");
			addChildAt(avatar, 1);
			avatar.x = 1 == list[showIdx].pos ? 200 : -200;
			avatar.y = -120;
			currShowComplete = false;
			playSound();
			if (list[showIdx].promptlyHandler != null)
			{
				try {
					list[showIdx].promptlyHandler();
				}catch (err:Error) { };
			}
			
			txt.text = list[showIdx].chat + "";
			effect.typeEffect(txt, list[showIdx].chat, 50);
			effect.completeHandler = showComplete;
		}
		
		private function showComplete():void 
		{
			player.stop();
			stopSound();
		}
		
		private function playSound():void 
		{
			if (loopPlaySoundFun != null)
			{
				loopPlaySoundFun();
			}
		}
		
		private function stopSound():void
		{
			currShowComplete = true;
			if (stopSoundFun != null)
			{
				stopSoundFun();
			}
		}
		
		public function clear():void
		{
			removeEventListener(Event.ADDED_TO_STAGE, addToStage);
			
			if (parent) parent.removeChild(this);
			
			if (!currShowComplete) stopSound();
			var _list:Vector.<DialogVo> = list;
			var _callback:Function = callback;
			list = null;
			callback = null;
			
			if (_callback != null) _callback();
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			if (!parent) return;
			
			e.stopImmediatePropagation();
			
			if (!effect.typeEnd)
			{
				effect.stopTimer(true);
				return;
			}
			
			if (list[showIdx].clickHandler != null)
			{
				AfterCall.call(list[showIdx].clickHandler);
			}
			showNextDialog();
		}
		
		private function addToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addToStage);
			stage.addEventListener(Event.RESIZE, resizeHandler);
			stage.addEventListener(MouseEvent.CLICK, clickHandler, true, int.MAX_VALUE);
			resizeHandler(null);
		}
		
		private function removedFromStage(e:Event):void
		{
			stage.removeEventListener(Event.RESIZE, resizeHandler);
			stage.removeEventListener(MouseEvent.CLICK, clickHandler, true);
		}
		
		private function resizeHandler(e:Event):void 
		{
			x = stage.stageWidth / 2;
			y = stage.stageHeight - 10;
			bg.width = stage.stageWidth;
			bg.height = stage.stageHeight;
			bg.x = -x;
			bg.y = -y;
		}
		
		private function createBg():Sprite 
		{
			var sp:Sprite = new Sprite();
			sp.graphics.beginFill(0x0, 0);
			sp.graphics.drawRect(0, 0, 100, 100);
			sp.graphics.endFill();
			return sp;
		}
		
	}

}