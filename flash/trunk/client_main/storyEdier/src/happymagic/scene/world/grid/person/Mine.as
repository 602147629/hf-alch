package happymagic.scene.world.grid.person 
{
	import flash.geom.Point;
	import happyfish.scene.world.WorldState;
	import happymagic.model.vo.MineVo;
	
	/**
	 * 矿 2011.11.14
	 * @author XiaJunJie
	 */
	public class Mine extends ActItem
	{
		private var __callBack:Function;
		public var clickTimes:int; //连续点击计数
		
		public function Mine(vo:MineVo, $worldState:WorldState,__callBack:Function=null)  
		{
			this.__callBack = __callBack;
			super(vo, $worldState, vo.id, vo.x, vo.z, vo.currentHp, callback);
			block = true;
			typeName = "Mine";
		}
		
		private function callback():void
		{
			if(__callBack!=null) __callBack();
		}
		
		override public function remove():void 
		{
			super.remove();
			this._worldState.world.removeToGrid(this, false);
		}
		
	}

}