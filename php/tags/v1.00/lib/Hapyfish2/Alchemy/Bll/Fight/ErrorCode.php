<?

class Hapyfish2_Alchemy_Bll_Fight_ErrorCode
{

  public static $describition = array(
      -8001           => 'home_pos_not_allow',
      -8002           => 'enemy_pos_not_allow',
      -8003           => 'act sequence error',
      -8004           => 'unit_list_not_set',
      -8005           => '援助攻击只能由主角发起',

      -8101           => '协议数据位数不正',
      -8102           => '协议攻击方站位不正',
      -8103           => '协议被攻方站位不正',
      -8104           => '协议攻击型 类型 不正',
      -8105           => '协议治疗型 类型 不正',
      -8106           => '协议治疗型 技能/道具ID 不正',
      -8107           => '协议没有该操作位符号',

      -8201           => '首回合行动者 不正',
      -8202           => '目标方已死',
      -8203           => '技能/道具cid没找到',
      -8204           => '技能没有装备',
      -8205           => 'mp不足',
      -8206           => '道具不足',
      -8207           => '援助攻击次数不足/每次战斗只能援助一次',

      -8301           => 'fight info not found',
      -8302           => 'fight id not match',
      -8303           => 'fight info status error',
      -8304           => 'fight proc too long',
      -8305           => 'fight sides data prepare error',

      -8401           => 'simulate failed',
      -8402           => 'fight result not match'

  );
}