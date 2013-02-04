package happyfish.guide 
{
	import flash.display.Stage;
	import flash.events.EventDispatcher;
	import happyfish.guide.interfaces.IGuideDialog;
	import happyfish.guide.view.GuideDialog;
	import happyfish.guide.vo.DialogVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideDialogManager extends EventDispatcher 
	{
		
		private var queue:Vector.<DialogArguments>;
		
		
		private var dialog:IGuideDialog;
		
		
		public function setDialog(dialog:IGuideDialog):void
		{
			this.dialog = dialog;
		}
		
		public function showDialogs(list:Vector.<DialogVo>, stage:Stage, endCallback:Function):void
		{
			if (1 == queue.push(new DialogArguments(list, stage, endCallback)))
			{
				showFirstDialog();
			}
		}
		
		private function showFirstDialog():void 
		{
			if (0 == queue.length) return;
			
			dialog.setEndCallback(endCallback);
			dialog.showDialog(queue[0].list, queue[0].stage);
		}
		
		private function endCallback():void
		{
			var args:DialogArguments = queue.shift();
			args.endCallback(args.list);
			showFirstDialog();
		}
		
		public function GuideDialogManager(p:Class) 
		{
			if (p != Singleton) throw new Error("this is singleton!");
			
			queue = new Vector.<DialogArguments>();
			setDialog(new GuideDialog());
		}
		
		private static var instance:GuideDialogManager
		public static function getInstance():GuideDialogManager
		{
			if (!instance) instance = new GuideDialogManager(Singleton);
			return instance;
		}
	}
}
import flash.display.Stage;
import happyfish.guide.vo.DialogVo;

class Singleton { }

class DialogArguments
{
	public var list:Vector.<DialogVo>;
	public var stage:Stage;
	public var endCallback:Function;
	
	public function DialogArguments(list:Vector.<DialogVo>, stage:Stage, endCallback:Function)
	{
		this.list = list;
		this.stage = stage;
		this.endCallback = endCallback;
	}
}