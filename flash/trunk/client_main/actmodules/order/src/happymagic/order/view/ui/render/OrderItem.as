package happymagic.order.view.ui.render 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.text.TextField;
	import happyfish.display.ui.GridItem;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.model.data.OrderData;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.view.Customer;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderItem extends GridItem 
	{
		private var iview:OrderItemRender;
		private var customer:Customer;
		//private var 
		
		public function OrderItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as OrderItemRender;
			
			iview.border.visible = false;
		}
		
		override public function setData(value:Object):void 
		{
			this.customer = value as Customer;
			selected = selected;
			iview.addEventListener(Event.ENTER_FRAME, enterFrameHandler);
		}
		
		private function enterFrameHandler(e:Event):void 
		{
			e.target.removeEventListener(Event.ENTER_FRAME, enterFrameHandler);
			var txt:TextField = TextField(iview.getChildByName("txt"));
			TextFieldUtil.autoSetDefaultFormat(txt);
			txt.text = customer.order.avatarName;
		}
		
		override public function set selected(value:Boolean):void 
		{
			super.selected = value;
			if (!customer) return;
			var label:String = value ? "Select" : "Unselect";
			iview.gotoAndStop(label + customer.order.awardType);
			iview.addEventListener(Event.ENTER_FRAME, enterFrameHandler);
		}
		
		
		
	}

}