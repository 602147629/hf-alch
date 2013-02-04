package happymagic.battle.model 
{
	import happymagic.battle.view.battlefield.Role;
	import happymagic.battle.view.ui.CustomNumber;
	import happymagic.model.vo.AnimationVo;
	import happymagic.model.vo.EffectDictionary;
	import happymagic.model.vo.EffectVo;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class Skills 
	{
		//近攻普通攻击VO----------------------------------------------------------
		private static var _closeAttackVo:SkillAndItemVo;
		public static const CLOSEATTACK_CID:int = 1;
		
		public static function get closeAttackVo():SkillAndItemVo
		{
			if (!_closeAttackVo)
			{
				_closeAttackVo = new SkillAndItemVo;
				_closeAttackVo.cid = CLOSEATTACK_CID;
				_closeAttackVo.name = "近身攻击";
				_closeAttackVo.effectList = new Vector.<EffectVo>();
				
				var effectVo:EffectVo = new EffectVo;
				effectVo.type = EffectDictionary.CHANGE_HP;
				effectVo.isPhysic = true;
				effectVo.isAbs = false;
				effectVo.value = -1;
				
				_closeAttackVo.effectList.push(effectVo);
				_closeAttackVo.target = SkillAndItemVo.ENEMY;
				_closeAttackVo.range = SkillAndItemVo.CLOSE;
				_closeAttackVo.area = SkillAndItemVo.SINGLE;
				_closeAttackVo.dodgeAccept = _closeAttackVo.critAccept = 100;
				
				//脚本
				_closeAttackVo.displayScript = new Array;
				
				var animationVo:AnimationVo;
				var animationGroup:Array;
				
				//移动到目标
				animationVo = new AnimationVo;
				animationVo.setData([2]);
				_closeAttackVo.displayScript.push(animationVo);
				
				animationGroup = new Array;
				
				//砍
				animationVo = new AnimationVo;
				animationVo.setData([1, 1, Role.ATTACK]);
				animationGroup.push(animationVo);
				
				//砍的音效
				animationVo = new AnimationVo;
				animationVo.setData([11, "battleSound.1.CloseAttack"]);
				animationGroup.push(animationVo);
				
				_closeAttackVo.displayScript.push(animationGroup);
				
				animationGroup = new Array;
				
				//目标挨打
				animationVo = new AnimationVo;
				animationVo.setData([AnimationVo.IGNORE_WHEN_MISS, 1, 2, Role.HITTED]);
				animationGroup.push(animationVo);
				
				//挨打音效
				animationVo = new AnimationVo;
				animationVo.setData([AnimationVo.IGNORE_WHEN_MISS, 11, "battleSound.1.Hitted"]);
				animationGroup.push(animationVo);
				
				//目标躲闪
				animationVo = new AnimationVo;
				animationVo.setData([AnimationVo.MISS, 1, 2, Role.DODGE]);
				animationGroup.push(animationVo);
				
				//飘字
				animationVo = new AnimationVo;
				animationVo.setData([8, 2, CustomNumber.RED]);
				animationGroup.push(animationVo);
				
				_closeAttackVo.displayScript.push(animationGroup);
				
				//跳回
				animationVo = new AnimationVo;
				animationVo.setData([3]);
				_closeAttackVo.displayScript.push(animationVo);
			}
			
			return _closeAttackVo;
		}
		
		//远程普通攻击VO------------------------------------------------------
		private static var _farAttackVo:SkillAndItemVo;
		public static const FARATTACK_CID:int = 2;
		
		public static function get farAttackVo():SkillAndItemVo
		{
			if (!_farAttackVo)
			{
				_farAttackVo = new SkillAndItemVo;
				_farAttackVo.cid = FARATTACK_CID;
				_farAttackVo.name = "远程攻击";
				_farAttackVo.effectList = new Vector.<EffectVo>();
				
				var effectVo:EffectVo = new EffectVo;
				effectVo.type = EffectDictionary.CHANGE_HP;
				effectVo.isPhysic = true;
				effectVo.isAbs = false;
				effectVo.value = -1;
				
				_farAttackVo.effectList.push(effectVo);
				_farAttackVo.target = SkillAndItemVo.ENEMY;
				_farAttackVo.range = SkillAndItemVo.FAR;
				_farAttackVo.area = SkillAndItemVo.SINGLE;
				_farAttackVo.dodgeAccept = _farAttackVo.critAccept = 100;
				
				//脚本------------------------------------------
				_farAttackVo.displayScript = new Array;
				
				var animationVo:AnimationVo;
				var animationGroup:Array;
				
				animationGroup = new Array;
				
				//射
				animationVo = new AnimationVo;
				animationVo.setData([1, 1, Role.ATTACK]);
				animationGroup.push(animationVo);
				
				//射击音效
				animationVo = new AnimationVo;
				animationVo.setData([11, "battleSound.1.FarAttack"]);
				animationGroup.push(animationVo);
				
				_farAttackVo.displayScript.push(animationGroup);
				
				animationGroup = new Array;
				
				//目标挨打
				animationVo = new AnimationVo;
				animationVo.setData([AnimationVo.IGNORE_WHEN_MISS, 1, 2, Role.HITTED]);
				animationGroup.push(animationVo);
				
				//挨打音效
				animationVo = new AnimationVo;
				animationVo.setData([AnimationVo.IGNORE_WHEN_MISS, 11, "battleSound.1.Hitted"]);
				animationGroup.push(animationVo);
				
				//目标躲闪
				animationVo = new AnimationVo;
				animationVo.setData([AnimationVo.MISS, 1, 2, Role.DODGE]);
				animationGroup.push(animationVo);
				
				//飘字
				animationVo = new AnimationVo;
				animationVo.setData([8, 2, CustomNumber.RED]);
				animationGroup.push(animationVo);
				
				_farAttackVo.displayScript.push(animationGroup);
			}
			return _farAttackVo;
		}
		
		//防御的VO数据------------------------------------------------------------------------
		private static var _defenceVo:SkillAndItemVo;
		public static const DEFENCE_CID:int = 3;
		
		public static function get defenseVo():SkillAndItemVo
		{
			if (!_defenceVo)
			{
				var animationVo:AnimationVo = new AnimationVo;
				animationVo.setData([1, 1, Role.DEFEND]);
				
				var effectVo:EffectVo =  new EffectVo();
				effectVo.type = EffectDictionary.ADD_CHANGE_PHYSICDEFENCE_STATUS;
				effectVo.isAbs = false;
				effectVo.isPhysic = true;
				effectVo.value = 1;
				effectVo.duration = 0;
				effectVo.statusProp = 1;
				
				_defenceVo = new SkillAndItemVo;
				_defenceVo.cid = DEFENCE_CID;
				_defenceVo.name = "防御";
				_defenceVo.target = SkillAndItemVo.SELF;
				_defenceVo.range = SkillAndItemVo.FAR;
				_defenceVo.area = SkillAndItemVo.SINGLE;
				_defenceVo.displayScript = [animationVo];
				_defenceVo.effectList = new Vector.<EffectVo>();
				_defenceVo.effectList.push(effectVo);
			}
			
			return _defenceVo;
		}
		
		//测试技能------------------------------------------------------------------------------
		private static var _testSkill:SkillAndItemVo;
		public static function get testSkill():SkillAndItemVo
		{
			if (!_testSkill)
			{
				var dispscript6:AnimationVo = new AnimationVo;
				dispscript6.setData([1, 1, Role.SKILL]); //聚气
				var dispscript5:AnimationVo = new AnimationVo;
				dispscript5.setData([2,1]); //移动
				var dispscript1:AnimationVo = new AnimationVo();
				dispscript1.setData([1, 1, "qianglizhan"]); //攻击
				var dispscript2:AnimationVo = new AnimationVo();
				dispscript2.setData([4, 2, "magichit.1.firebolt"]); //火球
				var dispscript3:AnimationVo = new AnimationVo();
				dispscript3.setData([8, 2, CustomNumber.RED]); //飘字
				var dispscript4:AnimationVo = new AnimationVo();
				dispscript4.setData([1, 2, Role.HITTED]); //目标挨打
				var dispscript8:AnimationVo = new AnimationVo;
				dispscript8.setData([9, 2, 203]); //目标变绿
				var dispscript7:AnimationVo = new AnimationVo();
				dispscript7.setData([3]); //跳回
				
				var effect:EffectVo = new EffectVo().setData( 
					{type:2, statusProp:0, value: -10, isAbs:1, duration:0, dodgeAccept:0, critAccept:0, isPhysic:1} ) as EffectVo;
				var effect2:EffectVo = new EffectVo().setData( 
					{type:4, statusProp:2, value: -5, isAbs:1, duration:1, dodgeAccept:0, critAccept:0, isPhysic:1, statusScript:[[22, 2, 203]] } ) as EffectVo;
				
				_testSkill = new SkillAndItemVo();
				_testSkill.cid = 222;
				_testSkill.name = "技能1";
				_testSkill.range = SkillAndItemVo.FAR;
				_testSkill.target = SkillAndItemVo.ENEMY;
				_testSkill.area = SkillAndItemVo.CROSS;
				_testSkill.needMp = 5;
				_testSkill.displayScript = [dispscript6,dispscript1,dispscript2,[dispscript3,dispscript4,dispscript8]];
				_testSkill.effectList = new Vector.<EffectVo>();
				_testSkill.effectList.push(effect);
				_testSkill.effectList.push(effect2);
			}
			return _testSkill;
		}
		
		//测试技能2 加血--------------------------------------------------------------------------------
		private static var _testSkill2:SkillAndItemVo;
		public static function get testSkill2():SkillAndItemVo
		{
			if (!_testSkill2)
			{
				var dispscript6:AnimationVo = new AnimationVo;
				dispscript6.setData([1, 1, Role.CASTMAGIC]);
				var dispscript2:AnimationVo = new AnimationVo();
				dispscript2.setData([4, 2, "magichit.1.musichit"]); //音乐
				var dispscript3:AnimationVo = new AnimationVo();
				dispscript3.setData([8, 2, CustomNumber.GREEN]); //飘字
				
				var effect:EffectVo = new EffectVo().setData( 
					{ cid:11,type:EffectDictionary.CHANGE_HP,value:10 } ) as EffectVo;
				
				_testSkill2 = new SkillAndItemVo();
				_testSkill2.cid = 112;
				_testSkill2.name = "加血";
				_testSkill2.range = SkillAndItemVo.FAR;
				_testSkill2.target = SkillAndItemVo.FRIEND;
				_testSkill2.area = SkillAndItemVo.SINGLE;
				_testSkill2.needMp = 1;
				_testSkill2.displayScript = [dispscript6,dispscript2,dispscript3];
				_testSkill2.effectList = new Vector.<EffectVo>();
				_testSkill2.effectList.push(effect);
			}
			return _testSkill2;
		}
		
	}

}