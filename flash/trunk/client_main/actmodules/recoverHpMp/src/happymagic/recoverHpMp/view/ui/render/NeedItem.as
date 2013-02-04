package happymagic.recoverHpMp.view.ui.render 
{
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.ItemIcon;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class NeedItem extends GridItem 
	{
		private var iview:NeedItemRender;
		private var icon:ItemIcon;
		
		public function NeedItem(ui:MovieClip) 
		{
			super(ui);
			
			iview = ui as NeedItemRender;
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new ItemIcon(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.border));
			iview.border.visible = false;
		}
		
		override public function setData(value:Object):void 
		{
			var vo:ConditionVo = value as ConditionVo;
			icon.setCondition(vo);
			var name:String = vo.getName();
			iview.nameTxt.text = name || vo.content + "";
			iview.numTxt.text = vo.curNum + "/" + vo.num;
			iview.finishFlag.visible = vo.curNum >= vo.num;
			if (!name)
			{
				TextFieldUtil.autoSetDefaultFormat(icon.icon.txt);
				icon.icon.txt.text = vo.num + "";
			}
		}
	}

}