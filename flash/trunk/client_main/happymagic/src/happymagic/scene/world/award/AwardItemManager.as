package happymagic.scene.world.award 
{
	import com.friendsofed.isometric.Point3D;
	import flash.geom.Point;
	import flash.utils.setTimeout;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ResultVo;
	import happymagic.scene.world.MagicWorld;
	import happyfish.manager.EventManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class AwardItemManager 
	{
	
		public function AwardItemManager(access:Private) 
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
				throw new Error( "AwardItemManager"+"单例" );
			}
		}
		
		public function init(_worldState:WorldState):void {
			worldState = _worldState;
		}
		
		/**
		 * 创建一批奖励对象到场景中
		 * @param	value	type:奖励种类	num:数量	point:出现位置[Point3D]
		 */
		public function addAwards(value:Array):void {
			for (var i:int = 0; i < value.length; i++) 
			{
				setTimeout(addOneAwards, 30 * i, value[i]);
			}
		}
		
		/**
		 * 表现result里获得的东西
		 * @param	value	resultVo
		 * @param	items	道具
		 * @param	point	表现的ISO坐标
		 */
		public function addAwardsByResultVo(value:ResultVo, items:Array, point:Point3D):void {
			var i:int;
			
			var tmparr:Array = new Array();
			if (value.coin > 0) tmparr.push( { type:AwardType.COIN, num:value.coin, point:point } );
			if (value.gem > 0) tmparr.push( { type:AwardType.GEM, num:value.gem, point:point } );
			if (value.exp > 0) tmparr.push( { type:AwardType.EXP, num:value.exp, point:point } );
			if (value.sp > 0) tmparr.push( { type:AwardType.SP, num:value.sp, point:point } );
			
			if (items) 
			{
				for (i = 0; i < items.length; i++) 
				{
					tmparr.push( { type:AwardType.ITEM, num:1, point:point, id:items[i].id } );
				}
			}
			
			
			for (i = 0; i < tmparr.length; i++) 
			{
				setTimeout(addOneAwards, 30 * i, tmparr[i]);
			}
		}
		
		private function addOneAwards(value:Object):void {
			var i:int;
			if (value.type==AwardType.ITEM) 
			{
				//创建道具
				(worldState.world as MagicWorld).createAwardItem(value.type, value.num, value.point,value.id);
			}else {
				if (value.type == AwardType.COIN) 
				{
					//按1/10/100分割
					var tmpnum:int;
					var _num:int = value.num;
					var arr:Array = new Array();
					tmpnum = Math.floor(_num/100);
					for (i = 0; i < tmpnum; i++) 
					{
						arr.push(100);
					}
					_num = _num % 100;
					tmpnum = Math.floor(_num/10);
					for (i = 0; i < tmpnum; i++) 
					{
						arr.push(10);
					}
					_num = _num % 10;
					for (i = 0; i < _num; i++) 
					{
						arr.push(1);
					}
					
					
					for (i = 0; i < arr.length; i++) 
					{
						if (arr[i]>0) 
						{
							setTimeout((worldState.world as MagicWorld).createAwardItem,30*i,value.type,arr[i],value.point);
						}
					}
				}else {
					(worldState.world as MagicWorld).createAwardItem(value.type, value.num, value.point);
				}
				
			}
			
		}
		
		//按照resultVo中的内容飘屏
		public function piaoStrByResultVo(result:ResultVo,point:Point):void
		{
			var arr:Array = new Array;
			if(result.coin!=0) arr.push( { type:PiaoMsgType.TYPE_COIN, content:result.coin.toString() } );
			if(result.gem!=0) arr.push( { type:PiaoMsgType.TYPE_GEM, content:result.gem.toString() } );
			if(result.sp!=0) arr.push( { type:PiaoMsgType.TYPE_SP, content:result.sp.toString() } );
			
			for (var i:int = 0; i < arr.length; i++)
			{
				setTimeout(DisplayManager.showPiaoStr, 500, arr[i]["type"], arr[i]["content"], point);
			}
		}
		
		public static function getInstance():AwardItemManager
		{
			if (instance == null)
			{
				instance = new AwardItemManager( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:AwardItemManager;
		private var worldState:WorldState;
		
	}
	
}
class Private {}