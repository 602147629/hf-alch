package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkPointVo extends BasicVo 
	{
		public var id:int;
		public var time:int;
		public var roleIds:Array;
		public var state:int; //1 未开工 2 开工
		public var awards:Array;//随即
		
		public function RoleWorkPointVo() 
		{
			
		}
		
		public function setVaule(obj:Object):RoleWorkPointVo
		{
			setData(obj);
			if (awards)
			{
				var awardtmp:Array = new Array();
				for (var i:int = 0; i < awards.length; i++) 
				{
					var vo:ConditionVo = new ConditionVo();
					vo.id = awards[i][0];
					vo.type = awards[i][1];
					awardtmp.push(vo);
				}
			
			    awards = awardtmp;				
			}

			
			return this;
		}
		
	}

}