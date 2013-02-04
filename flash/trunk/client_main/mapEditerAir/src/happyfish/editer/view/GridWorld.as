package  happyfish.editer.view
{
	import com.adobe.serialization.json.JSON;
	import com.friendsofed.isometric.DrawnIsoTile;
	import com.friendsofed.isometric.Point3D;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.geom.Matrix;
	import flash.utils.ByteArray;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.utils.Base64;
	
	/**
	 * ...
	 * @author jj
	 */
	public class GridWorld extends Sprite
	{
		private var data:Object;
		private var nodes:Object;
		private var tileViews:Object;
		private var nodeStr:String;
		
		public function GridWorld() 
		{
			tileViews = new Object();
		}
		
		public function setData(_nodeStr:String,gridSize:uint,mapSize:uint):void {
			this.nodeStr = _nodeStr;
			
			IsoUtil.TILE_SIZE = gridSize;
			
			y = -(gridSize / 2) * mapSize;
			
			var i:int;
			var m:int;
			var tmp:Node;
			if (nodeStr.length<=0) 
			{
				for (i = 0; i < mapSize; i++) 
				{
					for (m = 0; m < mapSize; m++) 
					{
						tmp = new Node(i, m);
						tmp.walkable = true;
						setNode(tmp);
					}
				}
			}else {
				var by:ByteArray = Base64.decodeToByteArray(nodeStr);
				by.uncompress();
				nodeStr = by.readUTF();
				
				var tmparr:Array = new Array();
				var tmparr2:Array=nodeStr.split(";");
				for (i = 0; i < tmparr2.length; i++) 
				{
					tmparr.push(new Array());
					tmparr[i]=(tmparr2[i].split(","));
				}
				for (i = 0; i < tmparr.length; i++) 
				{
					for (m = 0; m < tmparr.length; m++) 
					{
						tmp = new Node(i, m);
						tmp.walkable = (tmparr[i][m]==1) ? true : false;
						setNode(tmp);
					}
				}
			}
			
			//创建格子素材
			var tmptilesize:Number = IsoUtil.TILE_SIZE;
			var tileMap:BitmapData = new BitmapData(IsoUtil.TILE_SIZE * 2, IsoUtil.TILE_SIZE, true, 0xffffff);
			var tileMc:Sprite = new Sprite();
			tileMc.graphics.lineStyle(1, 0xffffff,1);
			tileMc.graphics.beginFill(0xFF3300,.8);
			tileMc.graphics.moveTo( -tmptilesize, 0);
			tileMc.graphics.lineTo(0, -tmptilesize / 2);
			tileMc.graphics.lineTo(tmptilesize, 0);
			tileMc.graphics.lineTo(0, tmptilesize / 2);
			tileMc.graphics.lineTo( -tmptilesize, 0);
			tileMc.graphics.endFill();
					var tmpmatrix:Matrix = new Matrix();
					tmpmatrix.translate(tmptilesize,tmptilesize/2);
					tileMap.draw(tileMc,tmpmatrix);
			
			
			var tmpGrid:TileView;
			var tmpnode2:Node;
			for (var name:String in nodes) 
			{
				tileViews[name] = new Object();
				for (var name2:String in nodes[name]) 
				{
					tmpnode2 = nodes[name][name2];
					tmpGrid = new TileView(tileMap);
					tmpGrid.setNode(tmpnode2);
					tmpGrid.position = new Point3D(tmpnode2.x*IsoUtil.TILE_SIZE, 0, tmpnode2.y*IsoUtil.TILE_SIZE);
					//tmpGrid.position = IsoUtil.gridToIso(new Point3D(tmpnode2.x,tmpnode2.y));
					tileViews[name][name2] = tmpGrid;
					addChild(tmpGrid);
				}
			}
		}
		
		public function setWalkAble(_x:uint,_y:uint,value:Boolean):void {
			if (nodes[_x]) 
			{
				if (nodes[_x][_y]) 
				{
					(nodes[_x][_y] as Node).walkable = value;
					
					(tileViews[_x][_y] as TileView).setWalkAble(value);
				}
			}
		}
		
		public function getNode(_x:uint,_y:uint):Node {
			if (nodes[_x]) 
			{
				if (nodes[_x][_y]) 
				{
					return nodes[_x][_y];
				}
			}
			
			return null;
		}
		
		public function setNode(value:Node):void {
			if (!nodes) 
			{
				nodes = new Object();
			}
			if (!nodes[value.x]) 
			{
				nodes[value.x] = new Object();
			}
			nodes[value.x][value.y] = value;
		}
		
		
		public function outData():String {
			
			var nodesarr:Array = new Array();
			var tmparr:Array;
			for (var name:String in nodes) 
			{
				tmparr = new Array();
				
				for (var name2:String in nodes[name]) 
				{
					tmparr[name2]=nodes[name][name2].walkable ? 1 :0;
				}
				
				nodesarr.push(tmparr.join(","));
			}
			var str:String;
			str = nodesarr.join(";");
			
			var by:ByteArray = new ByteArray();
			by.writeUTF(str);
			by.compress();
			str = Base64.encodeByteArray(by);
			
			nodeStr = str;
			
			//***********************************
			
			//var by:ByteArray = new ByteArray();
			//by.writeObject(nodes);
			//by.compress();
			//str = Base64.encodeByteArray(by);
			//
			//data.nodeStr = str;
			
			
			return nodeStr;
		}
		
		public function clear():void 
		{
			tileViews = new Object();
			nodes = new Object();
			
			while (numChildren>0) 
			{
				removeChildAt(0);
			}
		}
		
	}

}