package happymagic.order.view 
{
	import com.greensock.plugins.AutoAlphaPlugin;
	import com.greensock.plugins.TweenPlugin;
	import com.greensock.TweenLite;
	import com.greensock.TweenMax;
	import flash.geom.Point;
	import happyfish.display.ui.EnergyBarView;
	import happyfish.display.view.PerBarView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.display.control.PiaoMsgControl;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.UserVo;
	import happymagic.order.view.ui.Face1;
	import happymagic.order.view.ui.SatisfactionBarUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class SatisfactionBar
	{
		//private var bar:PerBarView;
		private var barW:Number;
		private var iview:SatisfactionBarUI;
		
		private var tipsRateBySatisfaction:int;
		
		public function SatisfactionBar(iview:SatisfactionBarUI) 
		{
			this.iview = iview;
			iview.flashMc.visible = false;
			
			iview.mouseChildren = false;
			iview.mouseEnabled = false;
			
			TweenPlugin.activate([AutoAlphaPlugin]);
			
			//bar = new PerBarView(iview, iview.bar.width, 100);
			barW = iview.bar.width;
			tipsRateBySatisfaction = DataManager.getInstance().gameSetting.tipsRateBySatisfaction;
			EventManager.addEventListener(DataManagerEvent.USERINFO_CHANGE, changeHandler);
			changeHandler(null);
		}
		
		private function changeHandler(e:DataManagerEvent):void 
		{
			var vo:UserVo = DataManager.getInstance().currentUser;
			
			if (e != null)
			{
				var change:int = vo.satisfaction - parseInt(iview.txt.text);
				if (change != 0)
				{
					if (change > 0)
					{
						showPiao(change);
						return;
					}
					var color:uint = change > 0 ? 0xFFFFFF : 0xFF0000;
					
					TweenMax.killTweensOf(iview.flashMc, true);
					TweenMax.to(iview.flashMc, .2, { tint:color, autoAlpha:1 } );
					TweenMax.to(iview.flashMc, .2, { tint:null, delay:.2, autoAlpha:0 } );
				}
			}
			
			iview.txt.text = vo.satisfaction+"";
			iview.bar.width = vo.satisfaction / 100 * barW;
			var frame:int = vo.satisfaction / 25;
			if (frame < 4) frame++;
			iview.face.gotoAndStop(frame);
			iview.tipsRateTxt.text = int(vo.satisfaction / 10) * tipsRateBySatisfaction + "%";
		}
		
		private function showPiao(change:int):void 
		{
			PiaoMsgControl.getInstance().setPointByType("satisfaction", getSatisfactionPoint);
			var px:int = DisplayManager.uiSprite.stage.mouseX;
			var py:int = DisplayManager.uiSprite.stage.mouseY;
			var msgs:Array = [[PiaoMsgType.TYPE_OTHER,change, "satisfaction", new Face1(), null, flashValue]];
			var event:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs,px,py);
			EventManager.getInstance().dispatchEvent(event);
		}
		
		private function getSatisfactionPoint():Point 
		{
			return iview.txt.localToGlobal(new Point(iview.txt.x, iview.txt.y));
		}
		
		private function flashValue(type:String, value:int):void 
		{
			var color:uint = value > 0 ? 0xFFFFFF : 0xFF0000;
			TweenMax.killTweensOf(iview.flashMc, true);
			TweenMax.to(iview.flashMc, .2, { tint:color, autoAlpha:1 } );
			TweenMax.to(iview.flashMc, .2, { tint:null, delay:.2, autoAlpha:0 } );
			var vo:UserVo = DataManager.getInstance().currentUser;
			iview.txt.text = vo.satisfaction+"";
			iview.bar.width = vo.satisfaction / 100 * barW;
			var frame:int = vo.satisfaction / 25;
			if (frame < 4) frame++;
			iview.face.gotoAndStop(frame);
			iview.tipsRateTxt.text = int(vo.satisfaction / 10) * tipsRateBySatisfaction + "%";
		}
		
	}

}