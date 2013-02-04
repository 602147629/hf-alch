package happymagic.battle.view.ui 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import happymagic.battle.view.GreenNumberUI;
	import happymagic.battle.view.MissUi;
	import happymagic.battle.view.RedNumberUI;
	/**
	 * 自定义数字
	 * @author XiaJunJie
	 */
	public class CustomNumber extends Sprite 
	{
		public var numPicClass:Class; //数字的图 是一个MovieClip 从1-10帧分别为0-9
		private var _value:int;
		
		public var spacing:int; //间距
		
		public function CustomNumber(font:String,spacing:int = 16) 
		{
			this.spacing = spacing;
			
			switch(font)
			{
				case RED:
					numPicClass = RedNumberUI;
				break;
				case GREEN:
					numPicClass = GreenNumberUI;
				break;
			}
		}
		
		public function set value(v:*):void
		{
			while (numChildren > 0) removeChildAt(0);
			
			if (v is int) _value = v;
			else if (v is Number) _value = Math.ceil(v);
			else if (v is String) _value = Math.ceil(Number(v));
			
			if (_value == 0)
			{
				addChild(new MissUi);
				return;
			}
			
			if (!numPicClass) return;
			
			var str:String = String(v);
			var offset:int = -str.length * spacing / 2;
			
			for (var i:int = 0; i < str.length; i++)
			{
				var frame:int = Number(String(str.substr(i, 1))) + 1;
				if (isNaN(frame)) continue;
				var singlePic:MovieClip = new numPicClass();
				singlePic.gotoAndStop(frame);
				addChild(singlePic);
				singlePic.x = offset + i * spacing;
			}
		}
		
		public function get value():int
		{
			return _value;
		}
		
		//飘----------------------------------------------------
		private var _alphaT:Number;
		public function set alphaT(value:Number):void
		{
			_alphaT = value;
			if (_alphaT > 0.5) alpha = (1 - _alphaT) * 2;
		}
		public function get alphaT():Number
		{
			return _alphaT;
		}
		
		
		private var longitudinalVelocity_Drop:Number; //纵向速度
		
		private var _dropT:Number;
		public function set dropT(value:Number):void
		{
			_dropT = value;
			
			x += transverseVelocity_Drop;
			y += longitudinalVelocity_Drop;
			
			longitudinalVelocity_Drop += g_Drop;
		}
		public function get dropT():Number
		{
			return _dropT;
		}
		
		public function resetDropT():void
		{
			_dropT = 0;
			longitudinalVelocity_Drop = initLongitudinalVelocity_Drop;
		}
		
		//常量--------------------------------------------
		public static const RED:String = "red";
		public static const GREEN:String = "green";
		
		public static const piaoDuration:Number = 1; //飘字时间
		public static const height_Up:int = 60; //向上飘字的高度
		private const transverseVelocity_Drop:Number = 3; //横向速度
		private const initLongitudinalVelocity_Drop:Number = -12; //向上初速度
		private const g_Drop:Number = 1.5; //重力加速度
	}
}