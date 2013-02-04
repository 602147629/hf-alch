package happymagic.model.vo 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class RolePropType 
	{
		
		public static const WATER:int = 3;
		public static const FIRE:int = 2;
		public static const WIND:int = 1;
		
		public static function getPropString(prop:int):String
		{
			switch(prop)
			{
				case WATER : return "water";
				case FIRE  : return "fire";
				case WIND  : return "wind";
			}
			return null;
		}
		
	}

}