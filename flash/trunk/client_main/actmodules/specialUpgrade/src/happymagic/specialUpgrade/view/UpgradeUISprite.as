package happymagic.specialUpgrade.view 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.scene.world.MagicWorld;
	import happymagic.specialUpgrade.commands.UpgradeCommand;
	import happymagic.specialUpgrade.model.SpecialUpgaradeVo;
	import happymagic.specialUpgrade.view.ui.render.UpgradeNeedItem;
	import happymagic.specialUpgrade.view.ui.render.UpgradeNeedItemRender;
	import happymagic.specialUpgrade.view.ui.UpgradeUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class UpgradeUISprite extends UISprite 
	{
		private var vo:SpecialUpgaradeVo;
		
		private var npcIcon:IconView;
		//private var buildIcon:IconView;
		private var listView:DefaultListView;
		
		private var movieIdx:int;
		
		private var iview:UpgradeUI;
		
		public function UpgradeUISprite() 
		{
			iview = new UpgradeUI();
			_view = iview;
			
			movieIdx = iview.getChildIndex(iview.suceesMovie);
			iview.removeChild(iview.suceesMovie);
			iview.suceesMovie.stop();
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.npcBorder.x, iview.npcBorder.y, iview.npcBorder.width, iview.npcBorder.height);
			npcIcon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(npcIcon, iview.getChildIndex(iview.npcBorder));
			iview.removeChild(iview.npcBorder);
			
			//rect = new Rectangle(iview.buildBorder.x, iview.buildBorder.y, iview.buildBorder.width, iview.buildBorder.height);
			//buildIcon = new IconView(rect.width, rect.height, rect);
			//iview.addChildAt(buildIcon, iview.getChildIndex(iview.buildBorder));
			//iview.removeChild(iview.buildBorder);
			
			listView = new DefaultListView(iview, iview, 5);
			listView.init(297, 268, 297, 50, -60, -65);
			listView.setGridItem(UpgradeNeedItem, UpgradeNeedItemRender);
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			
			iview.starLevel.stop();
			
			EventManager.addEventListener(DataManagerEvent.ITEMS_CHANGE, updataItems);
		}
		
		public function setData(vo:SpecialUpgaradeVo):void
		{
			this.vo = vo;
			var buildingName:String = MagicWorld(DataManager.getInstance().worldState.world).getItemById(vo.id).data.name;
			iview.titleTxt.text = LocaleWords.getInstance().getWord("specialUpgradeTitle", buildingName);
			npcIcon.setData(vo.npcClass);
			//buildIcon.setData(vo.npcClass);
			var chats:Array = vo.npcChat.split("&&");
			iview.chatTxt.text = chats[int(Math.random() * chats.length)];
			iview.nextLevelTxt.text = LocaleWords.getInstance().getWord("LVx", vo.level);
			updataItems(null);
			
			showStarLevel(vo.content);
		}
		
		private function showStarLevel(content:String):void 
		{
			var arr:Array = content.split("\n");
			var firstLine:String = arr[0];
			var result:Array = /{\d星}/.exec(firstLine) as Array;
			if (result != null)
			{
				var str:String = result[0];
				iview.starLevel.gotoAndStop(parseInt(str.substr(1)));
				iview.starTxt.text = firstLine.substr(str.length);
				arr.shift();
				iview.starTxt.visible = true;
				iview.starLevel.visible = true;
				iview.awardTxt.y = iview.starTxt.y + iview.starTxt.height;
				iview.addEventListener(Event.ENTER_FRAME, layoutStarTxt);
			}else
			{
				iview.starTxt.visible = false;
				iview.starLevel.visible = false;
				iview.awardTxt.y = iview.starTxt.y;
			}
			content = arr.join("\n");
			iview.awardTxt.text = content;
		}
		
		private function layoutStarTxt(e:Event):void 
		{
			e.currentTarget.removeEventListener(e.type, layoutStarTxt);
			iview.starTxt.x = iview.starLevel.x + iview.starLevel.width;
		}
		
		private function updataItems(e:DataManagerEvent):void 
		{
			var getCount:Function = DataManager.getInstance().itemData.getItemCount;
			var canUpgrade:Boolean = true;
			for (var i:int = vo.conditions.length - 1; i >= 0; i--)
			{
				var condition:ConditionVo = vo.conditions[i];
				condition.updateCurNum();
				canUpgrade &&= condition.isFinish();
			}
			iview.upgradeBtn.mouseEnabled = canUpgrade;
			iview.upgradeBtn.filters = canUpgrade ? [] : [FiltersDomain.grayFilter];
			listView.setData(vo.conditions);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.upgradeBtn :
					new UpgradeCommand().upgrade(vo.id, upgradeComplete);
					iview.mouseChildren = false;
					break;
					
				case iview.closeBtn :
					closeMe();
					break;
			}
		}
		
		private function upgradeComplete(sucess:Boolean):void 
		{
			if (sucess)
			{
				iview.addChildAt(iview.suceesMovie, movieIdx);
				iview.suceesMovie.addFrameScript(iview.suceesMovie.totalFrames - 1, movieComplete);
				iview.suceesMovie.gotoAndPlay(1);
			}else
			{
				movieComplete();
			}
		}
		
		private function movieComplete():void 
		{
			iview.mouseChildren = true;
			iview.suceesMovie.stop();
			if (iview.suceesMovie.parent) iview.removeChild(iview.suceesMovie);
			closeMe();
		}
	}
}