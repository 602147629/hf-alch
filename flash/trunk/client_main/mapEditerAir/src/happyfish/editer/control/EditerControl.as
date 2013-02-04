package  happyfish.editer.control
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.Matrix;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.MapClassVo;
	import happyfish.editer.view.Main;
	import happyfish.editer.view.TileView;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	/**
	 * ...
	 * @author jj
	 */
	public class EditerControl
	{
		public static var showPos:Boolean=true;
		protected var _mouseGridIcon:TileView;
		protected var main:Main;
		protected var map:Sprite;
		protected var pos:Point3D;
		protected var oldPos:Point3D;
		
		public function EditerControl(_main:Main,mapview:Sprite) 
		{
			main = _main;
			map = mapview;
			
			//mapview.addEventListener(MouseEvent.CLICK, mapClickFun,true);
			mapview.addEventListener(MouseEvent.MOUSE_DOWN, beginDrag,true);
			mapview.addEventListener(MouseEvent.MOUSE_MOVE, mouseMoveFun,true);
			mapview.stage.addEventListener(MouseEvent.MOUSE_UP, upDrag);
			
			initPos();
		}
		
		private function initPos():void
		{
			oldPos = pos;
			var p:Point = new Point(main.mapSprite.gridView.mouseX-32, main.mapSprite.gridView.mouseY);
			pos = IsoUtil.isoToGrid(IsoUtil.screenToIso(p));
			
			p = IsoUtil.nodeToScreen(new Node(pos.x, pos.z));
			p = main.mapSprite.gridView.localToGlobal(p);
			p = main.mapSprite.globalToLocal(p);
			//trace(p);
			mouseGridIcon.setXY(p.x,p.y);
		}
		
		public function clear():void {
			map.removeEventListener(MouseEvent.MOUSE_DOWN, beginDrag,true);
			map.removeEventListener(MouseEvent.MOUSE_MOVE, mouseMoveFun,true);
			map.stage.removeEventListener(MouseEvent.MOUSE_UP, upDrag);
			
			main = null;
			map = null;
			
			if (_mouseGridIcon) 
			{
				_mouseGridIcon.parent.removeChild(_mouseGridIcon);
				_mouseGridIcon = null;
			}
		}
		
		protected function mouseMoveFun(e:MouseEvent):void 
		{
			
			initPos();
			
			if(showPos) main.editer.setStr(pos.x+","+pos.z);
			
		}
		
		protected function upDrag(e:MouseEvent):void 
		{
			
		}
		
		protected function beginDrag(e:MouseEvent):void 
		{
			initPos();
		}
		
		protected function mapClickFun(e:MouseEvent):void 
		{
			initPos();
		}
		
		public function get mapClass():MapClassVo {
			return EditDataManager.getInstance().curMapClass;
		}
		
		public function get mouseGridIcon():TileView {
			if (!_mouseGridIcon) 
			{
				//创建格子素材
			var tmptilesize:Number = IsoUtil.TILE_SIZE;
			var tileMap:BitmapData = new BitmapData(IsoUtil.TILE_SIZE * 2, IsoUtil.TILE_SIZE, true, 0xffffff);
			var tileMc:Sprite = new Sprite();
			tileMc.graphics.lineStyle(2, 0xffffff,.8);
			tileMc.graphics.beginFill(0x5E66A2,0);
			tileMc.graphics.moveTo( -tmptilesize, 0);
			tileMc.graphics.lineTo(0, -tmptilesize / 2);
			tileMc.graphics.lineTo(tmptilesize, 0);
			tileMc.graphics.lineTo(0, tmptilesize / 2);
			tileMc.graphics.lineTo( -tmptilesize, 0);
			tileMc.graphics.endFill();
					var tmpmatrix:Matrix = new Matrix();
					tmpmatrix.translate(tmptilesize,tmptilesize/2);
					tileMap.draw(tileMc, tmpmatrix);
					
				_mouseGridIcon = new TileView(tileMap);
				_mouseGridIcon.alpha = 1;
			}
			
			main.mapSprite.addChild(_mouseGridIcon);
			return _mouseGridIcon;
		}
	}

}