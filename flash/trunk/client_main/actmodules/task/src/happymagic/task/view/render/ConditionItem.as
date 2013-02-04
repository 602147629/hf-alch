package happymagic.task.view.render 
{
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happymagic.display.view.ui.ItemIcon;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ConditionItem extends GridItem 
	{
		private var vo:ConditionVo;
		private var iview:ConditionRender
		private var icon:ItemIcon;
		
		public function ConditionItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as ConditionRender;
			
			var rect:Rectangle = new Rectangle(iview.border.x, iview.border.y, iview.border.width, iview.border.height);
			icon = new ItemIcon(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.border));
			iview.border.visible = false;
		}
		
		override public function setData(value:Object):void 
		{
			vo = value as ConditionVo;
			icon.setCondition(vo);
			
			iview.contentTxt.text = vo.content;
			iview.numTxt.text = vo.curNum + "/" + vo.num;
			iview.numTxt.textColor = vo.curNum >= vo.num ? 0x005E2F : 0x801A00;
			iview.stateMc.gotoAndStop(vo.curNum >= vo.num ? "Complete" : "Uncomplete");
		}
		
	}

}