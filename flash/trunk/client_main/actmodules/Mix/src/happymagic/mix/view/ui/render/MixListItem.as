package happymagic.mix.view.ui.render 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.manager.DataManager;
	import happymagic.mix.events.MixListEvent;
	import happymagic.mix.view.ItemTip;
	import happymagic.mix.view.ui.ListRender;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.model.vo.classVo.MixClassVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MixListItem extends GridItem 
	{
		private var iview:ListRender;
		private var icon:IconView;
		public var data:MixClassVo;
		
		private var tip:ItemTip;
		private var content:String;
		
		public function MixListItem(ui:MovieClip) 
		{
			super(ui);
			
			iview = ui as ListRender;
			iview.buttonMode = false;
			iview.mouseChildren = true;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			if (iview.border.parent) iview.border.parent.removeChild(iview.border);
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChild(icon);
			
			iview.buildBtn.addEventListener(MouseEvent.CLICK, clickrun);
			iview.addEventListener(MouseEvent.ROLL_OVER, overHandler);
			iview.addEventListener(MouseEvent.ROLL_OUT, outHandler);
		}
		
		private function overHandler(e:MouseEvent):void 
		{
			if (!tip) tip = new ItemTip();
			tip.showText(content);
			tip.x = iview.x + iview.width;
			tip.y = iview.y + iview.height / 2;
			iview.parent.addChild(tip);
		}
		
		private function outHandler(e:MouseEvent):void 
		{
			if (tip && tip.parent) tip.parent.removeChild(tip);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			iview.dispatchEvent(new MixListEvent(MixListEvent.SHOW_MIX, true, false, data));
		}
		
		override public function setData(value:Object):void 
		{
			data = value as MixClassVo;
			var itemVo:BaseItemClassVo = DataManager.getInstance().itemData.getItemClass(data.itemCid);
			content = itemVo.content;
			icon.setData(itemVo.className);
			iview.nameTxt.text = itemVo.name;
			iview.worthTxt.text = itemVo.worth + "";
			
			setEffect(itemVo);
		}
		
		private function setEffect(itemVo:BaseItemClassVo):void 
		{
			var idx:int = 0;
			if (itemVo is EquipmentClassVo)
			{
				var keys:Array = ["pa", "pd", "ma", "md", "speed"];
				for (var i:int = 0; i < keys.length; i++)
				{
					if (itemVo[keys[i]] > 0)
					{
						iview["icon" + idx].gotoAndStop(i + 1);
						iview["propTxt" + idx].text = itemVo[keys[i]] + "";
						iview["icon" + idx].visible = true;
						iview["propTxt" + idx].visible = true;
						idx++;
					}
				}
			}else
			{
				
			}
			
			for (; idx < 4; idx++)
			{
				iview["icon" + idx].visible = false;
				iview["icon" + idx].stop();
				iview["propTxt" + idx].visible = false;
			}
		}
		
	}
}