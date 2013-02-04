package happymagic.model.vo.order 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	import happyfish.time.Time;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderVo extends BasicVo 
	{
		
		public var id:String;
		
		
		// Array<ConditionVo>
		public var needs:Array;
		// 订单将要离开的时间戳, 后端传入的是剩余时间,经过转换了
		public var outTime:int;
		
		// 订单开始和总时间
		private var _beginTime:int;
		// 订单是可工作时间,-1:无限
		public var totalTime:int;
		private var _state:int;
		public var avatarName:String;
		public var avatarFaceClass:String;
		public var avatarClassName:String;
		public var demandDialog:String;
		public var successDialog:String;
		public var failedDialog:String;
		public var awards:Array;
		
		// 1:普通奖励  2:更多奖励
		public var awardType:int;
		
		override public function setData(obj:Object):BasicVo 
		{
			if ("needs" in obj)
			{
				needs = decodeJson(obj.needs);
				delete obj.needs;
				for (var i:int = needs.length - 1; i >= 0; i--)
				{
					var arr:Array = needs[i] as Array;
					needs[i] = new ConditionVo().setData( { type:ConditionType.ITEM, id:arr[0], num:arr[1] } );
				}
			}
			if ("dialog" in obj)
			{
				arr = obj.dialog.split("&&");
				delete obj.dialog;
				demandDialog = arr[0];
				successDialog = arr[1];
				failedDialog = arr[2];
			}
			
			awards = [];
			if ("awards" in obj)
			{
				var tmp:Array = decodeJson(obj.awards);
				for (i = tmp.length - 1; i >= 0; i--)
				{
					arr = tmp[i] as Array;
					awards[i] = new ConditionVo().setData( { type:ConditionType.ITEM, id:arr[0], num:arr[1] } );
				}
			}
			
			if (obj.exp > 0)
			{
				awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_EXP, num:obj.exp } ));
			}
			
			if (obj.coin > 0)
			{
				awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:obj.coin } ));
			}
			delete obj.awards;
			delete obj.exp;
			delete obj.coin;
			
			outTime = Time.getCurTime() + int(obj.outTime);
			delete obj.outTime;
			totalTime = obj.totalTime;
			return super.setData(obj);
		}
		
		public function get beginTime():int { return _beginTime; }
		
		public function get remainingTime():int
		{
			if (-1 == totalTime) return -1;
			return _beginTime != 0 ? Time.getRemainingTime(_beginTime, totalTime) : totalTime;
		}
		public function set remainingTime(value:int):void 
		{
			_beginTime = Time.getCurTime() - totalTime + value;
		}
		
		public function get state():int { return _state; }
		public function set state(value:int):void 
		{
			if (OrderType.WORKING == value && OrderType.REQUEST == _state)
			{
				_beginTime = Time.getCurTime();
			}
			_state = value;
		}
	}
}