package happymagic.display.control 
{
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.events.DEvent;
	import happyfish.manager.EventManager;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.world.grid.Person;
	import happymagic.display.view.ui.personMsg.PersonMsgManager;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.CompleteChatCommand;
	import happymagic.scene.world.bigScene.NpcView;
	import happymagic.scene.world.grid.person.Player;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class NpcChatCommand 
	{
		private var player:Player;
		private var npc:Person;
		private var words:Array;
		private var chated:Boolean;
		
		private var curWord:int;
		
		public function NpcChatCommand(player:Player, npc:Person, words:Array, chated:Boolean)
		{
			DataManager.getInstance().setVar("oldCameraPosition", new Point(DisplayManager.camera.x, DisplayManager.camera.y));
			CameraControl.getInstance().centerTweenTo(npc.asset, DisplayManager.camera);
			
			DisplayManager.sceneSprite.mouseChildren = DisplayManager.uiSprite.mouseChildren = false;
			
			this.player = player;
			this.npc = npc;
			
			this.words = words;
			this.chated = chated;
			
			curWord = -1;
			
			nextWord();
		}
		
		private function nextWord():void
		{
			curWord ++;
			if (curWord >= words.length) complete();
			else
			{
				var word:Array = words[curWord];
				var person:Person = word[0] == 1 ? player : npc;
				var content:String = word[1];
				var chatTime:int = content.length * CHATTIME_PERWORD;
				PersonMsgManager.getInstance().addStoryMsg(person, person.data["faceClass"], person.data["name"], content, chatTime, nextWord);
			}
		}
		
		private function complete():void
		{
			DisplayManager.sceneSprite.mouseChildren = DisplayManager.uiSprite.mouseChildren = true;
			
			var p:Point = DataManager.getInstance().getVar("oldCameraPosition") as Point;
			
			DisplayManager.camera.x = p.x;
			DisplayManager.camera.y = p.y;
			
			if (!chated) {
				new CompleteChatCommand(npc.data["chatId"]);
				(npc as NpcView).npcvo.chatState = 2;
				(npc as NpcView).initPaoIcon();
				
				EventManager.getInstance().dispatchEvent(new DEvent("npcChatComplete", { npcId:(npc as NpcView).npcvo.id } ));
			}
		}
		
		private const CHATTIME_PERWORD:int = 1000;
	}

}