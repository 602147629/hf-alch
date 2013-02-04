package happymagic.scene.world.grid.person 
{
	import happyfish.scene.world.grid.Person;
	import happymagic.model.vo.ActItemClassVo;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.WorldState;
	import flash.events.MouseEvent;
	/**
	 * 地下城交互对象
	 * @author XiaJunJie
	 */
	public class ActItem extends Person
	{
		public var vo:ActItemClassVo;
		public var currentHp:int;
		
		public function ActItem(vo:ActItemClassVo, $worldState:WorldState,id:int,_x:uint,_z:uint,currentHp:int,__callBack:Function=null) 
		{
			this.vo = vo;
			this.currentHp = currentHp;
			
			var data:Object = { x:_x, z:_z, className:vo.className, id:id };
			super(data, $worldState, __callBack) ;
			
			this.gridPos.x = _x;
			this.gridPos.z = _z;
			this.grid_size_x = vo.sizeX;
			this.grid_size_z = vo.sizeZ;
			
			makeView();
		}
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			
			_view.container.addEventListener(MouseEvent.ROLL_OVER, this.onMouseOver);
			_view.container.addEventListener(MouseEvent.ROLL_OUT, this.onMouseOut);
			_view.container.addEventListener(MouseEvent.MOUSE_MOVE, this.onMouseOverMove);
			_view.container.addEventListener(MouseEvent.CLICK, onClick);
			return _view;
		}
		
	}

}