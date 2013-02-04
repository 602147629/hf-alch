package createPlayer
{
	import com.brokenfunction.json.decodeJson;
	import com.greensock.easing.Expo;
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.ColorMatrixFilter;
	import flash.geom.Point;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.utils.display.McShower;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.MagicUrlLoader;

	/**
	 * ...
	 * @author jj
	 */
	public class CreatePlayerMain extends Sprite 
	{
		private var view:createPlayerUi;
		private var currentAvatarId:Number;
		
		private var avatarIds:Array = [1001, 1002, 1003, 1004, 1005, 1006];
		private var _grayFilter:ColorMatrixFilter;
		public function CreatePlayerMain():void 
		{
			var red:Number = 0.3086;
			var green:Number = 0.694;
			var blue:Number = 0.0820;
			_grayFilter = new ColorMatrixFilter([red, green, blue, 0, 0, red, green, blue, 0, 0, red, green, blue, 0, 0, 0, 0, 0, 1, 0]);
			
			if (stage) init();
			else addEventListener(Event.ADDED_TO_STAGE, init);
		}

		private function init(e:Event = null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			// entry point
			view = new createPlayerUi();
			addChild(view);
			view.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			selectAvatar(0);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "yesBtn":
				saveCreate();
				break;
				
				case "avatar_0":
				case "avatar_1":
				case "avatar_2":
				case "avatar_3":
				case "avatar_4":
				case "avatar_5":
					var index:uint = (uint((e.target.name as String).split("_")[1]));
					selectAvatar(index);
				break;
			}
		}
		
		private function selectAvatar(index:uint):void 
		{
			currentAvatarId = avatarIds[index];
			view.faceMc.gotoAndStop(index+1);
			//for (var i:int = 0; i < 8; i++) 
			//{
				//if (index==i) 
				//{
					//view["avatar_" + index.toString()].filters = [];
				//}else {
					//view["avatar_" + index.toString()].filters = [_grayFilter];
				//}
			//}
			
		}
		
		private function saveCreate():void
		{
			var createLoader:MagicUrlLoader = new MagicUrlLoader();
			createLoader.addEventListener(Event.COMPLETE, saveCreate_complete);
			
			var request:URLRequest = new URLRequest(PublicDomain.getInstance().createUrl);
			request.method = "POST";
			
			var vars:URLVariables = new URLVariables();
			vars.avatarId = currentAvatarId;	
			
			request.data = vars;
			
			createLoader.load(request);
			
		}
		
		private function saveCreate_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, saveCreate_complete);
			trace("saveCreate_complete:",e.target.data);
			var obj:Object = decodeJson(e.target.data);
			
			if (obj.result) 
			{
				if (obj.result.status==1) 
				{
					dispatchEvent(new Event(Event.COMPLETE));
					parent.removeChild(this);
				}
			}
			
		}
		
	}

}