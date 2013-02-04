package happymagic.display.view.task 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.display.view.ui.DefaultAwardItemRenderUI;
	import happymagic.display.view.ui.ItemIcon;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.classVo.MixClassVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskNeedItemView extends GridItem
	{
		private var icon:ItemIcon;
		public var data:ConditionVo;
		private var iview:DefaultAwardItemRenderUI;
		public function TaskNeedItemView(_view:MovieClip) 
		{
			view = _view;
			iview = view as DefaultAwardItemRenderUI;
			super(view);
			view.mouseChildren = false;
			iview.border.alpha = 0;
		}
		
		override public function setData(value:Object):void {
			
			data = value as ConditionVo;
			loadIcon();
			
			iview.nameTxt.text = data.getName();
			if (data.num > 0) {
				
				iview.numTxt.text = data.num.toString();
			}else {
				iview.numTxt.text = "";
			}
		}
		
		private function loadIcon():void 
		{
			if (!icon) 
			{
				var rect:Rectangle = iview.border.getBounds(iview);
				icon = new ItemIcon(rect.width, rect.height, rect);
			}
			
			icon.setCondition(data);
			view.addChildAt(icon,view.getChildIndex(iview.border));
		}
		
	}

}