package happymagic.manager 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import flash.events.Event;
	import flash.net.SharedObject;
	import flash.utils.ByteArray;
	import happyfish.events.ActModuleEvent;
	import happyfish.feed.vo.FeedVo;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.model.vo.GuidesClassVo;
	import happyfish.model.vo.GuidesState;
	import happyfish.model.vo.GuidesVo;
	import happyfish.scene.astar.Node;
	import happyfish.scene.fog.FogManager;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.IsoPhysicsControl;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happyfish.time.Time;
	import happyfish.utils.CustomTools;
	import happymagic.events.DataManagerEvent;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.data.FunctionFilterData;
	import happymagic.model.data.IllustratedData;
	import happymagic.model.data.ItemData;
	import happymagic.model.data.MixData;
	import happymagic.model.data.OrderData;
	import happymagic.model.data.RoleData;
	import happymagic.model.data.TaskData;
	import happymagic.model.data.WorldData;
	import happymagic.model.vo.ai.AIScriptVo;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.classVo.MixClassVo;
	import happymagic.model.vo.classVo.WorldMapClassVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.EnemyClassVo;
	import happymagic.model.vo.GameSettingVo;
	import happymagic.model.vo.IllustrationsVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.MineVo;
	import happymagic.model.vo.MiningScriptVo;
	import happymagic.model.vo.MoneyType;
	import happymagic.model.vo.MonsterVo;
	import happymagic.model.vo.NpcClassVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.PortalVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.RoomLevelVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneVo;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.model.vo.UserVo;
	import happymagic.model.vo.WorldMapVo;
	import happymagic.scene.world.SceneType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DataManager 
	{
		private static var instance:DataManager;
		
		public const mixData:MixData = new MixData();
		
		public const itemData:ItemData = new ItemData();
		
		public const orderData:OrderData = new OrderData();
		
		public const roleData:RoleData = new RoleData();
		
		public const taskData:TaskData = new TaskData();

		public const illustratedData:IllustratedData = new IllustratedData();
		
		public const worldData:WorldData = new WorldData();
		
		public const functionFilterData:FunctionFilterData = new FunctionFilterData();
		
		public var gameSetting:GameSettingVo;
		
		/** initStatic 的原始数据 */
		public var initStaticData:Object;
		
		//等级信息
		private var _levelInfos:Array;
		
		//avatar类
		private var _avatars:Array = new Array();
		
		
		//基本设置文件,如多少时间回一次魔法
		public var basicClass:Object;
		
		/**
		 * 场景内装饰信息
		 */
		//包含场景内所有装饰道具信息,除墙,地板外,并以按类分毫
		public var decorList:Object = new Object();
		public var floorList:Array = new Array();
		public var floorList2:Array = new Array();//下面的一层地板
		public var wallList:Array = new Array();
		public var npcList:Vector.<NpcVo> = new Vector.<NpcVo>();
		public var roleList:Vector.<RoleVo> = new Vector.<RoleVo>();//场景中的佣兵列表
		
		//新增三项地下城物件列表 2011.11.11
		public var portalList:Vector.<PortalVo>;
		public var monsterList:Vector.<MonsterVo>;
		public var mineList:Vector.<MineVo>;
		
		//玩家信息
		public var userInfo:UserVo;
		
		//当前岛的vo
		public var curSceneUser:UserVo;
		
		//用户自己的信息
		private var _currentUser:UserVo;
		
		//好友列表
		public var friends:Array;
		
		/**
		 * DIY时保存时需要提交的改变数据
		 */
		public var decorChangeList:Object = new Object();
		public var decorChangeBagList:Object = new Object();
		public var floorChangeList:Object = new Object();
		public var floorChangeBagList:Object = new Object();
		public var wallChangeList:Object = new Object();
		public var wallChangeBagList:Object = new Object();
		
		/**
		 * DIY时装饰列表
		 */
		private var _decorBagList:Array;
		private var customObj:Object = new Object();
		
		//拣取水晶返回结果,以decorId为keyname的object,值为ResultVo
		public var pickUpResults:Object = new Object;
		//拣取水晶时掉落的物品列表
		public var pickUpItems:Object = new Object;
		
		//音效开关
		public var soundEffect:Boolean = true;
		
		public var worldState:WorldState;
		public var isDraging:Boolean;
		//场景类列表
		public var sceneClass:Array;
		//场景数据
		public var scenes:Array;
		//npc类
		public var npcClass:Array;
		//npc对话列表
		public var npcChats:Array;
		public var enemyClass:Array;
		private var _enemys:Array;
		public var physicsControl:IsoPhysicsControl;
		public var guidesClass:Array;
		public var guides:Array;
		public var diarys:Array;
		//是否在DIY中
		public var isDiying:Boolean;
		//feed类数据
		public var feedClass:Array;
		//活动数据
		public var acts:Array;
		//连续登陆的静态数据
		public var signAwardClass:Array;
		
		
		//换装静态数据
		public var rehandling:Array;
		
		//怪物类数据
		public var monsterClass:Array;
		//矿类数据
		public var mineClass:Array;
		
		//上一个场景的ID 2011.11.18
		public var lastSceneId:int = 0;
		
		//分解的静态数据
		public var resolve:Array;
		
		//雾缓存
		public var sceneFogPic:Object = new Object;
		public var sceneFogPicRect:Object = new Object;
		
		//模块数据列表,都是otherModules
		public var modules:Array;
		//用户等级信息
		public var levelInfos:Array;
		public var roomLevels:Vector.<RoomLevelVo>;
		public var skillAndItems:Vector.<SkillAndItemVo>;
		public var aIScripts:Vector.<AIScriptVo>;
		
		//地图静态地址 2012-3-21
		public var mapCopyClass:Object;
		
		//职业相克数据 2012-4-10
		public var jobAdj:Object;
		
		//属性相克数据 2012-4-19
		public var propAdj:Object;
		
		//商店物品列表
		public var shopItemList:Array;
		//商店材料列表
		public var shopMatList:Array;
		
		//是否有新场景的数据
		private var _isNewWorldMap:int;
		//是否有新的图鉴
		private var _isNewIllus:int;
		//是否有新的日志
		private var _isNewDiary:int;
		
		public function DataManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "DataManager"+"单例" );
			}
		}
		
		public function setVar(name:String,val:*):void {
			customObj[name] = val;
		}
		
		public function getVar(name:String):* {
			return customObj[name];
		}
		
		public static function getInstance():DataManager
		{
			if (instance == null)
			{
				instance = new DataManager( new Private() );
			}
			return instance;
		}
		
		public function getModuleVo(name:String):ModuleVo {
			var tmp:ModuleVo;
			for (var i:int = 0; i < modules.length; i++) 
			{
				tmp = modules[i];
				if (tmp.name==name) 
				{
					return tmp;
				}
			}
			return null;
		}
		
		public function getSkillAndItemVo(cid:int):SkillAndItemVo {
			for (var i:int = 0; i < skillAndItems.length; i++) 
			{
				if (skillAndItems[i].cid==cid) 
				{
					return skillAndItems[i];
				}
			}
			return null;
		}
		
		public function getAiScript(cid:uint):AIScriptVo {
			for (var i:int = 0; i < aIScripts.length; i++) 
			{
				if (aIScripts[i].cid==cid) 
				{
					return aIScripts[i];
				}
			}
			return null;
		}
		
		/**
		 * 随机一个场景入口
		 * @param	except
		 * @return
		 */
		public function getCustomSceneDoor(except:Node = null):Node {
			var tmp:Node=CustomTools.customFromArray(getSceneVoByClass(curSceneUser.currentSceneId, SceneState.OPEN).getEntrancesNode(),except);
			return tmp;
		}
		
		public function getActByName(name:String):ActVo {
			for (var i:int = 0; i < acts.length; i++) 
			{
				if (acts[i].actName==name) 
				{
					return acts[i] as ActVo;
				}
			}
			return null;
		}
		
		public function setActByName(actVo:ActVo):void {
			for (var i:int = 0; i < acts.length; i++) 
			{
				if (acts[i].actName==actVo.actName) 
				{
					acts[i] = actVo;
					//广播活动数据改变
					EventManager.getInstance().dispatchEvent(new ActModuleEvent(ActModuleEvent.ACT_DATA_CHANGE, actVo));
					return;
				}
			}
		}
		
		public function getEnoughSp(value:int):Boolean {
			return _currentUser.sp >= value;
		}
		
		//根据CID获取怪物配置 2011.11.11
		public function getMonsterClassByCid(cid:int):Object
		{
			for (var i:int = 0; i < monsterClass.length; i++)
			{
				if (parseInt(monsterClass[i].cid) == cid) return monsterClass[i];
			}
			return null;
		}
		
		

		//根据CID获取这个礼物是不是能够索要		
		public function isAskForGift(_cid:int):int
		{
			var giftsVoArray:Array = DataManager.getInstance().getVar("gifts");
					 
			for (var i:int = 0; i < giftsVoArray.length; i++ )
			{
				if (giftsVoArray[i].id == _cid)
				{
					return giftsVoArray[i].lockLevel;							 
				}
			}
			return 0;
		}
		

		
		//根据CID获取矿配置 2011.11.11
		public function getMineClassByCid(cid:int):Object
		{
			for (var i:int = 0; i < mineClass.length; i++)
			{
				if (parseInt(mineClass[i].cid) == cid) return mineClass[i];
			}
			return null;
		}
		
		public function getMonsterClassVoByCid(cid:int):MonsterVo
		{
			var data:Object = getMonsterClassByCid(cid);
			if (data)
			{
				return (new MonsterVo().setData(data)) as MonsterVo;
			}
			return null;
		}
		
		public function getNpcClassByNpcId(id:uint):Object {
			
			for (var i:int = 0; i < npcClass.length; i++) 
			{
				if (npcClass[i].cid==id) 
				{
					return npcClass[i];
				}
			}
			return null;
		}
		
		/**
		 * 
		 * @param	... args	[cid,num],[11,2]
		 * @return
		 */
		public function getEnoughDecors(decorArr:Array):Boolean {
			var tmp:ItemVo;
			for (var i:int = 0; i < decorArr.length; i++) 
			{
				tmp = itemData.getItem(decorArr[i][0]);
				if (tmp) 
				{
					if (tmp.num<decorArr[i][1]) 
					{
						return false;
					}
				}else {
					return false;
				}
			}
			return true;
		}
		
		/**
		 * 
		 * @param	... args	[cid,num],[11,2]
		 * @return
		 */
		public function getEnoughItems(itemArr:Array):Boolean {
			var tmp:ItemVo;
			for (var i:int = 0; i < itemArr.length; i++) 
			{
				tmp = itemData.getItem(itemArr[i][0]);
				if (tmp) 
				{
					if (tmp.num<itemArr[i][1]) 
					{
						return false;
					}
				}else {
					return false;
				}
			}
			return true;
		}
		
		public function getEnouthCrystalType(type:uint,num:uint):Boolean {
			if (type==MoneyType.COIN) 
			{
				if (num <= _currentUser.coin)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			
			if (type==MoneyType.GEM) 
			{
				return num <= _currentUser.gem;
			}
			
			return true;
		}
		
		/**
		 * 按type返回自己的相应水晶数量
		 * @param	type
		 * @return
		 */
		public function getCrystalType(type:uint):uint {
			if (type==MoneyType.COIN) 
			{
				return _currentUser.coin;
			}
			
			if (type==MoneyType.GEM) 
			{
				return _currentUser.gem;
			}
			
			return null;
		}
		
		public function getUserData(type:String):uint {
			var id:uint = ConditionType.StringToInt(type);
			if (id<2) 
			{
				return getCrystalType(id);
			}
			switch (type) 
			{
				case ConditionType.USER_EXP:
				return _currentUser.exp;
				break;
			}
			
			return null;
		}
		
		public function getEnoughUserData(type:String, num:uint):Boolean {
			var id:uint = ConditionType.StringToInt(type);
			if (id<2) 
			{
				return getEnouthCrystalType(id, num);
			}
			switch (type) 
			{
				case ConditionType.USER_EXP:
				return _currentUser.exp >= num;
				break;
				
				case ConditionType.USER_SP:
				return _currentUser.sp >= num;
				break;
			}
			
			return false;
		}
		
		public function getEnoughCrystal(coin:uint=0,gem:uint=0):Boolean {
			if (coin>_currentUser.coin) 
			{
				return false;
			}
			
			if (gem>_currentUser.gem) 
			{
				return false;
			}
			
			return true;
		}
		
		
		/**
		 * 是否所有新手引导都完成(除最后一步领取最终奖励)
		 */
		public function get isGuidesAllComplete():Boolean {
			//guides.sortOn("index",Array.NUMERIC);
			for (var i:int = 0; i < guides.length-1; i++) 
			{
				if (guides[i].state==GuidesState.UNFINISH) 
				{
					return false;
				}
			}
			
			return true;
		}
		
		/**
		 * 当前场景是否家场景
		 */
		public function get isHome():Boolean {
			return curSceneUser.currentSceneId == gameSetting.homeSceneId;
		}
		
		//判断是不是自己家
		public function get isSelfScene():Boolean {
			if (_currentUser.uid==curSceneUser.uid) 
			{
				return true;
			}else {
				return false;
			}
		}
		
		/**
		 * 当前场景的类型
		 */
		public function get curSceneType():uint {
			if (curSceneUser.currentSceneId == gameSetting.homeSceneId) 
			{
				if (_currentUser.uid==curSceneUser.uid) 
				{
					return SceneType.TYPE_HOME;
				}else {
					return SceneType.TYPE_FRIEND_HOME;
				}
			}else if (curSceneUser.currentSceneId == gameSetting.viliageSceneId) 
			{
				if (_currentUser.uid==curSceneUser.uid) 
				{
					return SceneType.TYPE_SELF_VILIAGE;
				}else {
					return SceneType.TYPE_FRIEND_VILIAGE;
				}
			}else  
			{
				return SceneType.TYPE_EXPLORE;
			}
		}
		
		public function get currentUser():UserVo { return _currentUser; }
		
		public function set currentUser(value:UserVo):void 
		{
			_currentUser = value;
			
			EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE));
		}
		
		public function get avatars():Array { return _avatars; }
		
		public function set avatars(value:Array):void 
		{
			_avatars = value;
		}
		
		public function get decorBagList():Array { return _decorBagList; }
		
		public function set decorBagList(value:Array):void 
		{
			_decorBagList = value;
		}
		
		public function get enemys():Array 
		{
			return _enemys;
		}
		
		public function set enemys(value:Array):void 
		{
			_enemys = value;
		}
		
		
		//获取人物当前等级
		public function getLevelInfo(level:uint):LevelInfoVo {
			for (var i:int = 0; i < levelInfos.length; i++) 
			{
				if (levelInfos[i].level==level) 
				{
					return levelInfos[i];
				}
			}
			
			return null;
		}
		
		public function getAvatarVo(avatarId:uint):AvatarVo
		{
			for (var i:int = 0; i < _avatars.length; i++) 
			{
				if (avatarId==_avatars[i].avatarId) 
				{
					return _avatars[i];
				}
			}
			return null;
		}
		
		public function getSceneClassById(id:uint):SceneClassVo {
			for (var i:int = 0; i < sceneClass.length; i++) 
			{
				if (id == sceneClass[i].sceneId) {
					return sceneClass[i];
				}
			}
			return null;
		}
		
		public function getSceneVoByClass(id:uint,state:uint):SceneVo {
			var tmp:SceneVo;
			var tmpClass:Object;
			for (var i:int = 0; i < sceneClass.length; i++) 
			{
				if (id==sceneClass[i].sceneId) 
				{
					tmpClass = decodeJson(JSON.encode(sceneClass[i]));
					tmp = new SceneVo();
					for (var name:String in tmpClass) 
					{
						if ( tmp.hasOwnProperty(name)) 
						{
							tmp[name] = tmpClass[name];
						}
					}
					tmp.state = state;
					return tmp;
				}
			}
			
			return null;
		}
		
		public function getGuidesClass(gid:uint):GuidesClassVo
		{
			for (var i:int = 0; i < guidesClass.length; i++) 
			{
				if (gid==guidesClass[i].gid) 
				{
					return guidesClass[i];
				}
			}
			return null;
		}
		
		public function getCurGuides():GuidesVo {
			//guides.sortOn("index",Array.NUMERIC | Array.DESCENDING);
			for (var i:int = 0; i < guides.length; i++) 
			{
				if (guides[i].state==1) 
				{
					return guides[i];
				}
			}
			return null;
		}
		
		//根据UID获取好友属性
		public function getFriendUserVo(_uid:String ):UserVo
		{
			for (var i :int = 0; i < friends.length; i++ )
			{
				if (friends[i].uid == _uid)
				{
					return friends[i] as UserVo;
				}
			}
			return null;
		}
		
		//根据ID获取feed类
		public function getFeedClass(_feedid:uint):FeedVo
		{
			if (!feedClass) return null;
			for (var i :int = 0; i < feedClass.length; i++ )
			{
				if (feedClass[i].id == _feedid)
				{
					return feedClass[i] as FeedVo;
				}
			}
			return null;			
		}
		
		/**
		 * 改变当前用户的用户信息
		 * @param	coin
		 * @param	gem
		 * @param	exp
		 * @param	sp
		 */
		public function changeCurUserInfo(coin:int=0, gem:int=0,exp:int=0,sp:int=0):void 
		{
			_currentUser.coin += coin;
			_currentUser.gem += gem;
			_currentUser.exp += exp;
			if (_currentUser.exp>=_currentUser.maxExp && !DataManager.getInstance().getVar("levelUpDoing")) 
			{
				//标记已在升级中
				DataManager.getInstance().setVar("levelUpDoing", true);
				
				var levelUpResult:ResultVo = new ResultVo();
				levelUpResult.status = ResultVo.SUCCESS;
				levelUpResult.levelUP = true;
				TakeResultVoControl.getInstance().take(levelUpResult);	
			}
			
			_currentUser.sp += sp;
			_currentUser.sp = Math.min(_currentUser.sp,_currentUser.maxSp);
			
			//EventManager.getInstance().dispatchEvent(new TaskStateEvent(TaskStateEvent.NEED_CHECK_STATE));
		}
		
		/**
		 * 改变当前场景用户的用户信息
		 * @param	coin
		 * @param	gem
		 * @param	exp
		 * @param	mp
		 */
		public function changeCurSceneUserInfo(coin:int=0, gem:int=0,exp:int=0,sp:int=0):void 
		{
			curSceneUser.coin += coin;
			curSceneUser.gem += gem;
			curSceneUser.exp += exp;
			curSceneUser.sp += sp;
		}
		
		//根据_mixmagicvo.mixType
		//返回相对应的OBJECTVO
		public function getMiningItemVo(_mixmagicvo:MixClassVo):Object
		{
			return itemData.getItemClass(_mixmagicvo.itemCid);
		}
		
		//根据Type里的数据来删选
		public function findArrayType(arr:Array,type:int):Array
		{
			var newarr:Array = new Array();
			for (var i:int = 0; i < arr.length; i++ )
			{
				if (arr[i].type == type)
				{
					newarr.push(arr[i]);
				}
			}
			
			return newarr;
		}
		
		/**
		 * 
		 * @return
		 */
		public function getCurrentScene():SceneClassVo
		{
			var sceneId:int = _currentUser.currentSceneId;
			return getSceneClassById(sceneId);
		}
		
		/**
		 * 获得本地数据
		 * @return
		 */
		private var _localData:SharedObject;
		public function get localData():SharedObject
		{
			if (!_localData)
			{
				_localData = SharedObject.getLocal("HappyMagic_" + _currentUser.uid);
				
				//忽略本地缓存 测试用
				//for (var key:String in _localData.data) delete _localData.data[key];
				//_localData.flush();
			}
			return _localData;
		}
		
		public function get isNewWorldMap():int 
		{
			return _isNewWorldMap;
		}
		
		public function set isNewWorldMap(value:int):void 
		{
			_isNewWorldMap = value;
			EventManager.getInstance().dispatchEvent(new Event("isNewWorldMap"));
		}
		
		public function get isNewIllus():int 
		{
			return _isNewIllus;
		}
		
		public function set isNewIllus(value:int):void 
		{
			_isNewIllus = value;
			EventManager.getInstance().dispatchEvent(new Event("isNewIllus"));
		}
		
		public function get isNewDiary():int 
		{
			return _isNewDiary;
		}
		
		public function set isNewDiary(value:int):void 
		{
			_isNewDiary = value;
			EventManager.getInstance().dispatchEvent(new Event("isNewDiary"));
		}
		
		public function getLocalFogData(sceneId:int = -1):Object
		{
			if (localData.data["fogData"] == null)
			{
				localData.data["fogData"] = new Object;
				localData.flush();
			}
			var fogData:Object = localData.data["fogData"];
			
			if (sceneId==-1) sceneId = _currentUser.currentSceneId;
			var sid:String = String(sceneId);
			if (fogData[sid] == null || fogData[sid] is ByteArray || fogData[sid]["time"] < Time.getCurTime())
			{
				var sceneClassVo:SceneClassVo = getCurrentScene();
				fogData[sid] = {"data":FogManager.getEmptySceneData(sceneClassVo.numCols, sceneClassVo.numRows),"time":getVar("sceneRefreshTime")};
				localData.flush();
			}
			return  fogData[sid];
		}
	}
	
}
class Private {}