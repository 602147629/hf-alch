package happymagic.model.vo 
{
	import com.brokenfunction.json.decodeJson;
	import flash.geom.Point;
	import happyfish.model.vo.BasicVo;
	import happyfish.scene.astar.Node;
	import happyfish.scene.personAction.PersonActionVo;
	import happyfish.scene.utils.NodesByteTools;
	/**
	 * ...
	 * @author jj
	 */
	public class SceneClassVo extends BasicVo
	{
		public var sceneId:uint;
		public var name:String;
		public var content:String;
		//类型 3:家 1:野外 2:地下城 2011.11.11
		public var type:int;
		//场景尺寸 2011.11.10
		public var numCols:int;
		public var numRows:int;
		//装饰物在此场景中的位置偏移 2011.11.10
		public var isoStartX:int;
		public var isoStartZ:int;
		//场景背景图
		public var bg:String;
		public var bgSound:String;
		public var nodeStr:String;
		//入口列表
		public var entrances:Array;
		public var actions:Array;//npc行为脚本列表
		//父场景ID 2011.11.22
		public var parentSceneId:uint;
		
		public var nodes:Array;//grid的nodes列表
		
		public var floorList:Array; //地板层1
		public var floorList2:Array; //地板层2
		public var wallList:Array; //墙
		public var decorList:Array; //装饰物列表
		public var portalList:Array; //传送门
		public var monsterList:Array; //怪
		public var mineList:Array; //矿
		public var npcList:Array; //npc
		
		public var withFog:int = 0;
		
		//战斗背景
		public var fightBg:String;
		
		public function SceneClassVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			var i:int;
			for (var name:String in obj) 
			{
				if (name == "entrances") entrances = decodeJson(obj[name]);
				else if ( this.hasOwnProperty(name)) 
				{
					this[name] = obj[name];
				}
			}
			//临时 增加NPC行为数据
			//addActions();
			return this;
		}
		
		private function addActions():void {
			//var str:String = "[[{\"type\":\"requestMeet\",\"num\":2},{\"type\":\"showMood\",\"iconClass\":\"pao_chat\",\"showTime\":10000},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":1000}]]";
			var str:String = "[[{\"type\":\"roundRoom\"},{\"type\":\"outScene\"},{\"type\":\"hide\",\"showTime\":10000}],[{\"type\":\"toNode\"},{\"type\":\"showMood\",\"iconClass\":\"pao_flash\",\"showTime\":2000}],[{\"type\":\"toNode\"},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":1000}],[{\"type\":\"toNode\",\"targetNodeArr\":[33,19],\"towardsNodeArr\":[33,18]},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":1000}],[{\"type\":\"toNode\",\"targetNodeArr\":[38,21],\"towardsNodeArr\":[38,20]},{\"type\":\"showMood\",\"iconClass\":\"pao_chat\",\"showTime\":3000}],[{\"type\":\"toNode\",\"targetNodeArr\":[19,32]},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":3000}],[{\"type\":\"toNode\",\"targetNodeArr\":[41,19]},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":3000}],[{\"type\":\"toNode\",\"targetNodeArr\":[21,30]},{\"type\":\"showMood\",\"iconClass\":\"pao_chat\",\"showTime\":3000}]]";
			var obj:Array = decodeJson(str);
			actions = new Array();
			for (var i:int = 0; i < obj.length; i++) 
			{
				actions.push(new Array());
				for (var m:int = 0; m < obj[i].length; m++) 
				{
					actions[i].push(new PersonActionVo().setData(obj[i][m]));
				}
				
			}
		}
		
		public function getEntrancesNode():Array {
			var _entrances:Array = new Array();
			for (var i:int = 0; i < entrances.length; i++) 
			{
				_entrances.push(new Node(entrances[i][0], entrances[i][1]));
			}
			return _entrances;
		}
		
		/////////////////////////////////////////////
		//				常量
		/////////////////////////////////////////////
		public static const HOME:int = 3; //家 2011.11.11
		public static const FIELD:int = 1; //野外 2011.11.11
		public static const DUNGEON:int = 2; //地下城 2011.11.11
	}

}