package happymagic.order.flow 
{
	import com.friendsofed.isometric.Point3D;
	import com.greensock.data.TweenMaxVars;
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.geom.Point;
	import happyfish.display.view.UISprite;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.McUtil;
	import happymagic.display.view.RightCenterMenuView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ResultVo;
	import happymagic.order.view.SatisfactionBar;
	import happymagic.order.view.ui.OrderAcceptMovie;
	import happymagic.order.vo.ModuleType;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardItemView;
	import happymagic.scene.world.award.AwardType;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderAcceptMovieFlow 
	{
		private var flyFrames:int;
		private var alphaFrames:int;
		private var movie:MovieClip;
		
		private var flyBeginCallback:Function;
		
		
		public function OrderAcceptMovieFlow() 
		{
			
		}
		
		public function showAt(x:int, y:int, flyBeginCallback:Function):void 
		{
			this.flyBeginCallback = flyBeginCallback;
			movie = new OrderAcceptMovie();
			McUtil.addLabelScript(movie, "FlyBegin", flyBegin, "AlphaBegin", alphaBegin, "End", stopMovie);
			movie.x = x;
			movie.y = y;
			DisplayManager.uiSprite.stage.addChild(movie);
		}
		
		private function flyBegin():void
		{
			
			var ui:DisplayObject = RightCenterMenuView(ModuleManager.getInstance().getModule("rightCenterMenu")).getMc("order");
			var p:Point = ui.localToGlobal(new Point(0, 0));
			p = movie.globalToLocal(p);
			var duration:int = McUtil.getDistance(movie, "FlyBegin", "End");
			TweenMax.to(movie.getChildByName("icon"), duration, { useFrames:true, x:p.x, y:p.y } );
			if (flyBeginCallback != null) flyBeginCallback();
		}
		
		private function alphaBegin():void
		{
			var duration:int = McUtil.getDistance(movie, "AlphaBegin", "End");
			TweenMax.to(movie.getChildByName("icon"), duration, { useFrames:true, alpha:0 } );
		}
		
		private function stopMovie():void
		{
			if (!movie) return;
			
			if (movie.parent) movie.parent.removeChild(movie);
			McUtil.removeLabelScript(movie, "FlyBegin", "AlphaBegin", "End");
			movie.stop();
			movie = null;
		}
	}

}