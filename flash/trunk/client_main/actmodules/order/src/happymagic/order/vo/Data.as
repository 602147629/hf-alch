package happymagic.order.vo 
{
	import happymagic.order.view.Customer;
	/**
	 * ...
	 * @author lite3
	 */
	public class Data 
	{
		public var requestNoviceOrder:Function;
		public var notRequestOrder:Boolean = false;
		
		public const workingList:Array = [];
		public const personList:Array = [];
		public const orderList:Array = [];
		
		public function getCustomerById(id:String):Customer
		{
			for (var i:int = personList.length - 1; i >= 0; i--)
			{
				if (Customer(personList[i]).order.id == id)
				{
					return Customer(personList[i]);
				}
			}
			return null;
		}
		
		
		private static var _instance:Data;
		public static function get instance():Data
		{
			return _instance ||= new Data();
		}
	}

}