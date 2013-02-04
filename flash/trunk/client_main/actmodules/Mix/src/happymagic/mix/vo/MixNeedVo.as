package happymagic.mix.vo 
{
	import happymagic.model.vo.classVo.BaseItemClassVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class MixNeedVo 
	{
		
		public var need:int;
		public var has:int;
		public var vo:BaseItemClassVo;
		
		public function MixNeedVo(need:int, has:int, vo:BaseItemClassVo) 
		{
			this.need = need;
			this.has = has;
			this.vo = vo;
		}
		
		
		
	}

}