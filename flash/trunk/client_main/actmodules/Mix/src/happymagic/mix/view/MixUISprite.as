package happymagic.mix.view 
{
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.utils.getDefinitionByName;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.defaultList.DefaultVLineListView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Pagination;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.manager.DataManager;
	import happymagic.mix.events.MixListEvent;
	import happymagic.mix.view.MixView;
	import happymagic.mix.view.ui.ListRender;
	import happymagic.mix.view.ui.MixListTab;
	import happymagic.mix.view.ui.MixListUI;
	import happymagic.mix.view.ui.render.MixListItem;
	import happymagic.model.data.MixData;
	import happymagic.model.vo.classVo.FurnaceClassVo;
	import happymagic.model.vo.classVo.MixClassVo;
	import happymagic.model.vo.ItemType;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * 合成界面
	 * @author lite3
	 */
	public class MixUISprite extends UISprite 
	{
		private var iview:MixListUI;
		private var table:TabelView;
		private var listView:DefaultListView;
		
		public var mixView:MixView;
		private var furnaceId:String;
		private var furnaceVo:FurnaceClassVo;
		private var data:Vector.<MixClassVo>;
		private var typeList:Array;
		// Map<int:type2, Vector.<MixClassVo>>
		private var dataMap:Object = { };
		
		
		public function MixUISprite() 
		{
			iview = new MixListUI();
			_view = iview;
			
			TextFieldUtil.autoSetDefaultFormat(iview.topShape.nameTxt);
			
			table = new TabelView();
			table.x = -264;
			table.y = -170;
			table.btwX = 4;
			table.addEventListener(Event.SELECT, selectTableHandler);
			iview.content.addChild(table);
			iview.addEventListener(MixListEvent.SHOW_MIX, showMixView);
			iview.closeBtn.addEventListener(MouseEvent.CLICK, closeHandler);
			
			listView = new DefaultListView(iview.content, iview, 8, false, false);
			listView.tweenTime = 0;
			listView.tweenDelay = 0;
			listView.setGridItem(MixListItem, ListRender);
			listView.init(509, 329, 122, 163, -247, -146);
			listView.pagination = new Pagination();
			listView.pagination.y = 201;
			listView.pagination.x = 0;
			iview.addChild(iview.topShape);
		}
		
		public function getItemView(cid:int):DisplayObject
		{
			var gridItem:GridItem = listView.getItemByKey("cid", cid);
			return gridItem ? gridItem.view : null;
		}
		
		private function showMixView(e:MixListEvent):void 
		{
			e.stopImmediatePropagation();
			
			if (!mixView)
			{
				mixView = new MixView();
				mixView.backHandler = hideMixView;
				mixView.closeHandler = closeMe;
			}
			mixView.setData(e.vo, furnaceId);
			iview.addChild(mixView);
			table.mouseChildren = false;
			//iview.nameTxt.text = e.vo.name;
			iview.addChild(iview.topShape);
			iview.addChild(iview.closeBtn);
			iview.content.visible = false;
		}
		
		private function hideMixView():void 
		{
			if (mixView && mixView.parent)
			{
				mixView.parent.removeChild(mixView);
				mixView.clear();
				table.mouseChildren = true;
				//iview.nameTxt.text = furnaceVo.name;
				iview.content.visible = true;
			}
		}
		
		
		private function closeHandler(e:MouseEvent):void 
		{
			closeMe();
		}
		
		public function showMixByFurnace(furnaceId:String):void
		{
			var data:DataManager = DataManager.getInstance();
			var furnace:FurnaceDecor = MagicWorld(data.worldState.world).getDecorById(furnaceId) as FurnaceDecor;
			if (!furnace) return;
			
			this.furnaceId = furnaceId;
			furnaceVo = data.itemData.getItemClass(furnace.decorVo.cid) as FurnaceClassVo;
			var list:Vector.<MixClassVo> = data.mixData.getMixListByFurnace(furnaceVo.cid);
			setData(list, furnaceVo.types);
			hideMixView();
		}
		
		public function showMixByMid(mid:int, furnaceId:String):void
		{
			var mixData:MixData = DataManager.getInstance().mixData;
			var vo:MixClassVo = mixData.getMixClass(mid);
			this.furnaceId = furnaceId;
			furnaceVo = DataManager.getInstance().itemData.getItemClass(vo.furnaceCid) as FurnaceClassVo;
			var list:Vector.<MixClassVo> = mixData.getMixListByFurnace(vo.furnaceCid);
			setData(list, furnaceVo.types);
			showMixView(new MixListEvent("", false, false, vo));
		}
		
		private function setData(list:Vector.<MixClassVo>, typeList:Array):void
		{
			clear();
			data = list;
			crateTabList(table, typeList);
			iview.topShape.nameTxt.text = furnaceVo.name;
		}
		
		private function crateTabList(table:TabelView, typeList:Array):void 
		{
			var len:int = typeList.length;
			if (len < 1) return;;
			
			var arr:Array = [[createTab(0), 0]];
			for (var i:int = 0; i < len; i++)
			{
				arr.push([createTab(typeList[i]), typeList[i]]);
			}
			table.setTabs.apply(null, arr);
			table.select(0);
		}
		
		private function createTab(type:int):MixListTab 
		{
			var tab:MixListTab = new MixListTab();
			tab.setType(type);
			return tab;
		}
		
		private function clear():void 
		{
			data = null;
			dataMap = { };
			
			listView.clear();
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			hideMixView();
			super.closeMe(del);
		}
		
		private function selectTableHandler(e:Event):void 
		{
			listView.setData(filterData(table.selectValue) || []);
		}
		
		private function filterData(type:int):Vector.<MixClassVo>
		{
			if (!data) return null;
			if (0 == type) return data;
			if (dataMap[type] != null) return dataMap[type];
			
			var arr:Vector.<MixClassVo> = new Vector.<MixClassVo>();
			var len:int = data.length;
			for (var i:int = 0; i < len; i++)
			{
				if (ItemType.getType2(data[i].itemCid) == type) arr.push(data[i] as MixClassVo);
			}
			dataMap[type] = arr;
			return arr;
		}
		
	}

}