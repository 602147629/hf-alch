package happymagic.roleInfo.view.ui 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.roleInfo.commands.ReplaceEquipCommand;
	import happymagic.roleInfo.view.EquipTip;
	import happymagic.roleInfo.view.ui.render.EquiItemRender;
	import happymagic.roleInfo.vo.EquiCompareVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class EquipItem extends GridItem 
	{
		private var vo:EquiCompareVo;
		private var iview:EquiItemRender;
		private var icon:IconView;
		private var tip:EquipTip;
		
		public function EquipItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as EquiItemRender;
			view.mouseChildren = true;
			view.buttonMode = false;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			TextFieldUtil.autoSetTxtDefaultFormat(iview.needLevelMc);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.border));
			iview.removeChild(iview.border);
			icon.mouseChildren = false;
			icon.addEventListener(MouseEvent.ROLL_OVER, skillOverHandler); 
			icon.addEventListener(MouseEvent.ROLL_OUT, skillOutHandler); 
			
			iview.okBtn.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		private function skillOverHandler(e:MouseEvent):void 
		{
			if (!tip) tip = new EquipTip();
			tip.x = e.currentTarget.x + e.currentTarget.width;
			tip.y = e.currentTarget.y + e.currentTarget.height/2;
			iview.parent.addChild(tip);
			tip.setData(vo.now, 0 == vo.nowWear);
		}
		
		private function skillOutHandler(e:MouseEvent):void 
		{
			if (tip && tip.parent) tip.parent.removeChild(tip);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			icon.mouseEnabled = false;
			new ReplaceEquipCommand().replaceEquip(vo);
		}
		
		// 取消默认的选择
		override protected function itemSelectFun(e:MouseEvent):void { }
		
		override public function setData(value:Object):void 
		{
			vo = value as EquiCompareVo;
			iview.nameTxt.text = vo.now.name;
			iview.needLevelMc.visible = vo.roleLevel < vo.now.level;
			iview.needLevelMc.levelTxt.text = vo.now.level;
			iview.okBtn.visible = vo.roleLevel >= vo.now.level && vo.nowId;
			icon.setData(vo.now.className);
			
			var wearS:String = null;
			if (0 == vo.now.maxWear)
			{
				wearS = LocaleWords.getInstance().getWord("wearInfinite");
			}else if (0 == vo.nowWear)
			{
				wearS = LocaleWords.getInstance().getWord("wearZero");
			}else
			{
				var wearR:int = Math.round(vo.nowWear / vo.now.maxWear * 100) || 1;
				wearS = wearR + "%";
			}
			iview.wearTxt.text = wearS;
			
			setCompare();
		}
		
		private function setCompare():void 
		{
			// [物攻,物防,魔攻,魔防,速度]
			var props:Array = ["pa", "pd", "ma", "md", "speed"];
			var arr:Array = [];
			for (var i:int = 0; i < props.length; i++)
			{
				arr[i] = { key:props[i], value:vo.now[props[i]] };
			}
			arr.sortOn("value", Array.NUMERIC|Array.DESCENDING);
			for (i = 0; i < 3; i++)
			{
				var now:int = vo.now[arr[i].key];
				if (0 == now)
				{
					iview["icon" + i].visible = false;
					iview["txt" + i].visible = false;
				}
				var old:int = vo.old ? vo.old[arr[i].key] : 0;
				iview["icon" + i].gotoAndStop(arr[i].key);
				var color:uint = 0x0;
				var flag:String = "";
				if (now > old)
				{
					color = 0x437830;
					flag = "↑";
				}
				else if (now < old)
				{
					color = 0x742E2E;
					flag = "↓";
				}
				iview["txt" + i].text = now + flag;
				iview["txt" + i].textColor = color;
			}
		}
	}

}