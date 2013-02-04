package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author 
	 */
	public class GameSettingVo extends BasicVo
	{
		public var replySpTime:uint; //每多少秒回复一次sp
		public var replySp:uint; //每次回复多少sp
		public var worldMapBg:String; //世界地图背景素材类名
		public var shalouCid:uint; //沙漏cid
		public var shalouTime:int = 3600; //沙漏减少的时间,1小时
		public var wineCid:int; //立即刷新佣兵的道具CID
		public var needWineNum:int; //每次消耗酒的数量
		public var crystalCid:uint; //魔晶cid,提高合成成功率的道具的cid
		public var customerInTime:uint; //多少时间进一个顾客
		public var customerGiveupSp:uint; //放弃顾客花费的SP
		public var tipsRateBySatisfaction:uint; //每10级满意度增加的小费比例
		public var skillUnlock:Array; //佣兵技能解锁需求 [[level,type,price],[5,1,100],[15,2,200]]
		public var homeSceneId:uint;	//家的sceneId
		public var viliageSceneId:uint;	//村庄的sceneId
		public var homeBuildId:uint;	//村里家的建筑ID
		public var barBuildIds:Array;	//村里酒吧建筑的ID列表
		public var maxBattleRoles:int;	//参战的最大人数,不包含临时角色
		public var useItemArray:Array = [515, 615, 715, 815,3115]; // 给自身使用的物品ID数组
		public var hpPrice:Number = 0;	// 每一点hp花费的金币
		public var mpPrice:Number = 0; // 每一点mp花费的金币
		public var orderRefreshGem:int; // 订单刷新的钻石数
		public var maxHireHelp:int; //酒馆最大帮助数，帮助到这个数时会有一次免费内刷新机会
		public var battleHelpItemCid:int; //战斗援助增加次数道具cid
		public var occPrice:Array = [100, 2]; //入侵时消耗,[coin,sp]
		public var featCid:uint = 3315;
		

        public var spaddItemIdArray:Array = [515, 615, 715, 815];// 4个行动力药水的id
		public var smithyBuildId:int=1408;	//铁匠铺ID
		
		public function GameSettingVo() 
		{
			
		}
		
	}

}