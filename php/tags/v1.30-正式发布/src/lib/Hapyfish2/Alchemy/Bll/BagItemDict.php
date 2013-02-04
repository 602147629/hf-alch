<?php

class Hapyfish2_Alchemy_Bll_BagItemDict
{

    public static function getItemCntByCid($uid, $cid)
	{
        $type =	substr($cid, -2, 1);
        $retNum = 0;
        $data = null;

		//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
		switch ($type) {
            case 1:
                $data = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
                break;
            case 2:
                $data = Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
                break;
            case 3:
                $data = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
                break;
            case 4:

                break;
            case 5:
                $data = Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
                break;
            case 6:
                $data = Hapyfish2_Alchemy_HFC_Weapon::getNewWeapon($uid);
                break;
            default:
		}

	    if ($data) {
            if (array_key_exists($cid, $data)) {
                $retNum = (int)$data[$cid]['count'];
            }
        }

		return $retNum;
	}

	public static function sendItemByCid($uid, $cid, $num)
	{
        $type =	substr($cid, -2, 1);

		//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
		switch ($type) {
            case 1:
                Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $cid, $num);
                break;
            case 2:
                Hapyfish2_Alchemy_HFC_Scroll::addUserScroll($uid, $cid, $num);
                break;
            case 3:
                Hapyfish2_Alchemy_HFC_Stuff::addUserStuff($uid, $cid, $num);
                break;
            case 4:

                break;
            case 5:
                Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, $num);
                break;
            case 6:
                Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $cid, $num);
                break;
            default:
		}

		return true;
	}

    public static function consumeItemByCid($uid, $cid, $num)
	{
        $type =	substr($cid, -2, 1);

		//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
		switch ($type) {
            case 1:
                Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, $num);
                break;
            case 2:
                Hapyfish2_Alchemy_HFC_Scroll::useUserScroll($uid, $cid, $num);
                break;
            case 3:
                Hapyfish2_Alchemy_HFC_Stuff::useUserStuff($uid, $cid, $num);
                break;
            case 4:

                break;
            case 5:
                Hapyfish2_Alchemy_HFC_Decor::useBag($uid, $cid, $num);
                break;
            case 6:
                Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $cid, $num);
                break;
            default:
		}

		return true;
	}
}