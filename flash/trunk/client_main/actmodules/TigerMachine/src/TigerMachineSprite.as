package  
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.geom.Matrix;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import flash.utils.Timer;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happymagic.model.vo.RoleVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class TigerMachineSprite extends Sprite
	{
		private var selectId:int;
		private var data:Array;
		private var sampleSprite:Sprite;
		private var matrix:Matrix;
		private var raceBmp:Bitmap;
		private var mc:titleSprite;
        private var speed:int = 28;
		private var showW:int = 76;
		private var showH:int = 274;		
		private var runNum:int = 0; //已经滚动多少像素点
		private var alllength:int;//总长度
		private var startslowdown:Boolean = false;//开始减速
		private var selectlength:int;
		public var complete:Boolean = false;//完成
		private var timer:Timer;
		private var type:int;//表示是自己还是敌人
		public var last:int = 0;//是不是最后一个
		
		private var iconallnum:int;
		private var iconnum:int = 0;
		
		public function TigerMachineSprite(_type:int) 
		{
			type = _type;
			timer = new Timer(33);
		}

		public function init(_data:Array):void
		{
            var kongitem:konggird; 	
			var myrole1:myrolegird;
			var myrole2:friendrolegird;
            var icon:IconView;
			var num:int;
			data = _data;
			var j:int;
			var i:int;
			var k:int
			mc = new titleSprite();

			var num1:int = data.length % 3;
			
			if (num1 == 0)
			{
				num1 = 3;
			}


			for (i = 0; i < 3 - num1; i++ ) 
			{
				var vo:RoleVo = new RoleVo();
				vo.id = -1;
				vo.level = 0;
				data.push(vo);
			}
			
			//设置总共有多少个icon需要加载
			for (j = 0; j < 3; j++) 
			{
				if ((data[j] as RoleVo).id>=0)
				{				
					iconallnum++;
				}				
			}
			
			num = _data.length / 3 ;
			
			if (num == 0)
			{
				num = 1;
			}
			for (i = 1; i < num; i++) 
			{
				for (j = 0; j < 3; j++) 
			    {
					if ((data[i*3+j] as RoleVo).id>=0)
					{
						iconallnum++;	
					}					
				}

			}
			
			//--------------------------------------------------			
			//设置第一张位图的图片
			for (j = 0; j < 3; j++) 
			{
				if ((data[j] as RoleVo).id>=0)
				{
					if (type == 1)
					{
						myrole1 = new myrolegird();
						myrole1.y += j * 91;
						icon = new IconView(80, 80, new Rectangle(0, 5, 80, 80));
						icon.addEventListener(Event.COMPLETE, loadcomplete);						
						icon.setData((data[j] as RoleVo).className);
						myrole1.addChild(icon);
						mc.addChild(myrole1);	
					}
					else
					{
						myrole2 = new friendrolegird();
						myrole2.y += j * 91;
						icon = new IconView(80, 80, new Rectangle(0, 5, 80, 80));
						icon.addEventListener(Event.COMPLETE, loadcomplete);						
						icon.setData((data[j] as RoleVo).className);
						myrole2.addChild(icon);						
						mc.addChild(myrole2);					
					}
					
				}
				else
				{
					kongitem = new konggird();
					kongitem.y += j * 91;
					mc.addChild(kongitem);
				}

			}				
			
			//总共几张图片
			num = _data.length / 3 ;
			if (num == 0)
			{
				num = 1;
			}
			for (i = 1; i < num; i++) 
			{
				var tempmc:titleSprite = new titleSprite();
				mc.addChild(tempmc);
				//tempmc.x = 10;
				tempmc.y += showH * i;
				
				for (j = 0; j < 3; j++) 
			    {
					if ((data[i*3+j] as RoleVo).id>=0)
					{
						if (type == 1)
						{
							myrole1 = new myrolegird();
							myrole1.y += j * 91;
							icon = new IconView(80, 80, new Rectangle(0, 5, 80, 80));
							myrole1.addChild(icon);	
							icon.addEventListener(Event.COMPLETE, loadcomplete);
							icon.setData((data[i*3+j] as RoleVo).className);
							tempmc.addChild(myrole1);		
						}
						else
						{
							myrole2 = new friendrolegird();
							myrole2.y += j * 91;
							icon = new IconView(80, 80, new Rectangle(0, 5, 80, 80));
							myrole2.addChild(icon);	
							icon.addEventListener(Event.COMPLETE, loadcomplete);
							icon.setData((data[i*3+j] as RoleVo).className);
							tempmc.addChild(myrole2);
						}					
					}
					else
					{
						kongitem = new konggird();
						kongitem.y += j * 91;
						tempmc.addChild(kongitem);
					}						
				}
			
				
			}
		    
			//setTimeout(showview, 500);
				
		}		
		
		private function loadcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadcomplete);
			iconnum++
			if (iconnum == iconallnum)
			{
				iconnum = 0;
				
				setTimeout(showview, 500);
			}
		}
		
		private function showview():void 
		{
			var rect1:Rectangle = mc.getBounds(mc);
			
			alllength = rect1.height;
			
			var bit:BitmapData = new BitmapData(rect1.width, rect1.height, false, 0x000000);
			
			bit.draw(mc);
			raceBmp= new Bitmap(bit);
			
			sampleSprite = new Sprite();
            raceBmp.scrollRect = new Rectangle(0, 0, showW, showH);
			matrix = new Matrix();
            sampleSprite.graphics.beginBitmapFill (raceBmp.bitmapData, matrix , true , true);
            sampleSprite.graphics.drawRect(0, 0, showW, showH);
            sampleSprite.graphics.endFill();
			
			addChild(sampleSprite);
			
			
	        //test
			//sampleSprite.graphics.clear();
			//matrix.translate( 0,184);
			//sampleSprite.graphics.beginBitmapFill (raceBmp.bitmapData, matrix , true , true);
			//sampleSprite.graphics.drawRect(0, 0, showW, showH);
			//sampleSprite.graphics.endFill ();		
			
		}
		
		public function readgo():void
		{
			mc.addEventListener(Event.ENTER_FRAME, entenframe);	
		}
		
		//终点
		public function start(_selectId:int):void
		{
			selectId = _selectId;
			
			//mc.addEventListener(Event.ENTER_FRAME, entenframe);	
			
			
			//setTimeout(startslow, 4000);
			
			//计算最后的终点是哪里
			var selectnum:int = getDataLength(selectId);
			if (selectnum == 0)
			{
			    selectlength = 91;
			}
			else if(selectnum == 1)
			{
                selectlength = 0; 				
			}
			else
			{
				selectlength = selectnum * 91;
			}
			

		}
		
		public function startslow():void 
		{
			
			if (last)
			{
				startslowdown = true;
				mc.removeEventListener(Event.ENTER_FRAME, entenframe);
				timer.addEventListener(TimerEvent.TIMER, slowrun);
				timer.start();				
			}
			else
			{
				setTimeout(delayslow, 500);
			}

		}
		
		private function delayslow():void 
		{
			startslowdown = true;
			mc.removeEventListener(Event.ENTER_FRAME, entenframe);
			timer.addEventListener(TimerEvent.TIMER, slowrun);
			timer.start();				
		}
		
		private function slowrun(e:TimerEvent):void 
		{
			runNum += speed;
			
			if (runNum > alllength )
			{
				runNum = 0;
				matrix = new Matrix();
			}

			if (startslowdown)
			{
				//var point:Point = new Point(this.x, this.y);
				//point = this.localToGlobal(point);
				//trace(point)
				if (last)
				{
					var abscha:int = Math.abs(selectlength-runNum);
				
					if (abscha == 0)
					{
						speed = 0;
						timer.stop();
						timer.removeEventListener(TimerEvent.TIMER, slowrun);
						complete = true;
						startslowdown = false;
						EventManager.getInstance().dispatchEvent(new TigerMachineEvent(TigerMachineEvent.TIGERCOMPLETE));
					//结束
					}
					else if (abscha <10)
					{
						speed = 1;
						//timer.delay = 100;
					//mc.removeEventListener(Event.ENTER_FRAME, entenframe);	
					}					
					else if(abscha <20)
					{
						speed = 2;
						//timer.delay = 90;
					}
					else if (abscha < 40)
					{
						speed = 4;
						//timer.delay = 80;
					}
					else if (abscha < 60)
					{
						speed = 6;
						//timer.delay = 70;
					}								
					else if(abscha < 100)
					{
						speed = 8;
						//timer.delay = 60;
					}					
				}
				else
				{
					var abscha2:int = Math.abs(selectlength - runNum);
					if (selectlength < runNum)
					{
						speed = -abscha2;						
					}
					else
					{
						speed = abscha2;
					}

					timer.stop();
					timer.removeEventListener(TimerEvent.TIMER, slowrun);
					complete = true;
					startslowdown = false;
					EventManager.getInstance().dispatchEvent(new TigerMachineEvent(TigerMachineEvent.TIGERCOMPLETE));
				}

			}	
			
			sampleSprite.graphics.clear();
			matrix.translate( 0,speed);
			sampleSprite.graphics.beginBitmapFill (raceBmp.bitmapData, matrix , true , true);
			sampleSprite.graphics.drawRect(0, 0, showW, showH);
			sampleSprite.graphics.endFill ();			
			
		}
		
		//滚动的过程
		private function entenframe(e:Event):void 
		{
			
			runNum += speed;
			
			if (runNum > alllength )
			{
				runNum = 0;
				matrix = new Matrix();
			}
			

						
			sampleSprite.graphics.clear();
			matrix.translate( 0,speed);
			sampleSprite.graphics.beginBitmapFill (raceBmp.bitmapData, matrix , true , true);
			sampleSprite.graphics.drawRect(0, 0, showW, showH);
			sampleSprite.graphics.endFill ();			
		}		
		
		//取这个头像在数组里的第几位
		private function getDataLength(_id:int):int
		{
			for (var i:int = 0; i < data.length; i++) 
			{
				if ((data[i] as RoleVo).id == _id)
				{
					return i;
				}
			}
			return 0;
		}
		
	}

}