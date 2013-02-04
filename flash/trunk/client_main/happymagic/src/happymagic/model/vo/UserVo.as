package happymagic.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happyfish.model.vo.BasicVo;
	import happyfish.modules.gift.interfaces.IGiftUserVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author slam
	 */
	public class UserVo extends BasicVo implements IGiftUserVo
	{
		private var _uid:String;
		private var _name:String;
		private var _face:String;
		public var avatar:uint;
		private var _className:String;
		private var _level:uint;
		public var exp:int;
		public var maxExp:int;
		public var maxRoleNum:int;//最大佣兵数
		public var roleLevel:int;//战斗角色等级
		public var satisfaction:int;//满意度
		public var gem:int;
		public var sp:int;
		public var maxSp:int;
		public var tileX:int;
		public var tileZ:int;
		
		// {id:引导id, idx:引导id}
		public var guideInfo:Object;
		
		//还有多少秒回复体力
		public var replySpTime:int;
		public var currentSceneId:uint;
		
		public var coin:int;
		
		//好友的排序index
		public var index:uint;
		
		//[int] -1 表示领完了 0 是无奖励  1是连续一天登陆
	    public var signDay:int;
		
		//判断是否为粉丝
	    public var isfans:Boolean;
		//还剩分享可获奖励的次数		
		private var _feedNum:int;
		//领取礼包的数量
		public var signAwardNumber:String;
		//这个人还能不能送他礼物 
		private var _giftAble:Boolean;
		//你能不能对他发请求
		private var _giftRequestAble:Boolean;
		//未收过的礼物数量
		private var _giftNum:uint;
		//你收到的请求数量
		private var _giftRequestNum:uint;
		
		public var maxOrder:uint; //最大订单数
		
		public var viliageInfo:Object; //村内建筑等级 { "id":level,"121":2 }
		
		public var x:uint;
		public var y:uint;
		
		//侵占相关------------------------
		public var safeTime:Number; //剩余保护时间 时间戳
		public var atkSafeTime:Number; //玩家攻击此用户的保护时间的时间戳
		public var ownerUid:Number; //占领者的UID
		public var ownerFace:String; //占领者的照片
		public var ownerName:String; //占领者的名字
		public var ownerAwardTime:Number; //占领者下一次收税的时间戳
		public var ownerEndTime:Number=0; //占领者自动撤离的时间戳
		public var ownerBuildId:int=0; //占领者占据的建筑
		//--------------------------------
		
		public var hireHelp:uint;		//酒馆当前已被帮助次数
		public var hireHelpUsed:int; //	1是用过   0为没用过
		
		public function UserVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			
			if (avatar) 
			{
				className = DataManager.getInstance().getAvatarVo(avatar).className;
			}
			return this;
		}
		
		public function clone():UserVo {
			var tmp:UserVo = new UserVo();
			tmp.setData(decodeJson(JSON.encode(this)));
			return tmp;
		}
		
		public function toString():String {
			var str:String;
			str = "uid:" + uid+" ";
			str += "name:" + name+" ";
			str += "level:" + level+" ";
			str += "face:" + face+" ";
			
			return str;
		}
		
		public function get uid():String 
		{
			return _uid;
		}
		
		public function set uid(value:String):void 
		{
			_uid = value;
		}
		
		public function get name():String 
		{
			return _name;
		}
		
		public function set name(value:String):void 
		{
			_name = value;
		}
		
		public function get face():String 
		{
			return _face;
		}
		
		public function set face(value:String):void 
		{
			_face = value;
		}
		
		public function get giftAble():Boolean 
		{
			return _giftAble;
		}
		
		public function set giftAble(value:Boolean):void 
		{
			_giftAble = value;
		}
		
		public function get giftRequestAble():Boolean 
		{
			return _giftRequestAble;
		}
		
		public function set giftRequestAble(value:Boolean):void 
		{
			_giftRequestAble = value;
		}
		
		public function get giftNum():uint 
		{
			return _giftNum;
		}
		
		public function set giftNum(value:uint):void 
		{
			_giftNum = value;
		}
		
		public function get giftRequestNum():uint 
		{
			return _giftRequestNum;
		}
		
		public function set giftRequestNum(value:uint):void 
		{
			_giftRequestNum = value;
		}
		
		public function get level():uint 
		{
			return _level;
		}
		
		public function set level(value:uint):void 
		{
			_level = value;
		}
		
		public function get className():String 
		{
			return _className;
		}
		
		public function set className(value:String):void 
		{
			_className = value;
		}
		
		public function get feedNum():int 
		{
			return _feedNum;
		}
		
		public function set feedNum(value:int):void 
		{
			_feedNum = value;
		}
	}

}