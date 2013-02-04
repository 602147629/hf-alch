package happymagic.display.view 
{
	import com.brokenfunction.json.decodeJson;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.KeyboardEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.text.TextField;
	import flash.text.TextFieldType;
	import flash.ui.Keyboard;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.EventManager;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.ShowStoryCommand;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.ResultVo;
	/**
	 * 控制台 按"F1"弹出一个输入框 测试各种命令
	 * @author XiaJunJie 2012.4.20
	 */
	public class Console extends Sprite
	{
		private var input:TextField;
		private var output:TextField;
		private var resultVo:ResultVo;
		private var uid:int;
		
		public function Console(container:Sprite) 
		{
			output = new TextField;
			output.type = TextFieldType.DYNAMIC;
			output.width = 300;
			output.height = 80;
			output.background = true;
			output.backgroundColor = 0xFFFFFF;
			addChild(output);
			
			input = new TextField;
			input.type = TextFieldType.INPUT;
			input.width = 300;
			input.height = 20;
			input.y = output.y + output.height;
			input.border = true;
			input.borderColor = 0;
			input.background = true;
			input.backgroundColor = 0xFFFFFF;
			addChild(input);
			
			this.visible = false;
			this.x = (container.stage.stageWidth - this.width) / 2;
			this.y = (container.stage.stageHeight - this.height) / 2;
			container.addChild(this);
			container.stage.addEventListener(KeyboardEvent.KEY_UP, onKeyUp);
			
			showAllCommands();
		}
		
		private function onKeyUp(event:KeyboardEvent):void
		{
			if (event.keyCode == Keyboard.F1)
			{
				visible = !visible;
				if (visible)
				{
					input.text = "";
					stage.focus = input;
				}
			}
			else if (event.keyCode == Keyboard.ENTER && stage.focus == input)
			{
				parseCommand(input.text);
				input.text = "";
			}
		}
		
		private function parseCommand(cmd:String):void
		{
			var num:int;
			var curUserId:int = int(DataManager.getInstance().currentUser.uid);
			var arr:Array = cmd.split(" ");
			if (arr.length == 0) return;
			switch(arr[0])
			{
				case "/showAllCommands":
					showAllCommands();
				break;
				case "/showStory":
					if (arr.length <= 1) output.text = "请输入storyId";
					else
					{
						new ShowStoryCommand(int(arr[1]));
						visible = false;
					}
				break;
				case "/addSp":
					if (arr.length <= 1) output.text = "请输入数值";
					else
					{
						num = int(arr[1]);
						resultVo = new ResultVo;
						resultVo.sp = num;
						post("http://devalchemyrenren.happyfish001.com/nicktest/addsp",{uid:curUserId,count:num});
					}
				break;
				case "/addCoin":
					if (arr.length <= 1) output.text = "请输入数量";
					else
					{
						num = int(arr[1]);
						resultVo = new ResultVo;
						resultVo.coin = num;
						post("http://devalchemyrenren.happyfish001.com/nicktest/addcoin",{uid:curUserId,count:num});
					}
				break;
				case "/resetCD":
					post("http://devalchemyrenren.happyfish001.com/zxtest/resetoccupy",{uids:curUserId});
				break;
				case "/addItem":
					if (arr.length < 3) output.text = "请输入CID和数量\n格式:\n/addItem CID 数量";
					else
					{
						post("http://devalchemyrenren.happyfish001.com/tools/adduseritem", { uid:curUserId, cid:int(arr[1]), count:int(arr[2]) }, arr[0]);
					}
				break;
				case "/addItemByName":
					if (arr.length < 3) output.text = "请输入CID和数量\n格式:\n/addItem CID 数量";
					else
					{
						var vo:BaseItemClassVo = DataManager.getInstance().itemData.getItemClassByName(arr[1]);
						if (!vo) break;
						post("http://devalchemyrenren.happyfish001.com/tools/adduseritem", { uid:curUserId, cid:vo.cid, count:int(arr[2]) }, arr[0]);
					}
				break;
				case "/addSkill":
					var params:Object = new Object;
					params["uid"] = curUserId;
					params["skill"] = int(arr[1]);
					if (arr.length > 2) params["id"] = int(arr[2]);
					post("http://devalchemyrenren.happyfish001.com/zxtest/addskill", params );
				break;
				case "/gotoScene104":
					post("http://devalchemyrenren.happyfish001.com/api/entermap", { sceneId:104 } );
				break;
				case "/gotoScene105":
					post("http://devalchemyrenren.happyfish001.com/api/entermap", { sceneId:105 } );
				break;
				case "/gotoScene106":
					post("http://devalchemyrenren.happyfish001.com/api/entermap", { sceneId:106, portalId:37 } );
				break;
				case "/gotoScene107":
					post("http://devalchemyrenren.happyfish001.com/api/entermap", { sceneId:107, portalId:53 } );
				break;
				case "/changeFriends":
					arr.pop();
					var fids:String = arr.join(",");
					post("http://devalchemyrenren.happyfish001.com/nicktest/updatefriend", {uid:curUserId,fids:fids});
				break;
				case "/startBattle":
					uid = int(arr[1]);
					post("http://devalchemyrenren.happyfish001.com/zxtest/testinitfight", { uid:int(arr[1]), fid:int(arr[2]) } );
				break;
				default:
					output.text = "未知指令";
				break;
			}
		}
		
		private function showAllCommands():void
		{
			output.text = "所有指令";
			output.appendText("\n显示所有命令: /showAllCommands");
			output.appendText("\n触发剧情: /showStory storyId");
			output.appendText("\n加SP: /addSp 数量");
			output.appendText("\n加金币: /addCoin 数量");
			output.appendText("\n重置佣兵CD: /resetCD");
			output.appendText("\n加物品: /addItem CID 数量");
			output.appendText("\n加物品: /addItemByName CID 数量");
			output.appendText("\n加技能: /addSkill 技能CID 佣兵ID\n省略佣兵ID即为主角加技能");
			output.appendText("\n去矿洞: /gotoScene104");
			output.appendText("\n去矿洞南区1层: /gotoScene105");
			output.appendText("\n去矿洞南区2层: /gotoScene106\n需先去矿洞南区1层");
			output.appendText("\n去矿洞南区3层: /gotoScene107\n需先去矿洞南区2层");
			output.appendText("\n更改好友列表: /changeFriends\n注:会删掉原来的好友");
			output.appendText("\n开启指定战斗: /startBattle 用户ID 战斗ID");
		}
		
		private function post(url:String, params:Object, cmd:String = null):void
		{
			var urlLoader:URLLoader = new URLLoader;
			var request:URLRequest = new URLRequest(url);
			var vars:URLVariables = new URLVariables();
			if (params) 
			{
				for (var name:String in params) vars[name] = params[name];
			}
			request.data = vars;
			urlLoader.load(request);
			urlLoader.addEventListener(Event.COMPLETE, function(e:Event):void
			{
				urlLoader.removeEventListener(Event.COMPLETE, arguments.callee);
				onLoadComplete(e, cmd);
			});
		}
		
		private function onLoadComplete(event:Event, cmd:String):void
		{
			event.target.removeEventListener(Event.COMPLETE, onLoadComplete);
			
			if (resultVo) TakeResultVoControl.getInstance().take(resultVo, true);
			resultVo = null;
			
			output.text = "成功";
			
			switch(cmd)
			{
				case "/startBattle" :
					var initData:Object = decodeJson(event.target.data);
					initData.isTest = true;
					initData.testUid = uid;
					DataManager.getInstance().setVar("battleInitData", initData);
					DisplayManager.sceneSprite.visible = false;
					ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("battle"));
					EventManager.getInstance().addEventListener("battleFinish", onBattleFinish);
					break;
					
				case "/addItem" :
				case "/addItemByName" :
					initData = decodeJson(event.target.data);
					if (!initData.items) break;
					DataManager.getInstance().itemData.setItemList(initData.items);
					EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.ITEMS_CHANGE));
					break;
			}
		}
		
		private function onBattleFinish(event:Event):void
		{
			DisplayManager.sceneSprite.visible = true;
			EventManager.getInstance().removeEventListener("battleFinish", onBattleFinish);
		}
		
	}

}