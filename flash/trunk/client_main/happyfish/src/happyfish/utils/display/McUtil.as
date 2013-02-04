package happyfish.utils.display 
{
	import flash.display.MovieClip;
	/**
	 * ...
	 * @author lite3
	 */
	public class McUtil 
	{
		public static function getFrame(label:String, mc:MovieClip):int
		{
			var arr:Array = mc.currentLabels;
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				if (arr[i].name == label) return arr[i].frame;
			}
			return 0;
		}
		
		/**
		 * 
		 * @param	label1
		 * @param	label2
		 * @return
		 */
		public static function getDistance(mc:MovieClip, label1:String, label2:String):int
		{
			var map:Object = getLabelMap(mc);
			var distance:int = 0;
			if (label1 in map && label2 in map)
			{
				distance = map[label1] - map[label2];
				if (distance < 0) distance = -distance;
			}
			return distance;
		}
		
		/**
		 * 
		 * @param	mc
		 * @return Map<label, frame>
		 */
		public static function getLabelMap(mc:MovieClip):Object
		{
			var map:Object = { };
			var arr:Array = mc.currentLabels;
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				map[arr[i].name] = arr[i].frame;
			}
			return map;
		}
		
		/**
		 * 
		 * @param	mc
		 * @param	...rest [label,script,param,label,script, ....]
		 */
		public static function addLabelScript(mc:MovieClip, ...rest):void
		{
			var pairs:int = rest.length / 2;
			if (0 == pairs) return;
			
			var map:Object = getLabelMap(mc);
			var arr:Array = [];
			for (var i:int = 0; i < pairs; i++)
			{
				if (rest[i] in map)
				{
					arr.push(map[rest[i * 2]] - 1, rest[i * 2 + 1]);
				}
			}
			mc.addFrameScript.apply(mc, arr);
		}
		
		/**
		 * 
		 * @param	mc
		 * @param	...rest [label, label, label, ...]
		 */
		public static function removeLabelScript(mc:MovieClip, ...rest):void
		{
			var len:int = rest.length;
			if (0 == len) return;
			
			var map:Object = getLabelMap(mc);
			var arr:Array = [];
			for (var i:int = 0; i < len; i++)
			{
				if (rest[i] in map)
				{
					arr.push(map[rest[i]] - 1, null);
				}
			}
			mc.addFrameScript.apply(mc, arr);
		}
		
		/**
		 * 
		 * @param	mc
		 * @param	...rest [frame, frame, frame, ....] frame 为帧号:第一帧为1
		 */
		public static function removeFrameScript(mc:MovieClip, ...rest):void
		{
			var len:int = rest.length;
			if (0 == len) return;
			
			var arr:Array = [];
			for (var i:int = 0; i < len; i++)
			{
				arr.push(rest[i] - 1, null);
			}
			mc.addFrameScript.apply(mc, arr);
		}
		
	}

}