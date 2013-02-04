package happymagic.model.vo 
{
	/**
	 * 叠加动画VO
	 * @author XiaJunJie
	 */
	public class AnimationVo
	{
		public var condition:String; //执行条件
		public var type:int; //类型 ROLE/COVER/BOTTOM/THROWING/REMOVE/PIAO
		public var pos:int; //位置 INITIATOR/TARGET/KEY_GRID
		
		public var movTarget:int; //要移动到的位置 0表示默认 1表示战场中心
		public var jump:Boolean; //是否要抛物线移动
		public var duration:Number = -1; //历时 -1表示使用默认
		
		public var label:String; //角色动画标签
		public var stopAtLabelEnd:Boolean; //标签播放完后是否停在标签处
		
		public var className:String; //叠加动画或底部动画或投掷物素材名
		public var filterType:int; //滤镜类型
		
		public var font:String; //文字类型
		public var percent:Number = 1; //飘的百分比
		public var piaoMp:Boolean; //飘的是否是MP 如果为否则飘HP
		
		public var shockTime:int; //震动时间
		
		public var times:int = 1; //播放次数 0表示循环
		public var delay:int; //播放延迟 单位:毫秒
		
		public var completeImmediately:Boolean; //是否立即跳过
		
		/**
		 * 设置数据
		 * @param	arr 一个数组 有如下样式
		 * 角色动画: [1,pos,label,delay,times,stopAtLabelEnd] 后三位可省
		 * 移动到目标: [2,movTarget,jump,duration,delay] 后四位可省
		 * 跳回: [3,duration,delay] 后两位可省
		 * 叠加动画: [4,pos,className,delay,times] 后两位可省
		 * 底部动画: [5,pos,className,delay,times] 后两位可省
		 * 飞行动画: [6,className,jump,delay] 后两位可省
		 * 飘字动画: [8,pos,font,delay,percent,piaoMp] 后两位可省
		 * 添加滤镜: [9,pos,filterType,delay] 后一位可省
		 * 震动动画: [10,pos,shockTime,delay] 后一位可省
		 * 播放音效: [11,className,delay] 后一位可省
		 * 移除动画: [21,pos,className,delay] 后一位可省
		 * 移除滤镜: [22,pos,filterType,delay] 后一位可省
		 */
		public function setData(arr:Array):void 
		{
			if (arr[0] is String) condition = arr.shift();
			
			type = arr[0];
			switch(type)
			{
				case ROLE:
					pos = arr[1];
					label = arr[2];
					if (arr.length > 3) delay = arr[3];
					if (arr.length > 4) times = arr[4];
					if (arr.length > 5) stopAtLabelEnd = arr[5] != 0;
				break;
				case MOVE_TO_TARGET:
					if (arr.length > 1) movTarget = arr[1];
					if (arr.length > 2) jump = arr[2] != 0;
					if (arr.length > 3) duration = Number(arr[3]);
					if (arr.length > 4) delay = arr[4];
				break;
				case JUMP_BACK:
					if (arr.length > 1) duration = Number(arr[1]);
					if (arr.length > 2) delay = arr[2];
				break;
				case COVER:
				case BOTTOM:
					pos = arr[1];
					className = arr[2];
					if (arr.length > 3) delay = arr[3];
					if (arr.length > 4) times = arr[4];
				break;
				case FLYING:
					className = arr[1];
					if (arr.length > 2) jump = arr[2] != 0;
					if (arr.length > 3) delay = arr[3];
				break;
				case PIAO:
					pos = arr[1];
					font = arr[2];
					if (arr.length > 3) delay = arr[3];
					if (arr.length > 4) percent = Number(arr[4]) / 100;
					if (arr.length > 5) piaoMp = arr[5] != 0;
				break;
				case FILTER:
					pos = arr[1];
					filterType = arr[2];
					if (arr.length > 3) delay = arr[3];
				break
				case REMOVE_ANIM:
					pos = arr[1];
					className = arr[2];
					if (arr.length > 3) delay = arr[3];
				break;
				case REMOVE_FILTER:
					pos = arr[1];
					filterType = arr[2];
					if (arr.length > 3) delay = arr[3];
				break;
				case SHOCK:
					pos = arr[1];
					shockTime = arr[2];
					if (arr.length > 3) delay = arr[3];
				break;
				case SOUND:
					className = arr[1];
					if (arr.length > 2) delay = arr[2];
				break;
			}
		}
		
		//常量-------------------------
		public static const MISS:String = "m"; //未命中时执行此脚本
		public static const IGNORE_WHEN_MISS:String = "iwm"; //未命中时忽略此脚本
		public static const MALE:String = "male"; //当施法者是男性时执行此脚本
		public static const FEMALE:String = "female"; //当施法者是女性时执行此脚本
		
		public static const ROLE:int = 1; //角色动画
		public static const MOVE_TO_TARGET:int = 2; //发起者移动到目标
		public static const JUMP_BACK:int = 3; //跳回
		public static const COVER:int = 4; //叠加动画
		public static const BOTTOM:int = 5; //底部动画 叠在角色下面的叠加动画
		public static const FLYING:int = 6; //投掷动画
		public static const PIAO:int = 8; //飘字
		public static const FILTER:int = 9; //滤镜
		public static const SHOCK:int = 10; //震动
		public static const SOUND:int = 11; //音效
		public static const REMOVE_ANIM:int = 21; //移除一个叠加动画或底部动画 如果有的话
		public static const REMOVE_FILTER:int = 22; //移除一个滤镜 如果有的话
		
		public static const INITIATOR:int = 1; //发起者
		public static const TARGET:int = 2; //目标
		public static const KEYGRID:int = 3; //场景内的关键位置
		
		public static const NONE:int = 0; //无
		public static const BATTLEFIELD_CENTER:int = 1; //战场中心
		public static const FRIEND_SKILL_POS:int = 2; //玩家方面后方
		
		public static const GRAY_FILTER:int = 201; //灰色滤镜
		public static const RED_FILTER:int = 202; //红色滤镜
		public static const GREEN_FILTER:int = 203; //绿色滤镜
	}

}