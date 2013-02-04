<?php

class Hapyfish2_Alchemy_Bll_FightMercenary
{
    public static function getId($uid)
    {
        $dal = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
        return $dal->getId($uid, 'mercenary');
    }


    public static function addMercenary($uid, $cid, $name='')
    {
        $newId = self::getId($uid);
        $lev = 1;
        $exp = 0;
        //$basInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelInfo($cid, $lev);
        $basInfo = array();

        $info = array(
            'uid' => $uid,
            'mid' => $newId,
            'name' => $name,
            'cid' => $cid,
            'rp' => rand(1,20),
            'element' => 1,
            'exp' => $exp,
            'level' => $lev,
            'hp' => $basInfo['hp'],
            'hp_max' => $basInfo['hp'],
            'mp' => $basInfo['mp'],
            'mp_max' => $basInfo['mp'],
            'phy_att' => $basInfo['phy_att'],
            'phy_def' => $basInfo['phy_def'],
            'mag_att' => $basInfo['mag_att'],
            'mag_def' => $basInfo['mag_def'],
            'agility' => $basInfo['agility'],
            'crit' => $basInfo['crit'],
            'dodge' => $basInfo['dodge'],
            'weapon' => array(),
            'skill' => array()
        );

        Hapyfish2_Alchemy_HFC_FightMercenary::addOne($uid, $info);
        return $newId;
    }

    //取本方主角，佣兵列表
    public static function getAllRolesList($uid)
    {
        //站位分布
        $posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);

        $homeSideInfo = array();
        //self fight info
        $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        //mecenarycorps fight info
        $mecenaryList = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);

        //basic job list
        //$basJob = Hapyfish2_Alchemy_Cache_Basic::getMercenaryList();
        //array $data(id,matrix_pos,job,level,hp,hp_max,mp,mp_max,phy_att,phy_def,mag_att,mag_def,agility,crit,dodge,weapon,skill)
        $posList = array();
        $posArray = array();
        foreach ($posMatrix as $pos=>$id) {
            if ($id == 0) {
                $selfPos = $pos;
            }
            else {
                $posList[] = $id;
                $posArray[$id] = $pos;
            }
        }

        //主角
        $selfInfo['id'] = 0;
        $selfInfo['matrix_pos'] = (int)$selfPos;
        $homeSideInfo[] = $selfInfo;

        if ($mecenaryList) {
            //雇佣兵
            foreach ( $mecenaryList as $key=>$mecenary ) {
                $mecenaryInfo = $mecenary;

                if ( in_array($key, $posList) ) {
                    $mecenaryInfo['id'] = (int)$key;
                    $mecenaryInfo['matrix_pos'] = (int)$posArray[$key];
                }
                else {
                    $mecenaryInfo['id'] = (int)$key;
                    $mecenaryInfo['matrix_pos'] = -1;
                }

                $homeSideInfo[] = $mecenaryInfo;
            }
        }

        return $homeSideInfo;
    }



}