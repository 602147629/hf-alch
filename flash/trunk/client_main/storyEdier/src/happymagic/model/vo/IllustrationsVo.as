package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class IllustrationsVo extends BasicVo 
	{
		
		public var cid:int;
		public var isNew:Boolean;
		
		public var base:IllustrationsClassVo;
		// 基类的引用,弱类型
		public function get _base():* { return base; }
	}

}