package  
{
	import happymagic.manager.RoleWorkManager;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.RoleWorkMapVo;
	import happymagic.model.vo.RoleWorkPointClassVo;
	import happymagic.model.vo.RoleWorkPointVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class test 
	{
		
		public function test() 
		{
			var vo2:RoleWorkMapVo = new RoleWorkMapVo();
			var arr2:Array = new Array();
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic = vo2;
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass = arr2;
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.bg = "http://devstaticalchemy.happyfish001.com/renren/alchemy/image/worldmap/bg.jpg";
			
			var vo:RoleWorkPointClassVo;
			var cvo:ConditionVo;
			vo = new RoleWorkPointClassVo();
			vo.id = 1;
			vo.iconClass = "mapicon1";
			vo.name = "望着小镇";
			vo.x = 70;
			vo.y = -3;
			vo.roleLevel = 2;
			vo.roleNum = 2;
			vo.needTime = 5400;
			
			vo.awards = new Array();
			cvo = new ConditionVo();
			cvo.type = 2;
			cvo.id = "coin";
			cvo.num = 0;
			vo.awards.push(cvo);
			
			cvo = new ConditionVo();
			cvo.type = 1;
			cvo.id = "131";
			cvo.num = 0;
			vo.awards.push(cvo);
			
			cvo = new ConditionVo();
			cvo.type = 1;
			cvo.id = "131";
			cvo.num = 0;
			vo.awards.push(cvo);
			
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass.push(vo);
			
			vo = new RoleWorkPointClassVo();
			vo.id = 2;
			vo.iconClass = "mapicon2";
			vo.x = -40;
			vo.y = -78;
			vo.needTime = 5400;
			vo.name = "小镇";
			vo.roleLevel = 2;
			vo.roleNum = 2;
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass.push(vo);	
			
			vo = new RoleWorkPointClassVo();
			vo.id = 3;
			vo.iconClass = "mapicon3";
			vo.name = "望镇";
			vo.x = -108;
			vo.y = 87;
			vo.roleLevel = 2;
			vo.roleNum = 2;
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass.push(vo);				
			
			vo = new RoleWorkPointClassVo();
			vo.id = 4;
			vo.iconClass = "mapicon4";
			vo.x = 175;
			vo.y = -83;
			vo.roleLevel = 2;
			vo.roleNum = 2;
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass.push(vo);				
			
			vo = new RoleWorkPointClassVo();
			vo.id = 5;
			vo.iconClass = "mapicon5";
			vo.x = -222;
			vo.y = -195;
			vo.roleLevel = 2;
			vo.roleNum = 2;
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic.pointClass.push(vo);				
			
			
			
			
			
			
			var arr:Array = new Array();
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataInit = arr;
			
			var vo1:RoleWorkPointVo;
			vo1 = new RoleWorkPointVo();
			vo1.id = 1;
			vo1.state = 2;
			vo1.roleId = new Array();
			vo1.roleId.push(0);
			vo1.roleId.push(100);
			vo1.time = 1338881846;
			vo1.awards = new Array();
			
			cvo = new ConditionVo();
			cvo.type = 2;
			cvo.id = "coin";
			cvo.num = 0;
			vo1.awards.push(cvo);
			
			cvo = new ConditionVo();
			cvo.type = 1;
			cvo.id = "131";
			cvo.num = 0;
			vo1.awards.push(cvo);
			
			cvo = new ConditionVo();
			cvo.type = 1;
			cvo.id = "131";
			cvo.num = 0;
			vo1.awards.push(cvo);			
			
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataInit.push(vo1);	
			
			vo1 = new RoleWorkPointVo();
			vo1.id = 2;
			vo1.state = 2;
			vo1.roleId = new Array();
			vo1.roleId.push(0);
			vo1.roleId.push(100);
			vo1.time = 1338881846;		
			
			vo1.awards = new Array();
			
			cvo = new ConditionVo();
			cvo.type = 2;
			cvo.id = "coin";
			cvo.num = 0;
			vo1.awards.push(cvo);
			
			cvo = new ConditionVo();
			cvo.type = 1;
			cvo.id = "131";
			cvo.num = 0;
			vo1.awards.push(cvo);
			
			cvo = new ConditionVo();
			cvo.type = 1;
			cvo.id = "131";
			cvo.num = 0;
			vo1.awards.push(cvo);				
			
			
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataInit.push(vo1);	
			
			//vo1 = new RoleWorkPointVo();
			//vo1.id = 3;
			//vo1.state = 1;			
			//RoleWorkManager.getInstance().roleWorkData.roleWorkDataInit.push(vo1);
		}
		
	}

}