package happymagic.fixEquip.view.ui.render 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.fixEquip.model.Data;
	import happymagic.fixEquip.model.FixEquipItemVo;
	import happymagic.fixEquip.view.ui.FixEquipItemUI;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.model.vo.ItemVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class FixEquipItem extends GridItem 
	{
		public var data:Object;
		private var defaultWearColor:uint;
		
		private var iview:FixEquipItemUI;
		private var icon:IconView;
		
		public function FixEquipItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as FixEquipItemUI;
			iview.mouseChildren = true;
			iview.mouseEnabled = false;
			iview.buttonMode = false;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			defaultWearColor = iview.wearTxt.textColor;
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChild(icon);
			iview.removeChild(iview.border);
			iview.border = null;
			
			iview.removeEventListener(MouseEvent.CLICK, itemSelectFun);
			iview.fixBtn.addEventListener(MouseEvent.CLICK, itemSelectFun);
		}
		
		override public function setData(value:Object):void 
		{
			data = value;
			var vo:FixEquipItemVo = FixEquipItemVo(value);
			icon.setData(vo.base.className);
			iview.nameTxt.text = vo.base.name;
			iview.wearFlag.visible = vo.inDepot;
			iview.notFixFlag.visible = !vo.base.canFix;
			
			var levelEnough:Boolean = vo.base.fixLevel <= Data.instance.smithyBuildLevel;
			var canFix:Boolean = vo.base.canFix && levelEnough;
			iview.levelFlag.visible = vo.base.canFix && !levelEnough;
			iview.levelTxt.visible = vo.base.canFix && !levelEnough;
			iview.levelTxt.text = vo.base.fixLevel + "";
			iview.fixBtn.visible = vo.base.canFix && levelEnough;
			iview.coin.visible = vo.base.canFix;
			iview.priceTxt.visible = vo.base.canFix;
			iview.priceTxt.text = vo.fixPrice + "";
			
			if (0 == vo.wear)
			{
				iview.wearTxt.text = LocaleWords.getInstance().getWord("wearZero");
				iview.wearTxt.textColor = 0xFF0000;
			}else
			{
				var wearR:int = Math.round(vo.wear / vo.base.maxWear * 100);
				wearR ||= 1;
				iview.wearTxt.text = wearR + "%";
				iview.wearTxt.textColor = defaultWearColor;
			}
		}
	}

}