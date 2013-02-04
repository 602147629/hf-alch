package happymagic.display.view.ui.personMsg 
{
	import com.greensock.TweenLite;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.text.TextFieldAutoSize;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.display.view.IconView;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.utils.display.McShower;
	import happyfish.utils.display.TextFieldTools;
	import happymagic.manager.DataManager;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author jj
	 */
	public class StoryPersonMsgView extends storyPersonMsgUi
	{
		private var target:IsoItem;
		private var closeId:uint;
		private var hasInit:Boolean;
		private var callback:Function;
		private var textFieldTools:TextFieldTools;
		private var theStage:Stage;
		
		public function StoryPersonMsgView(_target:IsoItem, npcHead:String, npcName:String, str:String, __time:uint, _callback:Function = null)
		{
			callback = _callback;
			mouseChildren = false;
			mouseEnabled = false;
			
			target = _target;
			
			textFieldTools = new TextFieldTools;
			
			setData(npcHead, npcName, str, __time, callback);
		}
		
		public function setData(npcHead:String, npcName:String, str:String, time:uint, _callback:Function = null):void
		{
			if (!target.view.container.stage) closeMe();
			
			var rect:Rectangle;
			//调整位置
			if (!hasInit)
			{
				var camera:Sprite = (DataManager.getInstance().worldState.world as MagicWorld).view.isoView.camera;
				rect = target.view.container.getBounds(camera);
				x = (rect.left + rect.right) / 2;
				y = rect.top;
				camera.addChild(this);
				TweenLite.from(this, 0.5, { scaleX:0, scaleY:0 } );
				target.view.container.stage.addEventListener(MouseEvent.CLICK, onClick);
				theStage = target.view.container.stage;
				target.view.container.addEventListener(Event.REMOVED_FROM_STAGE, closeMe);
				
				hasInit = true;
			}
			
			content.x = 0;
			rect = content.getBounds(stage);
			if (rect.left < 0) content.x = -rect.left;
			else if (rect.right > stage.stageWidth) content.x = stage.stageWidth - rect.right;
			
			callback = _callback;
			
			//头像
			var npcHeadContainer:Sprite = content.npcHead;
			while (npcHeadContainer.numChildren > 0) npcHeadContainer.removeChildAt(0);
			var iconView:IconView = new IconView(76, 106);
			iconView.notAlign = true;
			iconView.setData(npcHead);
			npcHeadContainer.addChild(iconView);
			
			content.npcNameTxt.text = npcName; //名字
			
			textFieldTools.typeEffect(content.txt, str, 150); //说话内容
			
			if (closeId) 
			{
				clearTimeout(closeId);
				closeId = 0;
			}
			closeId = setTimeout(closeMe, time);
		}
		
		private function onClick(e:MouseEvent):void
		{
			if (textFieldTools.typeEnd)
			{
				clearTimeout(closeId);
				closeMe();
			}
			else textFieldTools.stopTimer(true);
		}
		
		public function closeMe(event:Event = null):void
		{	
			closeId = 0;
			PersonMsgManager.getInstance().delStoryMsg(target.view.name, true);
			if(theStage) theStage.removeEventListener(MouseEvent.CLICK, onClick);
			target.view.container.removeEventListener(Event.REMOVED_FROM_STAGE, closeMe);
			if (parent) parent.removeChild(this);
			if(textFieldTools) textFieldTools.stopTimer();
			if (callback!=null) callback.apply();
		}
	}
}