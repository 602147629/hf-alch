package happymagic.guide.api 
{
	import flash.display.DisplayObject;
	import flash.utils.getDefinitionByName;
	import happyfish.scene.world.grid.Person;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.order.OrderVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderAPI 
	{
		
		public function notRequest():void
		{
			var data:Object = getDefinitionByName("happymagic.order.vo.Data").instance;
			data.notRequestOrder = true;
		}
		
		public function autoRequest():void
		{
			var data:Object = getDefinitionByName("happymagic.order.vo.Data").instance;
			data.notRequestOrder = false;
		}
		
		public function requestOrder():void
		{
			var data:Object = getDefinitionByName("happymagic.order.vo.Data").instance;
			data.requestNoviceOrder();
		}
		
		public function get notMovingCustomer():Boolean
		{
			var data:Object = getDefinitionByName("happymagic.order.vo.Data").instance;
			var person:Object = data.personList.length > 0 ? data.personList[0] : null;
			return !person.moving;
		}
		
		public function get getCustomerView():DisplayObject
		{
			var data:Object = getDefinitionByName("happymagic.order.vo.Data").instance;
			if(data.personList.length > 0) return data.personList[0].view.container;
			return null;
		}
		
		public function hasAccept():Boolean
		{
			var v:Vector.<OrderVo> = DataManager.getInstance().orderData.getOrderList(false, true, false);
			return v ? v.length > 0 : false;
		}
		
		public function hasUnaccept():Boolean
		{
			var v:Vector.<OrderVo> = DataManager.getInstance().orderData.getOrderList(false, false, true);
			return v ? v.length > 0 : false;
		}
		
	}

}