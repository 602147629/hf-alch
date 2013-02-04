package happymagic.model.vo 
{
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class EffectDictionary 
	{	
		public static const CHANGE_HP:int = 2; //伤害或加血
		public static const CHANGE_MP:int = 3; //烧蓝或加蓝
		
		public static const ADD_CHANGE_HP_STATUS:int = 4;	//增加影响HP状态
		public static const ADD_CHANGE_MP_STATUS:int = 5;	//增加影响MP状态
		
		public static const ADD_CHANGE_PHYSICATTACK_STATUS:int = 6;	//增加影响物攻状态
		public static const ADD_CHANGE_PHYSICDEFENCE_STATUS:int = 7;	//增加影响物防状态
		public static const ADD_CHANGE_MAGICATTACK_STATUS:int = 8;	//增加影响魔攻状态
		public static const ADD_CHANGE_MAGICDEFENCE_STATUS:int = 9;	//增加影响魔防状态
		
		public static const ADD_CHANGE_DODGE_STATUS:int = 10;		//增加影响闪躲状态
		public static const ADD_CHANGE_CRIT_STATUS:int = 11;		//增加影响暴击状态
		
		public static const ADD_PETRIFACTION_STATUS:int = 12;		//增加石化状态
		public static const ADD_SLEEP_STATUS:int = 13;				//增加睡眠状态
		public static const ADD_CONFUSION_STATUS:int = 14;			//增加混乱状态

		
		public static const REMOVE_ALL_STATUS:int = 15;			//消除所有状态
		public static const REMOVE_ALL_POSITIVE_STATUS:int = 16;	//消除所有正面状态
		public static const REMOVE_ALL_NEGATIVE_STATUS:int = 17;	//消除所有负面状态
		
		public static const REMOVE_CHANGE_HP_STATUS:int = 18;	//消除影响HP状态
		public static const REMOVE_CHANGE_MP_STATUS:int = 19;	//消除影响MP状态
		
		public static const REMOVE_CHANGE_PHYSICATTACK_STATUS:int = 20;	//消除影响物攻状态
		public static const REMOVE_CHANGE_PHYSICDEFENCE_STATUS:int = 21;	//消除影响物防状态
		public static const REMOVE_CHANGE_MAGICATTACK_STATUS:int = 22;	//消除影响魔攻状态
		public static const REMOVE_CHANGE_MAGICDEFENCE_STATUS:int = 23;	//消除影响魔防状态
		
		public static const REMOVE_CHANGE_DODGE_STATUS:int = 24;			//消除影响闪躲状态
		public static const REMOVE_CHANGE_CRIT_STATUS:int = 25;			//消除影响暴击状态
		
		public static const REMOVE_PETRIFACTION_STATUS:int = 26;		//消除石化状态
		public static const REMOVE_SLEEP_STATUS:int = 27;				//消除睡眠状态
		public static const REMOVE_CONFUSION_STATUS:int = 28;			//消除混乱状态
		
		public static const STEAL:int = 29; //偷窃
		
		public static const REVIVE:int = 30; //复活
		
		public static const ADD_SEAL_STATUS:int = 31; //封印
		
		public static const CHANGE_SP:int = 300;
		
		public static var strMap:Array = ["", "", "ChangeHp", "ChangeMp", "AddChangeHpStatus", "AddChangeMpStatus",
		"AddChangePhysicAttackStatus", "AddChangePhysicDefenceStatus", "AddChangeMagicAttackStatus", "AddChangeMagicDefenceStatus",
		"AddChangeDodgeStatus", "AddChangeCritStatus", "AddPetrifactionStatus", "AddSleepStatus", "AddConfusionStatus",
		"RemoveAllStatus", "RemoveAllPositiveStatus", "RemoveAllNegativeStatus", "RemoveChangeHpStatus", "RemoveChangeMpStatus",
		"RemoveChangePhysicAttackStatus", "RemoveChangePhysicDefenceStatus", "RemoveChangeMagicAttackStatus", "RemoveChangeMagicDefenceStatus",
		"RemoveChangeDodgeStatus", "RemoveChangeCritStatus", "RemovePetrifactionStatus", "RemoveSleepStatus", "RemoveConfusionStatus",
		"Steal","Revive","AddSealStatus"];
	}

}