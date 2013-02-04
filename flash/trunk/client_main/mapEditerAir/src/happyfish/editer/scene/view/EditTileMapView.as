package happyfish.editer.scene.view 
{
	import com.friendsofed.isometric.IsoUtils;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.geom.Matrix;
	import flash.geom.Rectangle;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.editer.model.vo.EditFloorVo;
	import happyfish.scene.iso.IsoUtil;
	/**
	 * 地板总显示类
	 * @author ...
	 */
	public class EditTileMapView extends Bitmap
	{
		public var tileSprite:EditIsoLayer;
		private var tileBitmapData:BitmapData;
		
		private var tileList:Object;
		private var tileList2:Object;
		private var initTileMapId:uint;
		private var floorListLength:int;
		public var floor1:Array;
		public var floor2:Array;
		
		public function EditTileMapView() 
		{
			tileSprite = new EditIsoLayer(true);
			tileList = new Object();
			tileList2 = new Object();
		}
		
		public function setData(_floor1:Array,_floor2:Array):void {
			
			floor1 = _floor1;
			floor2 = _floor2;
			
			clear();
			
			y = -(IsoUtil.TILE_SIZE / 2) * floor1.length;
			
			initTile();
			
		}
		
		private function initTile():void 
		{
			floorListLength = floor1.length * floor1[0].length*2;
			
			tileList = new Object();
			tileList2 = new Object();
			
			var i:int;
			var j:int;
			var tmptile:EditTileItemView;
			for (i = 0; i < floor1.length; i++) 
			{
				tileList[i] = new Object();
				for (j = 0; j < floor1[i].length; j++) 
				{
					tmptile = new EditTileItemView(floor1[i][j],tileSprite, tile_complete);
					tileList[i][j] = tmptile;
				}
			}
			for ( i = 0; i < floor2.length; i++) 
			{
				tileList2[i] = new Object();
				for (j = 0; j < floor2[i].length; j++) 
				{
					tmptile = new EditTileItemView(floor2[i][j],tileSprite, tile_complete);
					tileList2[i][j] = tmptile;
				}
			}
			
			initTileMap();
		}
		
		private function tile_complete():void 
		{
			floorListLength--;
			
			if (floorListLength<=0) 
			{
				floorListLength = 0;
				tileSprite.sortFun();
				initTileMapId = setTimeout(initTileMap,100);
			}
		}
		
		public function setTile(x:uint, y:uint, floor:EditFloorVo):void {
			if (x<0 || x>floor1.length-1 || y<0 || y>floor1.length-1) 
			{
				return;
			}
			
			var data:Array;
			var layer:int;
			if (floor.sortPriority==0) 
			{
				data = floor1;
				layer = 1;
			}else {
				data = floor2;
				layer = 2;
			}
			
			data[x][y] = floor;
			floor.x = x;
			floor.z = y;
			
			var tile:EditTileItemView = getTileView(x, y,layer); 
			tile.data = floor;
			floorListLength++;
			if (floor.className) 
			{
				tile.resetView(floor.className, tile_complete);
				initTileMap();
			}else {
				tile.resetView(floor.className, tile_complete);
				initTileMap();
			}
			
			
		}
		
		public function getTileView(x:uint, y:uint,layer:int):EditTileItemView 
		{
			var tmpobj:Object;
			if (layer==1) 
			{
				tmpobj = tileList;
			}else {
				tmpobj = tileList2;
			}
			
			if (tmpobj[x]) 
			{
				if (tmpobj[x][y]) 
				{
					return tmpobj[x][y] as EditTileItemView;
				}
			}
			return null;
		}
		
		public function clear():void 
		{
			tileSprite.clear();
			tileList = new Object();
			tileList2 = new Object();
		}
		
		
		
		private function initTileMap():void 
		{
			trace("initTileMap");
			initTileMapId = 0;
			
			if (tileBitmapData) tileBitmapData.dispose();
			//var groundRect:Rectangle = tileSprite.getRect(tileSprite);
			var groundRect:Rectangle = 
				new Rectangle( -floor1.length*IsoUtil.TILE_SIZE , -IsoUtil.TILE_SIZE/2,
								floor1.length * IsoUtil.TILE_SIZE*2, floor1.length * IsoUtil.TILE_SIZE+40
								);
			tileBitmapData = new BitmapData(groundRect.width, groundRect.height, true,0x000000);
				tileBitmapData.draw(tileSprite, new Matrix(1, 0, 0, 1, -groundRect.x, -groundRect.y));
				
				bitmapData = tileBitmapData;
			x = -(groundRect.width)/2+IsoUtil.TILE_SIZE;
			
		}
		
	}

}