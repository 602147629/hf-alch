package happyfish.display.ui.accordion 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.EventDispatcher;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import happyfish.display.ui.defaultList.DefaultListView;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class AccordionChild extends EventDispatcher 
	{
		
		/** Child在Accordion的索引, 由Accordion修改 */
		public var index:int;
		public var listField:String;
		public var titleField:String;
		
		protected var _selected:Boolean;
		protected var titleHeight:int;
		protected var titleWidth:int;
		protected var width:int;
		protected var height:int;
		protected var data:Object;
		
		public var view:Sprite;
		protected var listView:DefaultListView;
		public function getListView():DefaultListView { return listView; }
		
		public function AccordionChild(view:Sprite)
		{
			this.view = view;
			listView = createListView(view, view);
			getTitleBar().mouseChildren = false;
		}
		
		public function getTitleHeight():int { return titleHeight; }
		public function setTitleHeight(value:int):void
		{
			titleHeight = value;
		}
		
		public function getTitleWidth():int { return titleWidth; }
		public function setTitleWidth(value:int):void
		{
			titleWidth = value;
		}
		
		public function getTitleBar():MovieClip { return view["titleBar"] }
		
		public function setData(o:Object):void
		{
			this.data = o;
			showTitle();
			listView.setData(data[listField] || []);
		}
		
		public function showTitle():void 
		{
			if (!data) return;
			if (("titleTxt" in view) && (view["titleTxt"] is TextField))
			{
				view["titleTxt"].text = data[titleField] || "";
			}
		}
		
		public function clear():void
		{
			data = null;
		}
		
		public function resize(w:int, h:int):void
		{
			width = w;
			height = h;
		}
		
		public function get selected():Boolean { return _selected; }
		
		public function set selected(value:Boolean):void 
		{
			_selected = value;
			getTitleBar().gotoAndStop(value ? "Select" : "Unselect");
			
			getTitleBar().buttonMode = !value;
			if (value)
			{
				view.scrollRect = new Rectangle(0, 0, width, height);
				resizeBg(width, height);
			}else
			{
				view.scrollRect = new Rectangle(0, 0, titleWidth, titleHeight);
				resizeBg(titleWidth, titleHeight);
			}
		}
		
		protected function resizeBg(w:int, h:int):void 
		{
			var bg:DisplayObject = "bg" in view ? view["bg"] as DisplayObject : null;
			if (!bg) return;
			bg.width = w;
			bg.height = h;
		}
		
		
		protected function createListView(view:Sprite, container:DisplayObjectContainer):DefaultListView
		{
			throw new Error ("必须被覆盖!");
			return null;
		}
	}

}