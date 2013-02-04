package happymagic.view
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.event.StorageEvent;
	import happymagic.events.DiyEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.StoragItemUseCommand;
	import happymagic.model.data.RoleData;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.classVo.DecorClassVo;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.model.vo.classVo.FurnaceClassVo;
	import happymagic.model.vo.classVo.GoodsClassVo;
	import happymagic.model.vo.classVo.ItemLabelType;
	import happymagic.model.vo.classVo.ScrollClassVo;
	import happymagic.model.vo.EffectVo;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.model.vo.StoragItemVo;
	import happymagic.scene.world.SceneType;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class StorageItemView extends GridItem
	{
		private var iview:StorageItemViewUi;
		public var data:ItemVo;
		private var itemclassdata:BaseItemClassVo;
		private var conentarray:Array = new Array();
		private var list:StorageListView;
		private var mcpoint:Point;
		private var tips:StorageItemTipUI;
		private var color:Array;
		
		public function StorageItemView(_uiview:MovieClip)
		{
			super(_uiview);
			
			iview = _uiview as StorageItemViewUi;
			
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
			iview.useBtn.visible = false;
			iview.placeBtn.visible = false;
			mcpoint = new Point();
			mcpoint.x = 33;
			mcpoint.y = 59;
			iview.icon.visible = false;
			iview.wear.visible = false;
		
		}
		
		private function clickrun(e:MouseEvent):void
		{
			switch (e.target.name)
			{
				case "useBtn": 
					iview.mouseChildren = false;
					iview.mouseEnabled = false;
					var roleData:RoleData = DataManager.getInstance().roleData;
					if (DataManager.getInstance().itemData.isSpecialItem(data.cid)||data.base is ScrollClassVo)
					{
						
						var storagItemUseCommand:StoragItemUseCommand = new StoragItemUseCommand();
						storagItemUseCommand.useItem(data.cid);
						storagItemUseCommand.addEventListener(Event.COMPLETE, storagItemUseCommandComplete);
					}
					else
					{
						iview.mouseChildren = true;
						iview.mouseEnabled = true;
						var selectPlayerAddItemView:SelectPlayerAddView = DisplayManager.uiSprite.addModule("SelectPlayerAddView", "happymagic.view.SelectPlayerAddView", false, AlginType.CENTER, 20, -30) as SelectPlayerAddView;
						selectPlayerAddItemView.setData(data.cid,conentarray);
						DisplayManager.uiSprite.setBg(selectPlayerAddItemView);
					}
					break;
				
				case "placeBtn": 
					
					if (DataManager.getInstance().curSceneType == SceneType.TYPE_HOME)
					{

					}
					else
					{
						DisplayManager.showSysMsg(LocaleWords.getInstance().getWord("Storageword4"));
						return;
					}
					
					ModuleManager.getInstance().closeModule("StorageView", true);
					//发送DIY请求-----
					var event:DiyEvent = new DiyEvent(DiyEvent.ADD_ITEM);
					event.item = data;
					EventManager.getInstance().dispatchEvent(event);
					
					EventManager.getInstance().dispatchEvent(new StorageEvent(StorageEvent.CLOSEMOUDLE));
					break;
			
			}
		
		}
		
		private function storagItemUseCommandComplete(e:Event):void
		{
			iview.mouseChildren = true;
			iview.mouseEnabled = true;
			
			if (e.target.objdata.result.status == 1 && data.base is ScrollClassVo)
			{
				var learnFeedView:LearnFeedView = DisplayManager.uiSprite.addModule("LearnFeedView", "happymagic.view.LearnFeedView", false, AlginType.CENTER, 20, -70) as LearnFeedView;
				learnFeedView.setData(itemclassdata.name);
				DisplayManager.uiSprite.setBg(learnFeedView);
			}
			
			EventManager.getInstance().dispatchEvent(new StorageEvent(StorageEvent.USEITEMCOMPLETE));
		}
		
		override public function setData(_data:Object):void
		{
			var vo:StoragItemVo;
			
			data = _data as ItemVo;
			
			itemclassdata = DataManager.getInstance().itemData.getItemClass(data.cid);
			
			iview.nametxt.text = itemclassdata.name;
			
			iview.num.text = String(data.num);
			
			if (data.base is FurnaceClassVo || data.base is DecorClassVo)
			{
				iview.placeBtn.visible = true;
				mcpoint = new Point();
				mcpoint.x = 33;
				mcpoint.y = 55;				
			}
			
			if (data.base is GoodsClassVo)
			{
				iview.useBtn.visible = true;
				iview.icon.y += 1;
				mcpoint = new Point();
				mcpoint.x = 33;
				mcpoint.y = 50;
				
				var skillAndItemVo:SkillAndItemVo = DataManager.getInstance().getSkillAndItemVo(int((data.base as GoodsClassVo).cid));
				var arr:Vector.<SkillAndItemVo> = DataManager.getInstance().skillAndItems;
				if (skillAndItemVo)
				{
					var vector:Vector.<EffectVo> = skillAndItemVo.effectList;
					for (var i:int = 0; i < vector.length; i++ )
					{
						switch(vector[i].type)
						{
							case 2:
								vo = new StoragItemVo();
								vo.type = 8;	
                                vo.num = vector[i].value;
								conentarray.push(vo);
							break;
					
							case 3:
								vo = new StoragItemVo();
								vo.type = 9;
								vo.num = vector[i].value;
								conentarray.push(vo);
							break;
								
							case 4:
								vo = new StoragItemVo();
								vo.type = 8;
                                vo.num = vector[i].value;
								conentarray.push(vo);
								break;
					
							case 5:
								vo = new StoragItemVo();
								vo.type = 9;
								vo.num = vector[i].value;
								conentarray.push(vo);
							break;
								
								
							case 100:
								vo = new StoragItemVo();
								vo.type = 10;
								vo.num = vector[i].value;
								conentarray.push(vo);	
							break;
							
							case 101:
								vo = new StoragItemVo();
								vo.type = 11;
								vo.num = vector[i].value;
								conentarray.push(vo);										
							break;
							
							case 102:
								vo = new StoragItemVo();
								vo.type = 12;
								vo.num = vector[i].value;
								conentarray.push(vo);
							break;								
						}
					}
					
				}		
				if (conentarray.length)
				{
					iview.icon.visible = true;
					switch(conentarray[0].type)
					{
						case 8:
							if (conentarray[0].num < 1)
							{
								iview.icon["num"].text = conentarray[0].num * 100 + "%";
							}
							else
							{
								iview.icon["num"].text = conentarray[0].num.toString();
							}	
									
							iview.icon["icon"].gotoAndStop(6);
						break;
								
					    case 9:
							if (conentarray[0].num < 1)
							{
								iview.icon["num"].text = conentarray[0].num * 100 + "%";
							}
							else
							{
								iview.icon["num"].text = conentarray[0].num.toString();
							}	
									
							iview.icon["icon"].gotoAndStop(7);								
						break;								
								
						case 10:
						    iview.icon["num"].text = conentarray[0].num.toString();
							iview.icon["icon"].gotoAndStop(8);									
						break;		
								
						case 11:
							iview.icon["num"].text = conentarray[0].num.toString();
							iview.icon["icon"].gotoAndStop(9);									
						break;	
						
						case 12:
							iview.icon.visible = false;									
						break;							
					}
				}
				else
				{
					iview.useBtn.visible = false;					
				}
				
				if (data.base.labelMap[ItemLabelType.IN_BATTLE])
				{
					iview.useBtn.visible = false;
				}
				
			}
			
			if (data.base is ScrollClassVo)
			{
				if (data.base.type2 == ItemType.Skill)
				{
					
				}
				else
				{
					iview.useBtn.visible = true;					
				}
				
				mcpoint = new Point();
				mcpoint.x = 33;
				mcpoint.y = 52;
				
				if (DataManager.getInstance().currentUser.level < (data.base as ScrollClassVo).level)
				{
					BtnStateControl.setBtnState(iview.useBtn, false);
				}
			}
			
			var str:String;
			if (data.base is EquipmentClassVo)
			{
				mcpoint = new Point();
				mcpoint.x = 33;
				mcpoint.y = 35;
				
				var currentwear:int = DataManager.getInstance().itemData.getItem(data.id).wear;
				var maxwear:int = (data.base as EquipmentClassVo).maxWear;
				iview.wear.visible = true;
				if (maxwear == 0)
				{
					iview.wear.text = LocaleWords.getInstance().getWord("Storageword1");
				}
				else
				{
					var cha:Number = currentwear * 100 / maxwear;
					
					if (0 < cha < 1)
					{
						cha = 1;
					}
					
					iview.wear.text = LocaleWords.getInstance().getWord("Storageword3") + cha + "%";
				}
				
				
				
				
				if (((data.base) as EquipmentClassVo).pa)
				{
					vo = new StoragItemVo();
					vo.type = 1;
					vo.num = (data.base as EquipmentClassVo).pa;
					conentarray.push(vo);
				}
				
				if (((data.base) as EquipmentClassVo).pd)
				{
					vo = new StoragItemVo();
					vo.type = 2;
					vo.num = (data.base as EquipmentClassVo).pd;
					conentarray.push(vo);
				}
				
				if (((data.base) as EquipmentClassVo).speed)
				{
					vo = new StoragItemVo();
					vo.type = 5;
					vo.num = (data.base as EquipmentClassVo).speed;
					conentarray.push(vo);
				}
				
				if (((data.base) as EquipmentClassVo).ma)
				{
					vo = new StoragItemVo();
					vo.type = 3;
					vo.num = (data.base as EquipmentClassVo).ma;
					conentarray.push(vo);
				}
				
				if (((data.base) as EquipmentClassVo).md)
				{
					vo = new StoragItemVo();
					vo.type = 4;
					vo.num = (data.base as EquipmentClassVo).md;
					conentarray.push(vo);
				}
				
				if (((data.base) as EquipmentClassVo).hp)
				{
					vo = new StoragItemVo();
					vo.type = 6;
					vo.num = (data.base as EquipmentClassVo).hp;
					conentarray.push(vo);
				}
				
				if (((data.base) as EquipmentClassVo).mp)
				{
					vo = new StoragItemVo();
					vo.type = 7;
					vo.num = (data.base as EquipmentClassVo).mp;
					conentarray.push(vo);
				}
				
				list = new StorageListView(new StorageListViewUi(), iview, 4, true, false);
				list.init(120, 48, 50, 17, 0, 0);
				list.setGridItem(StorageItemItemView, StorageItemItemViewUi);
				list.x = 18;
				list.y = 92;
				list.tweenTime = 0;
				
				list.setData(conentarray);
			}
			
			
			color = iview.filters;
			
			loadicon();
		}
		
		private function loadicon():void
		{
			var icon:IconView = new IconView(48, 48, new Rectangle(mcpoint.x, mcpoint.y, 48, 48));
			icon.setData(itemclassdata.className);
			iview.addChildAt(icon,iview.getChildIndex(iview.wear));
			
			iview.addEventListener(MouseEvent.MOUSE_OVER, mouseover);
			iview.addEventListener(MouseEvent.MOUSE_OUT, mouseout);
			//Tooltips.getInstance().register(icon.icon, itemclassdata.content, Tooltips.getInstance().getBg("defaultBg"));
			//Tooltips.getInstance().register(icon, itemclassdata.content, Tooltips.getInstance().getBg("defaultBg"));
		//	Tooltips.getInstance().register(complexIcon, DataManager.getInstance().itemData.getItemClass(vo.itemCid).content, Tooltips.getInstance().getBg("defaultBg"));
		}
		
		private function mouseout(e:MouseEvent):void 
		{
		    iview.filters = color;
			iview.parent.removeChild(tips);
			
		}
		
		private function mouseover(e:MouseEvent):void 
		{
			if (tips)
			{
				
			}
			else
			{
				tips = new StorageItemTipUI();				
			}

			tips.mouseChildren = false;
			tips.mouseEnabled = false;
			tips.content.text = itemclassdata.content;
			
			iview.parent.addChild(tips);
			tips.x = iview.x + iview.width - 20;
			tips.y = iview.y + iview.height / 2;
			
			if (itemclassdata.coin)
			{
				tips.price.text = LocaleWords.getInstance().getWord("Storageword2") + String(itemclassdata.coin);
				tips.pricemc.visible = true;
			}
			else
			{
				tips.pricemc.visible = false;
				tips.price.text = "";
			}	
			
			iview.filters = [new GlowFilter(0xFF6600, 1, 10, 10, 2.5, 1, false, false)];
			
			
		}
	
	}

}