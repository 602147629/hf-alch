package happyfish.veal.parse 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class Sign 
	{
		// ==
		public static const NameSpace:int  = 14906; // :: (':'.charCodeAt(0) << 8) + ':'.charCodeAt(0)
		public static const Equality:int   = 15677; // == ('='.charCodeAt(0) << 8) + '='.charCodeAt(0)
		public static const Inequality:int = 8509;  // != ('!'.charCodeAt(0) << 8)  + '='.charCodeAt(0)
		public static const GreaterThanOrEqualTo:int = 15933;  // >= ('>'.charCodeAt(0) << 8)  + '='.charCodeAt(0)
		public static const LessThanOrEqualTo:int = 15421;     // <= ('<'.charCodeAt(0) << 8)  + '='.charCodeAt(0)
		
		public var ch:int
		public function Sign(ch:int) 
		{
			this.ch = ch;
		}
		
		public function toString():String
		{
			return "[Sign " + String.fromCharCode(ch) + "]";
		}
	}
}