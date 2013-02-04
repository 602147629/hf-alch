package happymagic.scene.world.control 
{
	import com.greensock.OverwriteManager;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.MouseCursorAction;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	
	/**
	 * ...
	 * @author slam
	 */
	public class MouseMagicAction extends MouseCursorAction
	{
		protected var skipBackgroundClick:Boolean;
		
		public function MouseMagicAction($state:WorldState, $stack_flg:Boolean = false) 
		{
			super($state, $stack_flg);
		}
		
		public function onAwardItemOver(event:GameMouseEvent):void {
			return;
		}
		
		public function onAwardItemOut(event:GameMouseEvent):void {
			return;
		}
		
		public function onAwardItemClick(event:GameMouseEvent):void {
			return;
		}
		
		public function onRoomUpItemClick(event:GameMouseEvent):void {
			
		}
		
		public function onRoomUpItemOver(event:GameMouseEvent):void {
			return;
		}
		
		public function onRoomUpItemOut(event:GameMouseEvent):void {
			return;
		}
		
		public function onEnemyClick(event:GameMouseEvent):void {
			return;
		}
		
		public function onEnemyOver(event:GameMouseEvent):void {
			return;
		}
		
		public function onEnemyOut(event:GameMouseEvent):void {
			return;
		}

        public function onBackgroundClick(event:GameMouseEvent) : void
        {
			return;
        }

        public function onDecorOver(event:GameMouseEvent) : void
        {
            return;
        }

        public function onDecorClick(event:GameMouseEvent) : void
        {
            return;
        }

        public function onDecorOut(event:GameMouseEvent) : void
        {
            return;
        }
		
        public function onStudentOver(event:GameMouseEvent) : void
        {
            return;
        }

        public function onStudentClick(event:GameMouseEvent) : void
        {
            return;
        }

        public function onStudentOut(event:GameMouseEvent) : void
        {
            return;
        }
		
        public function onDeskOver(event:GameMouseEvent) : void
        {
            return;
        }

        public function onDeskClick(event:GameMouseEvent) : void
        {
            return;
        }

        public function onDeskOut(event:GameMouseEvent) : void
        {
            return;
        }
		
        public function onDoorClick(event:GameMouseEvent) : void
        {
            return;
        }
		
        public function onWallDecorOver(event:GameMouseEvent) : void
        {
            return;
        }

        public function onWallDecorClick(event:GameMouseEvent) : void
        {
            return;
        }

        public function onWallDecorOut(event:GameMouseEvent) : void
        {
            return;
        }
		
		public function onPlayerClick(event:GameMouseEvent):void {
			return ;
		}
		
		public function onPlayerOver(event:GameMouseEvent):void {
			return;
		}
		
		public function onPlayerOut(event:GameMouseEvent):void {
			return;
		}
		
		public function onNpcOver(event:GameMouseEvent):void {
			return;
		}
		public function onNpcOut(event:GameMouseEvent):void {
			return;
		}
		public function onNpcClick(event:GameMouseEvent):void {
			return;
		}
		
		public function onMassesClick(event:GameMouseEvent):void {
			return;
		}
		
		public function onPortalOver(event:GameMouseEvent):void {
			
		}
		
		public function onPortalOut(event:GameMouseEvent):void {
			
		}
		
		public function onPortalClick(event:GameMouseEvent):void {
			
		}
	}

}