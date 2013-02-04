/*
SQLyog Community v8.51 
MySQL - 5.1.48-log : Database - kaixin_alchemy_0
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`kaixin_alchemy_0` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `kaixin_alchemy_0`;

/*Table structure for table `alchemy_user_activity` */

DROP TABLE IF EXISTS `alchemy_user_activity`;

CREATE TABLE `alchemy_user_activity` (
  `uid` int(10) NOT NULL,
  `activity` int(10) DEFAULT '0',
  `step` varchar(200) DEFAULT '[]',
  `update_time` int(10) DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_0` */

DROP TABLE IF EXISTS `alchemy_user_decor_0`;

CREATE TABLE `alchemy_user_decor_0` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_1` */

DROP TABLE IF EXISTS `alchemy_user_decor_1`;

CREATE TABLE `alchemy_user_decor_1` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_2` */

DROP TABLE IF EXISTS `alchemy_user_decor_2`;

CREATE TABLE `alchemy_user_decor_2` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_3` */

DROP TABLE IF EXISTS `alchemy_user_decor_3`;

CREATE TABLE `alchemy_user_decor_3` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_4` */

DROP TABLE IF EXISTS `alchemy_user_decor_4`;

CREATE TABLE `alchemy_user_decor_4` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_5` */

DROP TABLE IF EXISTS `alchemy_user_decor_5`;

CREATE TABLE `alchemy_user_decor_5` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_6` */

DROP TABLE IF EXISTS `alchemy_user_decor_6`;

CREATE TABLE `alchemy_user_decor_6` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_7` */

DROP TABLE IF EXISTS `alchemy_user_decor_7`;

CREATE TABLE `alchemy_user_decor_7` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_8` */

DROP TABLE IF EXISTS `alchemy_user_decor_8`;

CREATE TABLE `alchemy_user_decor_8` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_9` */

DROP TABLE IF EXISTS `alchemy_user_decor_9`;

CREATE TABLE `alchemy_user_decor_9` (
  `uid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',
  `z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_0` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_0`;

CREATE TABLE `alchemy_user_decor_inbag_0` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_1` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_1`;

CREATE TABLE `alchemy_user_decor_inbag_1` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_10` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_10`;

CREATE TABLE `alchemy_user_decor_inbag_10` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_11` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_11`;

CREATE TABLE `alchemy_user_decor_inbag_11` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_12` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_12`;

CREATE TABLE `alchemy_user_decor_inbag_12` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_13` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_13`;

CREATE TABLE `alchemy_user_decor_inbag_13` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_14` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_14`;

CREATE TABLE `alchemy_user_decor_inbag_14` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_15` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_15`;

CREATE TABLE `alchemy_user_decor_inbag_15` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_16` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_16`;

CREATE TABLE `alchemy_user_decor_inbag_16` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_17` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_17`;

CREATE TABLE `alchemy_user_decor_inbag_17` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_18` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_18`;

CREATE TABLE `alchemy_user_decor_inbag_18` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_19` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_19`;

CREATE TABLE `alchemy_user_decor_inbag_19` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_2` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_2`;

CREATE TABLE `alchemy_user_decor_inbag_2` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_20` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_20`;

CREATE TABLE `alchemy_user_decor_inbag_20` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_21` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_21`;

CREATE TABLE `alchemy_user_decor_inbag_21` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_22` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_22`;

CREATE TABLE `alchemy_user_decor_inbag_22` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_23` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_23`;

CREATE TABLE `alchemy_user_decor_inbag_23` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_24` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_24`;

CREATE TABLE `alchemy_user_decor_inbag_24` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_25` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_25`;

CREATE TABLE `alchemy_user_decor_inbag_25` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_26` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_26`;

CREATE TABLE `alchemy_user_decor_inbag_26` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_27` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_27`;

CREATE TABLE `alchemy_user_decor_inbag_27` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_28` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_28`;

CREATE TABLE `alchemy_user_decor_inbag_28` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_29` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_29`;

CREATE TABLE `alchemy_user_decor_inbag_29` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_3` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_3`;

CREATE TABLE `alchemy_user_decor_inbag_3` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_30` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_30`;

CREATE TABLE `alchemy_user_decor_inbag_30` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_31` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_31`;

CREATE TABLE `alchemy_user_decor_inbag_31` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_32` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_32`;

CREATE TABLE `alchemy_user_decor_inbag_32` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_33` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_33`;

CREATE TABLE `alchemy_user_decor_inbag_33` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_34` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_34`;

CREATE TABLE `alchemy_user_decor_inbag_34` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_35` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_35`;

CREATE TABLE `alchemy_user_decor_inbag_35` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_36` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_36`;

CREATE TABLE `alchemy_user_decor_inbag_36` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_37` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_37`;

CREATE TABLE `alchemy_user_decor_inbag_37` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_38` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_38`;

CREATE TABLE `alchemy_user_decor_inbag_38` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_39` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_39`;

CREATE TABLE `alchemy_user_decor_inbag_39` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_4` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_4`;

CREATE TABLE `alchemy_user_decor_inbag_4` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_40` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_40`;

CREATE TABLE `alchemy_user_decor_inbag_40` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_41` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_41`;

CREATE TABLE `alchemy_user_decor_inbag_41` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_42` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_42`;

CREATE TABLE `alchemy_user_decor_inbag_42` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_43` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_43`;

CREATE TABLE `alchemy_user_decor_inbag_43` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_44` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_44`;

CREATE TABLE `alchemy_user_decor_inbag_44` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_45` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_45`;

CREATE TABLE `alchemy_user_decor_inbag_45` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_46` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_46`;

CREATE TABLE `alchemy_user_decor_inbag_46` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_47` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_47`;

CREATE TABLE `alchemy_user_decor_inbag_47` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_48` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_48`;

CREATE TABLE `alchemy_user_decor_inbag_48` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_49` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_49`;

CREATE TABLE `alchemy_user_decor_inbag_49` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_5` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_5`;

CREATE TABLE `alchemy_user_decor_inbag_5` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_6` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_6`;

CREATE TABLE `alchemy_user_decor_inbag_6` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_7` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_7`;

CREATE TABLE `alchemy_user_decor_inbag_7` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_8` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_8`;

CREATE TABLE `alchemy_user_decor_inbag_8` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_decor_inbag_9` */

DROP TABLE IF EXISTS `alchemy_user_decor_inbag_9`;

CREATE TABLE `alchemy_user_decor_inbag_9` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_event_gift` */

DROP TABLE IF EXISTS `alchemy_user_event_gift`;

CREATE TABLE `alchemy_user_event_gift` (
  `uid` int(10) DEFAULT NULL,
  `id` int(10) DEFAULT NULL,
  `type` int(10) DEFAULT NULL,
  KEY `uid` (`uid`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_0` */

DROP TABLE IF EXISTS `alchemy_user_fight_0`;

CREATE TABLE `alchemy_user_fight_0` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援 7-1v1竞技场 8-对白触发战斗 9-新手引导战斗',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_1` */

DROP TABLE IF EXISTS `alchemy_user_fight_1`;

CREATE TABLE `alchemy_user_fight_1` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_2` */

DROP TABLE IF EXISTS `alchemy_user_fight_2`;

CREATE TABLE `alchemy_user_fight_2` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_3` */

DROP TABLE IF EXISTS `alchemy_user_fight_3`;

CREATE TABLE `alchemy_user_fight_3` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_4` */

DROP TABLE IF EXISTS `alchemy_user_fight_4`;

CREATE TABLE `alchemy_user_fight_4` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_5` */

DROP TABLE IF EXISTS `alchemy_user_fight_5`;

CREATE TABLE `alchemy_user_fight_5` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_6` */

DROP TABLE IF EXISTS `alchemy_user_fight_6`;

CREATE TABLE `alchemy_user_fight_6` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_7` */

DROP TABLE IF EXISTS `alchemy_user_fight_7`;

CREATE TABLE `alchemy_user_fight_7` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_8` */

DROP TABLE IF EXISTS `alchemy_user_fight_8`;

CREATE TABLE `alchemy_user_fight_8` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_9` */

DROP TABLE IF EXISTS `alchemy_user_fight_9`;

CREATE TABLE `alchemy_user_fight_9` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',
  `enemy_id` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',
  `rnd_element` varchar(1000) NOT NULL DEFAULT '[]',
  `home_side` text NOT NULL,
  `enemy_side` text NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `new_monster` varchar(1000) DEFAULT NULL COMMENT '首杀怪物id列表',
  PRIMARY KEY (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_0` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_0`;

CREATE TABLE `alchemy_user_fight_attribute_0` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_1` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_1`;

CREATE TABLE `alchemy_user_fight_attribute_1` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_2` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_2`;

CREATE TABLE `alchemy_user_fight_attribute_2` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_3` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_3`;

CREATE TABLE `alchemy_user_fight_attribute_3` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_4` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_4`;

CREATE TABLE `alchemy_user_fight_attribute_4` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_5` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_5`;

CREATE TABLE `alchemy_user_fight_attribute_5` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_6` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_6`;

CREATE TABLE `alchemy_user_fight_attribute_6` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_7` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_7`;

CREATE TABLE `alchemy_user_fight_attribute_7` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_8` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_8`;

CREATE TABLE `alchemy_user_fight_attribute_8` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_attribute_9` */

DROP TABLE IF EXISTS `alchemy_user_fight_attribute_9`;

CREATE TABLE `alchemy_user_fight_attribute_9` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(3) unsigned NOT NULL,
  `job` tinyint(3) unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `class_name` varchar(200) NOT NULL,
  `face_class_name` varchar(200) NOT NULL,
  `s_face_class_name` varchar(200) NOT NULL,
  `scene_player_class` varchar(200) NOT NULL,
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',
  `s_phy_def` int(10) DEFAULT '0',
  `s_mag_att` int(10) DEFAULT '0',
  `s_mag_def` int(10) DEFAULT '0',
  `s_agility` int(10) DEFAULT '0',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_0` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_0`;

CREATE TABLE `alchemy_user_fight_corps_0` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_1` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_1`;

CREATE TABLE `alchemy_user_fight_corps_1` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_2` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_2`;

CREATE TABLE `alchemy_user_fight_corps_2` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_3` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_3`;

CREATE TABLE `alchemy_user_fight_corps_3` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_4` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_4`;

CREATE TABLE `alchemy_user_fight_corps_4` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_5` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_5`;

CREATE TABLE `alchemy_user_fight_corps_5` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_6` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_6`;

CREATE TABLE `alchemy_user_fight_corps_6` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_7` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_7`;

CREATE TABLE `alchemy_user_fight_corps_7` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_8` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_8`;

CREATE TABLE `alchemy_user_fight_corps_8` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_corps_9` */

DROP TABLE IF EXISTS `alchemy_user_fight_corps_9`;

CREATE TABLE `alchemy_user_fight_corps_9` (
  `uid` int(10) unsigned NOT NULL,
  `matrix` varchar(200) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_0` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_0`;

CREATE TABLE `alchemy_user_fight_mercenary_0` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_1` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_1`;

CREATE TABLE `alchemy_user_fight_mercenary_1` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_2` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_2`;

CREATE TABLE `alchemy_user_fight_mercenary_2` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_3` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_3`;

CREATE TABLE `alchemy_user_fight_mercenary_3` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_4` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_4`;

CREATE TABLE `alchemy_user_fight_mercenary_4` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_5` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_5`;

CREATE TABLE `alchemy_user_fight_mercenary_5` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_6` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_6`;

CREATE TABLE `alchemy_user_fight_mercenary_6` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_7` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_7`;

CREATE TABLE `alchemy_user_fight_mercenary_7` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_8` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_8`;

CREATE TABLE `alchemy_user_fight_mercenary_8` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_fight_mercenary_9` */

DROP TABLE IF EXISTS `alchemy_user_fight_mercenary_9`;

CREATE TABLE `alchemy_user_fight_mercenary_9` (
  `uid` int(10) unsigned NOT NULL,
  `mid` int(10) unsigned NOT NULL COMMENT '实例id',
  `cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',
  `gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',
  `rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',
  `job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',
  `name` varchar(30) NOT NULL COMMENT '佣兵名字',
  `class_name` varchar(200) NOT NULL COMMENT '素材',
  `face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',
  `s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',
  `scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',
  `element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',
  `exp` int(10) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `hp` int(10) unsigned NOT NULL DEFAULT '1',
  `hp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `mp` int(10) unsigned NOT NULL DEFAULT '1',
  `mp_max` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_att` int(10) unsigned NOT NULL DEFAULT '1',
  `phy_def` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_att` int(10) unsigned NOT NULL DEFAULT '1',
  `mag_def` int(10) unsigned NOT NULL DEFAULT '1',
  `agility` int(10) unsigned NOT NULL DEFAULT '1',
  `crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',
  `dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',
  `weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',
  `skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',
  `s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `s_crit` int(10) DEFAULT '0',
  `s_dodge` int(10) DEFAULT '0' COMMENT '强化附加属性',
  `work_time` int(10) DEFAULT '0' COMMENT '[int] 打工剩余时间   时间戳',
  `work_max_time` int(10) DEFAULT '0' COMMENT '[int] 秒  打工总时间',
  PRIMARY KEY (`uid`,`mid`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_0` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_0`;

CREATE TABLE `alchemy_user_floorwall_0` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_1` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_1`;

CREATE TABLE `alchemy_user_floorwall_1` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_2` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_2`;

CREATE TABLE `alchemy_user_floorwall_2` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_3` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_3`;

CREATE TABLE `alchemy_user_floorwall_3` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_4` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_4`;

CREATE TABLE `alchemy_user_floorwall_4` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_5` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_5`;

CREATE TABLE `alchemy_user_floorwall_5` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_6` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_6`;

CREATE TABLE `alchemy_user_floorwall_6` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_7` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_7`;

CREATE TABLE `alchemy_user_floorwall_7` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_8` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_8`;

CREATE TABLE `alchemy_user_floorwall_8` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_floorwall_9` */

DROP TABLE IF EXISTS `alchemy_user_floorwall_9`;

CREATE TABLE `alchemy_user_floorwall_9` (
  `uid` int(10) unsigned NOT NULL,
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',
  `wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_0` */

DROP TABLE IF EXISTS `alchemy_user_furnace_0`;

CREATE TABLE `alchemy_user_furnace_0` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_1` */

DROP TABLE IF EXISTS `alchemy_user_furnace_1`;

CREATE TABLE `alchemy_user_furnace_1` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_2` */

DROP TABLE IF EXISTS `alchemy_user_furnace_2`;

CREATE TABLE `alchemy_user_furnace_2` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_3` */

DROP TABLE IF EXISTS `alchemy_user_furnace_3`;

CREATE TABLE `alchemy_user_furnace_3` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_4` */

DROP TABLE IF EXISTS `alchemy_user_furnace_4`;

CREATE TABLE `alchemy_user_furnace_4` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_5` */

DROP TABLE IF EXISTS `alchemy_user_furnace_5`;

CREATE TABLE `alchemy_user_furnace_5` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_6` */

DROP TABLE IF EXISTS `alchemy_user_furnace_6`;

CREATE TABLE `alchemy_user_furnace_6` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_7` */

DROP TABLE IF EXISTS `alchemy_user_furnace_7`;

CREATE TABLE `alchemy_user_furnace_7` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_8` */

DROP TABLE IF EXISTS `alchemy_user_furnace_8`;

CREATE TABLE `alchemy_user_furnace_8` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_furnace_9` */

DROP TABLE IF EXISTS `alchemy_user_furnace_9`;

CREATE TABLE `alchemy_user_furnace_9` (
  `id` int(10) unsigned NOT NULL COMMENT '工作台实例id',
  `uid` int(10) unsigned NOT NULL,
  `furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',
  `x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',
  `z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',
  `m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',
  `need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',
  `cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_0` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_0`;

CREATE TABLE `alchemy_user_gift_bag_0` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_1` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_1`;

CREATE TABLE `alchemy_user_gift_bag_1` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_2` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_2`;

CREATE TABLE `alchemy_user_gift_bag_2` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_3` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_3`;

CREATE TABLE `alchemy_user_gift_bag_3` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_4` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_4`;

CREATE TABLE `alchemy_user_gift_bag_4` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_5` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_5`;

CREATE TABLE `alchemy_user_gift_bag_5` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_6` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_6`;

CREATE TABLE `alchemy_user_gift_bag_6` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_7` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_7`;

CREATE TABLE `alchemy_user_gift_bag_7` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_8` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_8`;

CREATE TABLE `alchemy_user_gift_bag_8` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_bag_9` */

DROP TABLE IF EXISTS `alchemy_user_gift_bag_9`;

CREATE TABLE `alchemy_user_gift_bag_9` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL COMMENT '日期',
  `method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',
  `gid` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_0` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_0`;

CREATE TABLE `alchemy_user_gift_friend_wish_0` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_1` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_1`;

CREATE TABLE `alchemy_user_gift_friend_wish_1` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_2` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_2`;

CREATE TABLE `alchemy_user_gift_friend_wish_2` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_3` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_3`;

CREATE TABLE `alchemy_user_gift_friend_wish_3` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_4` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_4`;

CREATE TABLE `alchemy_user_gift_friend_wish_4` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_5` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_5`;

CREATE TABLE `alchemy_user_gift_friend_wish_5` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_6` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_6`;

CREATE TABLE `alchemy_user_gift_friend_wish_6` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_7` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_7`;

CREATE TABLE `alchemy_user_gift_friend_wish_7` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_8` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_8`;

CREATE TABLE `alchemy_user_gift_friend_wish_8` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_friend_wish_9` */

DROP TABLE IF EXISTS `alchemy_user_gift_friend_wish_9`;

CREATE TABLE `alchemy_user_gift_friend_wish_9` (
  `uid` int(10) unsigned NOT NULL,
  `from_uid` int(10) unsigned NOT NULL,
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`,`from_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_0` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_0`;

CREATE TABLE `alchemy_user_gift_wish_0` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_1` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_1`;

CREATE TABLE `alchemy_user_gift_wish_1` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_2` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_2`;

CREATE TABLE `alchemy_user_gift_wish_2` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_3` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_3`;

CREATE TABLE `alchemy_user_gift_wish_3` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_4` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_4`;

CREATE TABLE `alchemy_user_gift_wish_4` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_5` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_5`;

CREATE TABLE `alchemy_user_gift_wish_5` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_6` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_6`;

CREATE TABLE `alchemy_user_gift_wish_6` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_7` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_7`;

CREATE TABLE `alchemy_user_gift_wish_7` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_8` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_8`;

CREATE TABLE `alchemy_user_gift_wish_8` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_gift_wish_9` */

DROP TABLE IF EXISTS `alchemy_user_gift_wish_9`;

CREATE TABLE `alchemy_user_gift_wish_9` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(1000) NOT NULL DEFAULT '',
  `gid_1` int(10) unsigned NOT NULL,
  `gid_2` int(10) unsigned NOT NULL,
  `gid_3` int(10) unsigned NOT NULL,
  `gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',
  `gained_2` varchar(32) NOT NULL DEFAULT '',
  `gained_3` varchar(32) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_0` */

DROP TABLE IF EXISTS `alchemy_user_goods_0`;

CREATE TABLE `alchemy_user_goods_0` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_1` */

DROP TABLE IF EXISTS `alchemy_user_goods_1`;

CREATE TABLE `alchemy_user_goods_1` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_2` */

DROP TABLE IF EXISTS `alchemy_user_goods_2`;

CREATE TABLE `alchemy_user_goods_2` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_3` */

DROP TABLE IF EXISTS `alchemy_user_goods_3`;

CREATE TABLE `alchemy_user_goods_3` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_4` */

DROP TABLE IF EXISTS `alchemy_user_goods_4`;

CREATE TABLE `alchemy_user_goods_4` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_5` */

DROP TABLE IF EXISTS `alchemy_user_goods_5`;

CREATE TABLE `alchemy_user_goods_5` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_6` */

DROP TABLE IF EXISTS `alchemy_user_goods_6`;

CREATE TABLE `alchemy_user_goods_6` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_7` */

DROP TABLE IF EXISTS `alchemy_user_goods_7`;

CREATE TABLE `alchemy_user_goods_7` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_8` */

DROP TABLE IF EXISTS `alchemy_user_goods_8`;

CREATE TABLE `alchemy_user_goods_8` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_goods_9` */

DROP TABLE IF EXISTS `alchemy_user_goods_9`;

CREATE TABLE `alchemy_user_goods_9` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_0` */

DROP TABLE IF EXISTS `alchemy_user_help_0`;

CREATE TABLE `alchemy_user_help_0` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_1` */

DROP TABLE IF EXISTS `alchemy_user_help_1`;

CREATE TABLE `alchemy_user_help_1` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_2` */

DROP TABLE IF EXISTS `alchemy_user_help_2`;

CREATE TABLE `alchemy_user_help_2` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_3` */

DROP TABLE IF EXISTS `alchemy_user_help_3`;

CREATE TABLE `alchemy_user_help_3` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_4` */

DROP TABLE IF EXISTS `alchemy_user_help_4`;

CREATE TABLE `alchemy_user_help_4` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_5` */

DROP TABLE IF EXISTS `alchemy_user_help_5`;

CREATE TABLE `alchemy_user_help_5` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_6` */

DROP TABLE IF EXISTS `alchemy_user_help_6`;

CREATE TABLE `alchemy_user_help_6` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_7` */

DROP TABLE IF EXISTS `alchemy_user_help_7`;

CREATE TABLE `alchemy_user_help_7` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_8` */

DROP TABLE IF EXISTS `alchemy_user_help_8`;

CREATE TABLE `alchemy_user_help_8` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_help_9` */

DROP TABLE IF EXISTS `alchemy_user_help_9`;

CREATE TABLE `alchemy_user_help_9` (
  `uid` int(10) NOT NULL,
  `id` int(10) DEFAULT NULL COMMENT '当前引导id',
  `idx` int(10) DEFAULT NULL COMMENT '当前引导索引',
  `status` tinyint(4) DEFAULT NULL COMMENT '当前引导状态,1:进行中,0:已完成',
  `finish_ids` varchar(200) DEFAULT NULL COMMENT '已完成引导列表,1,2,3',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_0` */

DROP TABLE IF EXISTS `alchemy_user_hire_0`;

CREATE TABLE `alchemy_user_hire_0` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_1` */

DROP TABLE IF EXISTS `alchemy_user_hire_1`;

CREATE TABLE `alchemy_user_hire_1` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_2` */

DROP TABLE IF EXISTS `alchemy_user_hire_2`;

CREATE TABLE `alchemy_user_hire_2` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_3` */

DROP TABLE IF EXISTS `alchemy_user_hire_3`;

CREATE TABLE `alchemy_user_hire_3` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_4` */

DROP TABLE IF EXISTS `alchemy_user_hire_4`;

CREATE TABLE `alchemy_user_hire_4` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_5` */

DROP TABLE IF EXISTS `alchemy_user_hire_5`;

CREATE TABLE `alchemy_user_hire_5` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_6` */

DROP TABLE IF EXISTS `alchemy_user_hire_6`;

CREATE TABLE `alchemy_user_hire_6` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_7` */

DROP TABLE IF EXISTS `alchemy_user_hire_7`;

CREATE TABLE `alchemy_user_hire_7` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_8` */

DROP TABLE IF EXISTS `alchemy_user_hire_8`;

CREATE TABLE `alchemy_user_hire_8` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_hire_9` */

DROP TABLE IF EXISTS `alchemy_user_hire_9`;

CREATE TABLE `alchemy_user_hire_9` (
  `uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',
  `hire_1` varchar(2000) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',
  `hire_2` varchar(2000) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',
  `hire_3` varchar(2000) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',
  `hire_4` varchar(2000) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',
  `hire_5` varchar(2000) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',
  `hire_6` varchar(2000) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_0` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_0`;

CREATE TABLE `alchemy_user_illustrations_0` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_1` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_1`;

CREATE TABLE `alchemy_user_illustrations_1` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_2` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_2`;

CREATE TABLE `alchemy_user_illustrations_2` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_3` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_3`;

CREATE TABLE `alchemy_user_illustrations_3` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_4` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_4`;

CREATE TABLE `alchemy_user_illustrations_4` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_5` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_5`;

CREATE TABLE `alchemy_user_illustrations_5` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_6` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_6`;

CREATE TABLE `alchemy_user_illustrations_6` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_7` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_7`;

CREATE TABLE `alchemy_user_illustrations_7` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_8` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_8`;

CREATE TABLE `alchemy_user_illustrations_8` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_illustrations_9` */

DROP TABLE IF EXISTS `alchemy_user_illustrations_9`;

CREATE TABLE `alchemy_user_illustrations_9` (
  `uid` int(10) unsigned NOT NULL,
  `id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_0` */

DROP TABLE IF EXISTS `alchemy_user_info_0`;

CREATE TABLE `alchemy_user_info_0` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级,村庄的',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级,王城的',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_1` */

DROP TABLE IF EXISTS `alchemy_user_info_1`;

CREATE TABLE `alchemy_user_info_1` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_2` */

DROP TABLE IF EXISTS `alchemy_user_info_2`;

CREATE TABLE `alchemy_user_info_2` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_3` */

DROP TABLE IF EXISTS `alchemy_user_info_3`;

CREATE TABLE `alchemy_user_info_3` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_4` */

DROP TABLE IF EXISTS `alchemy_user_info_4`;

CREATE TABLE `alchemy_user_info_4` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_5` */

DROP TABLE IF EXISTS `alchemy_user_info_5`;

CREATE TABLE `alchemy_user_info_5` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_6` */

DROP TABLE IF EXISTS `alchemy_user_info_6`;

CREATE TABLE `alchemy_user_info_6` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_7` */

DROP TABLE IF EXISTS `alchemy_user_info_7`;

CREATE TABLE `alchemy_user_info_7` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_8` */

DROP TABLE IF EXISTS `alchemy_user_info_8`;

CREATE TABLE `alchemy_user_info_8` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_info_9` */

DROP TABLE IF EXISTS `alchemy_user_info_9`;

CREATE TABLE `alchemy_user_info_9` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',
  `gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',
  `feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',
  `sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',
  `max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',
  `sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',
  `exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',
  `home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',
  `tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，村庄',
  `tavern_city_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级，王城',
  `smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',
  `order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',
  `mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',
  `satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',
  `tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',
  `open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',
  `cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',
  `isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `all_login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',
  `assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',
  `assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_0` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_0`;

CREATE TABLE `alchemy_user_invitelog_0` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_1` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_1`;

CREATE TABLE `alchemy_user_invitelog_1` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_2` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_2`;

CREATE TABLE `alchemy_user_invitelog_2` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_3` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_3`;

CREATE TABLE `alchemy_user_invitelog_3` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_4` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_4`;

CREATE TABLE `alchemy_user_invitelog_4` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_5` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_5`;

CREATE TABLE `alchemy_user_invitelog_5` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_6` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_6`;

CREATE TABLE `alchemy_user_invitelog_6` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_7` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_7`;

CREATE TABLE `alchemy_user_invitelog_7` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_8` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_8`;

CREATE TABLE `alchemy_user_invitelog_8` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_invitelog_9` */

DROP TABLE IF EXISTS `alchemy_user_invitelog_9`;

CREATE TABLE `alchemy_user_invitelog_9` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_0` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_0`;

CREATE TABLE `alchemy_user_log_add_gem_0` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_1` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_1`;

CREATE TABLE `alchemy_user_log_add_gem_1` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_10` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_10`;

CREATE TABLE `alchemy_user_log_add_gem_10` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_11` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_11`;

CREATE TABLE `alchemy_user_log_add_gem_11` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_12` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_12`;

CREATE TABLE `alchemy_user_log_add_gem_12` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_13` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_13`;

CREATE TABLE `alchemy_user_log_add_gem_13` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_14` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_14`;

CREATE TABLE `alchemy_user_log_add_gem_14` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_15` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_15`;

CREATE TABLE `alchemy_user_log_add_gem_15` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_16` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_16`;

CREATE TABLE `alchemy_user_log_add_gem_16` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_17` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_17`;

CREATE TABLE `alchemy_user_log_add_gem_17` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_18` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_18`;

CREATE TABLE `alchemy_user_log_add_gem_18` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_19` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_19`;

CREATE TABLE `alchemy_user_log_add_gem_19` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_2` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_2`;

CREATE TABLE `alchemy_user_log_add_gem_2` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_20` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_20`;

CREATE TABLE `alchemy_user_log_add_gem_20` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_21` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_21`;

CREATE TABLE `alchemy_user_log_add_gem_21` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_22` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_22`;

CREATE TABLE `alchemy_user_log_add_gem_22` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_23` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_23`;

CREATE TABLE `alchemy_user_log_add_gem_23` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_24` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_24`;

CREATE TABLE `alchemy_user_log_add_gem_24` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_25` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_25`;

CREATE TABLE `alchemy_user_log_add_gem_25` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_26` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_26`;

CREATE TABLE `alchemy_user_log_add_gem_26` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_27` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_27`;

CREATE TABLE `alchemy_user_log_add_gem_27` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_28` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_28`;

CREATE TABLE `alchemy_user_log_add_gem_28` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_29` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_29`;

CREATE TABLE `alchemy_user_log_add_gem_29` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_3` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_3`;

CREATE TABLE `alchemy_user_log_add_gem_3` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_30` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_30`;

CREATE TABLE `alchemy_user_log_add_gem_30` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_31` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_31`;

CREATE TABLE `alchemy_user_log_add_gem_31` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_32` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_32`;

CREATE TABLE `alchemy_user_log_add_gem_32` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_33` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_33`;

CREATE TABLE `alchemy_user_log_add_gem_33` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_34` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_34`;

CREATE TABLE `alchemy_user_log_add_gem_34` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_35` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_35`;

CREATE TABLE `alchemy_user_log_add_gem_35` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_36` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_36`;

CREATE TABLE `alchemy_user_log_add_gem_36` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_37` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_37`;

CREATE TABLE `alchemy_user_log_add_gem_37` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_38` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_38`;

CREATE TABLE `alchemy_user_log_add_gem_38` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_39` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_39`;

CREATE TABLE `alchemy_user_log_add_gem_39` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_4` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_4`;

CREATE TABLE `alchemy_user_log_add_gem_4` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_40` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_40`;

CREATE TABLE `alchemy_user_log_add_gem_40` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_41` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_41`;

CREATE TABLE `alchemy_user_log_add_gem_41` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_42` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_42`;

CREATE TABLE `alchemy_user_log_add_gem_42` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_43` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_43`;

CREATE TABLE `alchemy_user_log_add_gem_43` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_44` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_44`;

CREATE TABLE `alchemy_user_log_add_gem_44` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_45` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_45`;

CREATE TABLE `alchemy_user_log_add_gem_45` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_46` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_46`;

CREATE TABLE `alchemy_user_log_add_gem_46` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_47` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_47`;

CREATE TABLE `alchemy_user_log_add_gem_47` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_48` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_48`;

CREATE TABLE `alchemy_user_log_add_gem_48` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_49` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_49`;

CREATE TABLE `alchemy_user_log_add_gem_49` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_5` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_5`;

CREATE TABLE `alchemy_user_log_add_gem_5` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_6` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_6`;

CREATE TABLE `alchemy_user_log_add_gem_6` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_7` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_7`;

CREATE TABLE `alchemy_user_log_add_gem_7` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_8` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_8`;

CREATE TABLE `alchemy_user_log_add_gem_8` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_add_gem_9` */

DROP TABLE IF EXISTS `alchemy_user_log_add_gem_9`;

CREATE TABLE `alchemy_user_log_add_gem_9` (
  `uid` int(10) unsigned NOT NULL,
  `gold` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL,
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_0` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_0`;

CREATE TABLE `alchemy_user_log_consume_coin_0` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_1` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_1`;

CREATE TABLE `alchemy_user_log_consume_coin_1` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_2` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_2`;

CREATE TABLE `alchemy_user_log_consume_coin_2` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_3` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_3`;

CREATE TABLE `alchemy_user_log_consume_coin_3` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_4` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_4`;

CREATE TABLE `alchemy_user_log_consume_coin_4` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_5` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_5`;

CREATE TABLE `alchemy_user_log_consume_coin_5` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_6` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_6`;

CREATE TABLE `alchemy_user_log_consume_coin_6` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_7` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_7`;

CREATE TABLE `alchemy_user_log_consume_coin_7` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_8` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_8`;

CREATE TABLE `alchemy_user_log_consume_coin_8` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_coin_9` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_coin_9`;

CREATE TABLE `alchemy_user_log_consume_coin_9` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_0` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_0`;

CREATE TABLE `alchemy_user_log_consume_gem_0` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_1` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_1`;

CREATE TABLE `alchemy_user_log_consume_gem_1` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_2` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_2`;

CREATE TABLE `alchemy_user_log_consume_gem_2` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_3` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_3`;

CREATE TABLE `alchemy_user_log_consume_gem_3` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_4` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_4`;

CREATE TABLE `alchemy_user_log_consume_gem_4` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_5` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_5`;

CREATE TABLE `alchemy_user_log_consume_gem_5` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_6` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_6`;

CREATE TABLE `alchemy_user_log_consume_gem_6` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_7` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_7`;

CREATE TABLE `alchemy_user_log_consume_gem_7` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_8` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_8`;

CREATE TABLE `alchemy_user_log_consume_gem_8` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_log_consume_gem_9` */

DROP TABLE IF EXISTS `alchemy_user_log_consume_gem_9`;

CREATE TABLE `alchemy_user_log_consume_gem_9` (
  `uid` int(10) unsigned NOT NULL,
  `cost` int(10) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL,
  `num` smallint(5) unsigned NOT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_0` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_0`;

CREATE TABLE `alchemy_user_map_copy_0` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '版本号，匹配静态版本号 重置副本 ',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_1` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_1`;

CREATE TABLE `alchemy_user_map_copy_1` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_2` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_2`;

CREATE TABLE `alchemy_user_map_copy_2` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_3` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_3`;

CREATE TABLE `alchemy_user_map_copy_3` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_4` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_4`;

CREATE TABLE `alchemy_user_map_copy_4` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_5` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_5`;

CREATE TABLE `alchemy_user_map_copy_5` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_6` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_6`;

CREATE TABLE `alchemy_user_map_copy_6` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_7` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_7`;

CREATE TABLE `alchemy_user_map_copy_7` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_8` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_8`;

CREATE TABLE `alchemy_user_map_copy_8` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_9` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_9`;

CREATE TABLE `alchemy_user_map_copy_9` (
  `uid` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_ver` int(10) unsigned NOT NULL DEFAULT '1',
  `enter_time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`uid`,`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_0` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_0`;

CREATE TABLE `alchemy_user_map_copy_person_0` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_1` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_1`;

CREATE TABLE `alchemy_user_map_copy_person_1` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_2` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_2`;

CREATE TABLE `alchemy_user_map_copy_person_2` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_3` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_3`;

CREATE TABLE `alchemy_user_map_copy_person_3` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_4` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_4`;

CREATE TABLE `alchemy_user_map_copy_person_4` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_5` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_5`;

CREATE TABLE `alchemy_user_map_copy_person_5` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_6` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_6`;

CREATE TABLE `alchemy_user_map_copy_person_6` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_7` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_7`;

CREATE TABLE `alchemy_user_map_copy_person_7` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_8` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_8`;

CREATE TABLE `alchemy_user_map_copy_person_8` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_person_9` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_person_9`;

CREATE TABLE `alchemy_user_map_copy_person_9` (
  `uid` int(10) NOT NULL,
  `add_person` varchar(2000) DEFAULT NULL COMMENT '添加npcid列表',
  `remove_person` varchar(2000) DEFAULT NULL COMMENT '隐藏npcid列表',
  `list` varchar(2000) DEFAULT NULL COMMENT '动态npc列表,npcId->1,显示;0,隐藏',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_0` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_0`;

CREATE TABLE `alchemy_user_map_copy_transport_0` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_1` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_1`;

CREATE TABLE `alchemy_user_map_copy_transport_1` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_2` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_2`;

CREATE TABLE `alchemy_user_map_copy_transport_2` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_3` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_3`;

CREATE TABLE `alchemy_user_map_copy_transport_3` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_4` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_4`;

CREATE TABLE `alchemy_user_map_copy_transport_4` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_5` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_5`;

CREATE TABLE `alchemy_user_map_copy_transport_5` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_6` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_6`;

CREATE TABLE `alchemy_user_map_copy_transport_6` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_7` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_7`;

CREATE TABLE `alchemy_user_map_copy_transport_7` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_8` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_8`;

CREATE TABLE `alchemy_user_map_copy_transport_8` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_map_copy_transport_9` */

DROP TABLE IF EXISTS `alchemy_user_map_copy_transport_9`;

CREATE TABLE `alchemy_user_map_copy_transport_9` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_0` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_0`;

CREATE TABLE `alchemy_user_mercenary_work_0` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_1` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_1`;

CREATE TABLE `alchemy_user_mercenary_work_1` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_2` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_2`;

CREATE TABLE `alchemy_user_mercenary_work_2` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_3` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_3`;

CREATE TABLE `alchemy_user_mercenary_work_3` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_4` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_4`;

CREATE TABLE `alchemy_user_mercenary_work_4` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_5` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_5`;

CREATE TABLE `alchemy_user_mercenary_work_5` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_6` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_6`;

CREATE TABLE `alchemy_user_mercenary_work_6` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_7` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_7`;

CREATE TABLE `alchemy_user_mercenary_work_7` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_8` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_8`;

CREATE TABLE `alchemy_user_mercenary_work_8` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mercenary_work_9` */

DROP TABLE IF EXISTS `alchemy_user_mercenary_work_9`;

CREATE TABLE `alchemy_user_mercenary_work_9` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '已解锁的打工点id',
  `finish_time` int(10) DEFAULT NULL COMMENT '打工完成时间',
  `role_ids` varchar(200) DEFAULT NULL COMMENT '参与佣兵id列表,1,2,3',
  `awards` varchar(200) DEFAULT NULL COMMENT '所有奖励',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态,1:未开工,2:开工中',
  PRIMARY KEY (`uid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_0` */

DROP TABLE IF EXISTS `alchemy_user_mix_0`;

CREATE TABLE `alchemy_user_mix_0` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_1` */

DROP TABLE IF EXISTS `alchemy_user_mix_1`;

CREATE TABLE `alchemy_user_mix_1` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_2` */

DROP TABLE IF EXISTS `alchemy_user_mix_2`;

CREATE TABLE `alchemy_user_mix_2` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_3` */

DROP TABLE IF EXISTS `alchemy_user_mix_3`;

CREATE TABLE `alchemy_user_mix_3` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_4` */

DROP TABLE IF EXISTS `alchemy_user_mix_4`;

CREATE TABLE `alchemy_user_mix_4` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_5` */

DROP TABLE IF EXISTS `alchemy_user_mix_5`;

CREATE TABLE `alchemy_user_mix_5` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_6` */

DROP TABLE IF EXISTS `alchemy_user_mix_6`;

CREATE TABLE `alchemy_user_mix_6` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_7` */

DROP TABLE IF EXISTS `alchemy_user_mix_7`;

CREATE TABLE `alchemy_user_mix_7` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_8` */

DROP TABLE IF EXISTS `alchemy_user_mix_8`;

CREATE TABLE `alchemy_user_mix_8` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_mix_9` */

DROP TABLE IF EXISTS `alchemy_user_mix_9`;

CREATE TABLE `alchemy_user_mix_9` (
  `uid` int(10) NOT NULL,
  `mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_0` */

DROP TABLE IF EXISTS `alchemy_user_monster_0`;

CREATE TABLE `alchemy_user_monster_0` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_1` */

DROP TABLE IF EXISTS `alchemy_user_monster_1`;

CREATE TABLE `alchemy_user_monster_1` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_2` */

DROP TABLE IF EXISTS `alchemy_user_monster_2`;

CREATE TABLE `alchemy_user_monster_2` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_3` */

DROP TABLE IF EXISTS `alchemy_user_monster_3`;

CREATE TABLE `alchemy_user_monster_3` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_4` */

DROP TABLE IF EXISTS `alchemy_user_monster_4`;

CREATE TABLE `alchemy_user_monster_4` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_5` */

DROP TABLE IF EXISTS `alchemy_user_monster_5`;

CREATE TABLE `alchemy_user_monster_5` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_6` */

DROP TABLE IF EXISTS `alchemy_user_monster_6`;

CREATE TABLE `alchemy_user_monster_6` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_7` */

DROP TABLE IF EXISTS `alchemy_user_monster_7`;

CREATE TABLE `alchemy_user_monster_7` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_8` */

DROP TABLE IF EXISTS `alchemy_user_monster_8`;

CREATE TABLE `alchemy_user_monster_8` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_monster_9` */

DROP TABLE IF EXISTS `alchemy_user_monster_9`;

CREATE TABLE `alchemy_user_monster_9` (
  `uid` int(10) NOT NULL,
  `monster` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_0` */

DROP TABLE IF EXISTS `alchemy_user_occupy_0`;

CREATE TABLE `alchemy_user_occupy_0` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_1` */

DROP TABLE IF EXISTS `alchemy_user_occupy_1`;

CREATE TABLE `alchemy_user_occupy_1` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_2` */

DROP TABLE IF EXISTS `alchemy_user_occupy_2`;

CREATE TABLE `alchemy_user_occupy_2` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_3` */

DROP TABLE IF EXISTS `alchemy_user_occupy_3`;

CREATE TABLE `alchemy_user_occupy_3` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_4` */

DROP TABLE IF EXISTS `alchemy_user_occupy_4`;

CREATE TABLE `alchemy_user_occupy_4` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_5` */

DROP TABLE IF EXISTS `alchemy_user_occupy_5`;

CREATE TABLE `alchemy_user_occupy_5` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_6` */

DROP TABLE IF EXISTS `alchemy_user_occupy_6`;

CREATE TABLE `alchemy_user_occupy_6` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_7` */

DROP TABLE IF EXISTS `alchemy_user_occupy_7`;

CREATE TABLE `alchemy_user_occupy_7` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_8` */

DROP TABLE IF EXISTS `alchemy_user_occupy_8`;

CREATE TABLE `alchemy_user_occupy_8` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_occupy_9` */

DROP TABLE IF EXISTS `alchemy_user_occupy_9`;

CREATE TABLE `alchemy_user_occupy_9` (
  `uid` int(10) unsigned NOT NULL,
  `corps_used` varchar(500) NOT NULL DEFAULT '[]',
  `passive` varchar(500) NOT NULL DEFAULT '[]',
  `initiative` varchar(3000) NOT NULL DEFAULT '[]',
  `last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_0` */

DROP TABLE IF EXISTS `alchemy_user_openmine_0`;

CREATE TABLE `alchemy_user_openmine_0` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_1` */

DROP TABLE IF EXISTS `alchemy_user_openmine_1`;

CREATE TABLE `alchemy_user_openmine_1` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_2` */

DROP TABLE IF EXISTS `alchemy_user_openmine_2`;

CREATE TABLE `alchemy_user_openmine_2` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_3` */

DROP TABLE IF EXISTS `alchemy_user_openmine_3`;

CREATE TABLE `alchemy_user_openmine_3` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_4` */

DROP TABLE IF EXISTS `alchemy_user_openmine_4`;

CREATE TABLE `alchemy_user_openmine_4` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_5` */

DROP TABLE IF EXISTS `alchemy_user_openmine_5`;

CREATE TABLE `alchemy_user_openmine_5` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_6` */

DROP TABLE IF EXISTS `alchemy_user_openmine_6`;

CREATE TABLE `alchemy_user_openmine_6` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_7` */

DROP TABLE IF EXISTS `alchemy_user_openmine_7`;

CREATE TABLE `alchemy_user_openmine_7` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_8` */

DROP TABLE IF EXISTS `alchemy_user_openmine_8`;

CREATE TABLE `alchemy_user_openmine_8` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openmine_9` */

DROP TABLE IF EXISTS `alchemy_user_openmine_9`;

CREATE TABLE `alchemy_user_openmine_9` (
  `uid` int(10) NOT NULL,
  `open_mine` varchar(2000) DEFAULT NULL COMMENT '已打开箱子id列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_0` */

DROP TABLE IF EXISTS `alchemy_user_openportal_0`;

CREATE TABLE `alchemy_user_openportal_0` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_1` */

DROP TABLE IF EXISTS `alchemy_user_openportal_1`;

CREATE TABLE `alchemy_user_openportal_1` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_2` */

DROP TABLE IF EXISTS `alchemy_user_openportal_2`;

CREATE TABLE `alchemy_user_openportal_2` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_3` */

DROP TABLE IF EXISTS `alchemy_user_openportal_3`;

CREATE TABLE `alchemy_user_openportal_3` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_4` */

DROP TABLE IF EXISTS `alchemy_user_openportal_4`;

CREATE TABLE `alchemy_user_openportal_4` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_5` */

DROP TABLE IF EXISTS `alchemy_user_openportal_5`;

CREATE TABLE `alchemy_user_openportal_5` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_6` */

DROP TABLE IF EXISTS `alchemy_user_openportal_6`;

CREATE TABLE `alchemy_user_openportal_6` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_7` */

DROP TABLE IF EXISTS `alchemy_user_openportal_7`;

CREATE TABLE `alchemy_user_openportal_7` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_8` */

DROP TABLE IF EXISTS `alchemy_user_openportal_8`;

CREATE TABLE `alchemy_user_openportal_8` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_openportal_9` */

DROP TABLE IF EXISTS `alchemy_user_openportal_9`;

CREATE TABLE `alchemy_user_openportal_9` (
  `uid` int(10) NOT NULL,
  `open_portal` varchar(200) DEFAULT NULL COMMENT '已打开门列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_0` */

DROP TABLE IF EXISTS `alchemy_user_order_0`;

CREATE TABLE `alchemy_user_order_0` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_1` */

DROP TABLE IF EXISTS `alchemy_user_order_1`;

CREATE TABLE `alchemy_user_order_1` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_2` */

DROP TABLE IF EXISTS `alchemy_user_order_2`;

CREATE TABLE `alchemy_user_order_2` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_3` */

DROP TABLE IF EXISTS `alchemy_user_order_3`;

CREATE TABLE `alchemy_user_order_3` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_4` */

DROP TABLE IF EXISTS `alchemy_user_order_4`;

CREATE TABLE `alchemy_user_order_4` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_5` */

DROP TABLE IF EXISTS `alchemy_user_order_5`;

CREATE TABLE `alchemy_user_order_5` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_6` */

DROP TABLE IF EXISTS `alchemy_user_order_6`;

CREATE TABLE `alchemy_user_order_6` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_7` */

DROP TABLE IF EXISTS `alchemy_user_order_7`;

CREATE TABLE `alchemy_user_order_7` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_8` */

DROP TABLE IF EXISTS `alchemy_user_order_8`;

CREATE TABLE `alchemy_user_order_8` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_order_9` */

DROP TABLE IF EXISTS `alchemy_user_order_9`;

CREATE TABLE `alchemy_user_order_9` (
  `uid` int(10) NOT NULL,
  `order` text COMMENT '订单信息',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_0` */

DROP TABLE IF EXISTS `alchemy_user_paylog_0`;

CREATE TABLE `alchemy_user_paylog_0` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_1` */

DROP TABLE IF EXISTS `alchemy_user_paylog_1`;

CREATE TABLE `alchemy_user_paylog_1` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_2` */

DROP TABLE IF EXISTS `alchemy_user_paylog_2`;

CREATE TABLE `alchemy_user_paylog_2` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_3` */

DROP TABLE IF EXISTS `alchemy_user_paylog_3`;

CREATE TABLE `alchemy_user_paylog_3` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_4` */

DROP TABLE IF EXISTS `alchemy_user_paylog_4`;

CREATE TABLE `alchemy_user_paylog_4` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_5` */

DROP TABLE IF EXISTS `alchemy_user_paylog_5`;

CREATE TABLE `alchemy_user_paylog_5` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_6` */

DROP TABLE IF EXISTS `alchemy_user_paylog_6`;

CREATE TABLE `alchemy_user_paylog_6` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_7` */

DROP TABLE IF EXISTS `alchemy_user_paylog_7`;

CREATE TABLE `alchemy_user_paylog_7` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_8` */

DROP TABLE IF EXISTS `alchemy_user_paylog_8`;

CREATE TABLE `alchemy_user_paylog_8` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_paylog_9` */

DROP TABLE IF EXISTS `alchemy_user_paylog_9`;

CREATE TABLE `alchemy_user_paylog_9` (
  `uid` int(10) unsigned NOT NULL,
  `orderid` varchar(32) NOT NULL,
  `pid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `summary` varchar(100) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_payorder` */

DROP TABLE IF EXISTS `alchemy_user_payorder`;

CREATE TABLE `alchemy_user_payorder` (
  `orderid` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `gold` int(10) NOT NULL DEFAULT '0',
  `token` varchar(32) NOT NULL DEFAULT '',
  `order_time` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `complete_time` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_0` */

DROP TABLE IF EXISTS `alchemy_user_scroll_0`;

CREATE TABLE `alchemy_user_scroll_0` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_1` */

DROP TABLE IF EXISTS `alchemy_user_scroll_1`;

CREATE TABLE `alchemy_user_scroll_1` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_2` */

DROP TABLE IF EXISTS `alchemy_user_scroll_2`;

CREATE TABLE `alchemy_user_scroll_2` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_3` */

DROP TABLE IF EXISTS `alchemy_user_scroll_3`;

CREATE TABLE `alchemy_user_scroll_3` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_4` */

DROP TABLE IF EXISTS `alchemy_user_scroll_4`;

CREATE TABLE `alchemy_user_scroll_4` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_5` */

DROP TABLE IF EXISTS `alchemy_user_scroll_5`;

CREATE TABLE `alchemy_user_scroll_5` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_6` */

DROP TABLE IF EXISTS `alchemy_user_scroll_6`;

CREATE TABLE `alchemy_user_scroll_6` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_7` */

DROP TABLE IF EXISTS `alchemy_user_scroll_7`;

CREATE TABLE `alchemy_user_scroll_7` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_8` */

DROP TABLE IF EXISTS `alchemy_user_scroll_8`;

CREATE TABLE `alchemy_user_scroll_8` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_scroll_9` */

DROP TABLE IF EXISTS `alchemy_user_scroll_9`;

CREATE TABLE `alchemy_user_scroll_9` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_0` */

DROP TABLE IF EXISTS `alchemy_user_seq_0`;

CREATE TABLE `alchemy_user_seq_0` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_1` */

DROP TABLE IF EXISTS `alchemy_user_seq_1`;

CREATE TABLE `alchemy_user_seq_1` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_2` */

DROP TABLE IF EXISTS `alchemy_user_seq_2`;

CREATE TABLE `alchemy_user_seq_2` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_3` */

DROP TABLE IF EXISTS `alchemy_user_seq_3`;

CREATE TABLE `alchemy_user_seq_3` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_4` */

DROP TABLE IF EXISTS `alchemy_user_seq_4`;

CREATE TABLE `alchemy_user_seq_4` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_5` */

DROP TABLE IF EXISTS `alchemy_user_seq_5`;

CREATE TABLE `alchemy_user_seq_5` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_6` */

DROP TABLE IF EXISTS `alchemy_user_seq_6`;

CREATE TABLE `alchemy_user_seq_6` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_7` */

DROP TABLE IF EXISTS `alchemy_user_seq_7`;

CREATE TABLE `alchemy_user_seq_7` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_8` */

DROP TABLE IF EXISTS `alchemy_user_seq_8`;

CREATE TABLE `alchemy_user_seq_8` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_seq_9` */

DROP TABLE IF EXISTS `alchemy_user_seq_9`;

CREATE TABLE `alchemy_user_seq_9` (
  `uid` int(10) unsigned NOT NULL,
  `name` char(1) NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_0` */

DROP TABLE IF EXISTS `alchemy_user_story_0`;

CREATE TABLE `alchemy_user_story_0` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_1` */

DROP TABLE IF EXISTS `alchemy_user_story_1`;

CREATE TABLE `alchemy_user_story_1` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_2` */

DROP TABLE IF EXISTS `alchemy_user_story_2`;

CREATE TABLE `alchemy_user_story_2` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_3` */

DROP TABLE IF EXISTS `alchemy_user_story_3`;

CREATE TABLE `alchemy_user_story_3` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_4` */

DROP TABLE IF EXISTS `alchemy_user_story_4`;

CREATE TABLE `alchemy_user_story_4` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_5` */

DROP TABLE IF EXISTS `alchemy_user_story_5`;

CREATE TABLE `alchemy_user_story_5` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_6` */

DROP TABLE IF EXISTS `alchemy_user_story_6`;

CREATE TABLE `alchemy_user_story_6` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_7` */

DROP TABLE IF EXISTS `alchemy_user_story_7`;

CREATE TABLE `alchemy_user_story_7` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_8` */

DROP TABLE IF EXISTS `alchemy_user_story_8`;

CREATE TABLE `alchemy_user_story_8` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_9` */

DROP TABLE IF EXISTS `alchemy_user_story_9`;

CREATE TABLE `alchemy_user_story_9` (
  `uid` int(11) NOT NULL,
  `list` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_0` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_0`;

CREATE TABLE `alchemy_user_story_dialog_0` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_1` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_1`;

CREATE TABLE `alchemy_user_story_dialog_1` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_2` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_2`;

CREATE TABLE `alchemy_user_story_dialog_2` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_3` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_3`;

CREATE TABLE `alchemy_user_story_dialog_3` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_4` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_4`;

CREATE TABLE `alchemy_user_story_dialog_4` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_5` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_5`;

CREATE TABLE `alchemy_user_story_dialog_5` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_6` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_6`;

CREATE TABLE `alchemy_user_story_dialog_6` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_7` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_7`;

CREATE TABLE `alchemy_user_story_dialog_7` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_8` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_8`;

CREATE TABLE `alchemy_user_story_dialog_8` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_story_dialog_9` */

DROP TABLE IF EXISTS `alchemy_user_story_dialog_9`;

CREATE TABLE `alchemy_user_story_dialog_9` (
  `uid` int(10) NOT NULL,
  `list` varchar(2000) DEFAULT '{"101":{"1":1},"103":{"6":1}}',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_0` */

DROP TABLE IF EXISTS `alchemy_user_stuff_0`;

CREATE TABLE `alchemy_user_stuff_0` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_1` */

DROP TABLE IF EXISTS `alchemy_user_stuff_1`;

CREATE TABLE `alchemy_user_stuff_1` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_2` */

DROP TABLE IF EXISTS `alchemy_user_stuff_2`;

CREATE TABLE `alchemy_user_stuff_2` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_3` */

DROP TABLE IF EXISTS `alchemy_user_stuff_3`;

CREATE TABLE `alchemy_user_stuff_3` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_4` */

DROP TABLE IF EXISTS `alchemy_user_stuff_4`;

CREATE TABLE `alchemy_user_stuff_4` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_5` */

DROP TABLE IF EXISTS `alchemy_user_stuff_5`;

CREATE TABLE `alchemy_user_stuff_5` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_6` */

DROP TABLE IF EXISTS `alchemy_user_stuff_6`;

CREATE TABLE `alchemy_user_stuff_6` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_7` */

DROP TABLE IF EXISTS `alchemy_user_stuff_7`;

CREATE TABLE `alchemy_user_stuff_7` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_8` */

DROP TABLE IF EXISTS `alchemy_user_stuff_8`;

CREATE TABLE `alchemy_user_stuff_8` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_stuff_9` */

DROP TABLE IF EXISTS `alchemy_user_stuff_9`;

CREATE TABLE `alchemy_user_stuff_9` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',
  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_0` */

DROP TABLE IF EXISTS `alchemy_user_task_0`;

CREATE TABLE `alchemy_user_task_0` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_1` */

DROP TABLE IF EXISTS `alchemy_user_task_1`;

CREATE TABLE `alchemy_user_task_1` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_2` */

DROP TABLE IF EXISTS `alchemy_user_task_2`;

CREATE TABLE `alchemy_user_task_2` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_3` */

DROP TABLE IF EXISTS `alchemy_user_task_3`;

CREATE TABLE `alchemy_user_task_3` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_4` */

DROP TABLE IF EXISTS `alchemy_user_task_4`;

CREATE TABLE `alchemy_user_task_4` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_5` */

DROP TABLE IF EXISTS `alchemy_user_task_5`;

CREATE TABLE `alchemy_user_task_5` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_6` */

DROP TABLE IF EXISTS `alchemy_user_task_6`;

CREATE TABLE `alchemy_user_task_6` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_7` */

DROP TABLE IF EXISTS `alchemy_user_task_7`;

CREATE TABLE `alchemy_user_task_7` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_8` */

DROP TABLE IF EXISTS `alchemy_user_task_8`;

CREATE TABLE `alchemy_user_task_8` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_9` */

DROP TABLE IF EXISTS `alchemy_user_task_9`;

CREATE TABLE `alchemy_user_task_9` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `finish_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_0` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_0`;

CREATE TABLE `alchemy_user_task_daily_0` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `finish` varchar(1000) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_1` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_1`;

CREATE TABLE `alchemy_user_task_daily_1` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_2` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_2`;

CREATE TABLE `alchemy_user_task_daily_2` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_3` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_3`;

CREATE TABLE `alchemy_user_task_daily_3` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_4` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_4`;

CREATE TABLE `alchemy_user_task_daily_4` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_5` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_5`;

CREATE TABLE `alchemy_user_task_daily_5` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_6` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_6`;

CREATE TABLE `alchemy_user_task_daily_6` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_7` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_7`;

CREATE TABLE `alchemy_user_task_daily_7` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_8` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_8`;

CREATE TABLE `alchemy_user_task_daily_8` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_daily_9` */

DROP TABLE IF EXISTS `alchemy_user_task_daily_9`;

CREATE TABLE `alchemy_user_task_daily_9` (
  `uid` int(10) NOT NULL,
  `list` varchar(255) NOT NULL DEFAULT '[]',
  `data` varchar(3000) NOT NULL DEFAULT '[]',
  `refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',
  `finish` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_0` */

DROP TABLE IF EXISTS `alchemy_user_task_open_0`;

CREATE TABLE `alchemy_user_task_open_0` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_1` */

DROP TABLE IF EXISTS `alchemy_user_task_open_1`;

CREATE TABLE `alchemy_user_task_open_1` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_2` */

DROP TABLE IF EXISTS `alchemy_user_task_open_2`;

CREATE TABLE `alchemy_user_task_open_2` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_3` */

DROP TABLE IF EXISTS `alchemy_user_task_open_3`;

CREATE TABLE `alchemy_user_task_open_3` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_4` */

DROP TABLE IF EXISTS `alchemy_user_task_open_4`;

CREATE TABLE `alchemy_user_task_open_4` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_5` */

DROP TABLE IF EXISTS `alchemy_user_task_open_5`;

CREATE TABLE `alchemy_user_task_open_5` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_6` */

DROP TABLE IF EXISTS `alchemy_user_task_open_6`;

CREATE TABLE `alchemy_user_task_open_6` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_7` */

DROP TABLE IF EXISTS `alchemy_user_task_open_7`;

CREATE TABLE `alchemy_user_task_open_7` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_8` */

DROP TABLE IF EXISTS `alchemy_user_task_open_8`;

CREATE TABLE `alchemy_user_task_open_8` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_task_open_9` */

DROP TABLE IF EXISTS `alchemy_user_task_open_9`;

CREATE TABLE `alchemy_user_task_open_9` (
  `uid` int(10) unsigned NOT NULL,
  `list` varchar(2000) NOT NULL DEFAULT '[]',
  `list2` varchar(2000) NOT NULL DEFAULT '[]',
  `data` varchar(6000) NOT NULL DEFAULT '[]',
  `buffer_list` varchar(1000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_0` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_0`;

CREATE TABLE `alchemy_user_unique_item_0` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_1` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_1`;

CREATE TABLE `alchemy_user_unique_item_1` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_2` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_2`;

CREATE TABLE `alchemy_user_unique_item_2` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_3` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_3`;

CREATE TABLE `alchemy_user_unique_item_3` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_4` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_4`;

CREATE TABLE `alchemy_user_unique_item_4` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_5` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_5`;

CREATE TABLE `alchemy_user_unique_item_5` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_6` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_6`;

CREATE TABLE `alchemy_user_unique_item_6` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_7` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_7`;

CREATE TABLE `alchemy_user_unique_item_7` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_8` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_8`;

CREATE TABLE `alchemy_user_unique_item_8` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unique_item_9` */

DROP TABLE IF EXISTS `alchemy_user_unique_item_9`;

CREATE TABLE `alchemy_user_unique_item_9` (
  `uid` int(10) unsigned NOT NULL,
  `item_ids` varchar(3000) NOT NULL DEFAULT '[]' COMMENT '已获得唯一物品列表',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_0` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_0`;

CREATE TABLE `alchemy_user_unlockfunc_0` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_1` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_1`;

CREATE TABLE `alchemy_user_unlockfunc_1` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_2` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_2`;

CREATE TABLE `alchemy_user_unlockfunc_2` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_3` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_3`;

CREATE TABLE `alchemy_user_unlockfunc_3` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_4` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_4`;

CREATE TABLE `alchemy_user_unlockfunc_4` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_5` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_5`;

CREATE TABLE `alchemy_user_unlockfunc_5` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_6` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_6`;

CREATE TABLE `alchemy_user_unlockfunc_6` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_7` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_7`;

CREATE TABLE `alchemy_user_unlockfunc_7` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_8` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_8`;

CREATE TABLE `alchemy_user_unlockfunc_8` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_unlockfunc_9` */

DROP TABLE IF EXISTS `alchemy_user_unlockfunc_9`;

CREATE TABLE `alchemy_user_unlockfunc_9` (
  `uid` int(10) NOT NULL,
  `func_list` varchar(200) DEFAULT NULL COMMENT '未解锁功能列表,1,2,3,4',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_0` */

DROP TABLE IF EXISTS `alchemy_user_weapon_0`;

CREATE TABLE `alchemy_user_weapon_0` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(20000) NOT NULL COMMENT '装备信息[[id,status,durability,pa,pd,ma,md,speed,hp,mp,cri,dod],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度,物攻，物防，魔攻，魔防，速度，hp加成，mp加成，暴击，闪避]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_1` */

DROP TABLE IF EXISTS `alchemy_user_weapon_1`;

CREATE TABLE `alchemy_user_weapon_1` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_2` */

DROP TABLE IF EXISTS `alchemy_user_weapon_2`;

CREATE TABLE `alchemy_user_weapon_2` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_3` */

DROP TABLE IF EXISTS `alchemy_user_weapon_3`;

CREATE TABLE `alchemy_user_weapon_3` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_4` */

DROP TABLE IF EXISTS `alchemy_user_weapon_4`;

CREATE TABLE `alchemy_user_weapon_4` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_5` */

DROP TABLE IF EXISTS `alchemy_user_weapon_5`;

CREATE TABLE `alchemy_user_weapon_5` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_6` */

DROP TABLE IF EXISTS `alchemy_user_weapon_6`;

CREATE TABLE `alchemy_user_weapon_6` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_7` */

DROP TABLE IF EXISTS `alchemy_user_weapon_7`;

CREATE TABLE `alchemy_user_weapon_7` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_8` */

DROP TABLE IF EXISTS `alchemy_user_weapon_8`;

CREATE TABLE `alchemy_user_weapon_8` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_weapon_9` */

DROP TABLE IF EXISTS `alchemy_user_weapon_9`;

CREATE TABLE `alchemy_user_weapon_9` (
  `uid` int(10) unsigned NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '装备cid',
  `count` int(10) unsigned NOT NULL COMMENT '拥有个数',
  `data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_0` */

DROP TABLE IF EXISTS `alchemy_user_world_map_0`;

CREATE TABLE `alchemy_user_world_map_0` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]' COMMENT '[世界地图id,地图是否开启0-未开启1-开启,世界地图状态0-新解锁1-已进入过2-已打通过]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_1` */

DROP TABLE IF EXISTS `alchemy_user_world_map_1`;

CREATE TABLE `alchemy_user_world_map_1` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_2` */

DROP TABLE IF EXISTS `alchemy_user_world_map_2`;

CREATE TABLE `alchemy_user_world_map_2` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_3` */

DROP TABLE IF EXISTS `alchemy_user_world_map_3`;

CREATE TABLE `alchemy_user_world_map_3` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_4` */

DROP TABLE IF EXISTS `alchemy_user_world_map_4`;

CREATE TABLE `alchemy_user_world_map_4` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_5` */

DROP TABLE IF EXISTS `alchemy_user_world_map_5`;

CREATE TABLE `alchemy_user_world_map_5` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_6` */

DROP TABLE IF EXISTS `alchemy_user_world_map_6`;

CREATE TABLE `alchemy_user_world_map_6` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_7` */

DROP TABLE IF EXISTS `alchemy_user_world_map_7`;

CREATE TABLE `alchemy_user_world_map_7` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_8` */

DROP TABLE IF EXISTS `alchemy_user_world_map_8`;

CREATE TABLE `alchemy_user_world_map_8` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `alchemy_user_world_map_9` */

DROP TABLE IF EXISTS `alchemy_user_world_map_9`;

CREATE TABLE `alchemy_user_world_map_9` (
  `uid` int(10) unsigned NOT NULL,
  `map_ids` varchar(2000) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_0` */

DROP TABLE IF EXISTS `platform_user_friend_0`;

CREATE TABLE `platform_user_friend_0` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_1` */

DROP TABLE IF EXISTS `platform_user_friend_1`;

CREATE TABLE `platform_user_friend_1` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_2` */

DROP TABLE IF EXISTS `platform_user_friend_2`;

CREATE TABLE `platform_user_friend_2` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_3` */

DROP TABLE IF EXISTS `platform_user_friend_3`;

CREATE TABLE `platform_user_friend_3` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_4` */

DROP TABLE IF EXISTS `platform_user_friend_4`;

CREATE TABLE `platform_user_friend_4` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_5` */

DROP TABLE IF EXISTS `platform_user_friend_5`;

CREATE TABLE `platform_user_friend_5` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_6` */

DROP TABLE IF EXISTS `platform_user_friend_6`;

CREATE TABLE `platform_user_friend_6` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_7` */

DROP TABLE IF EXISTS `platform_user_friend_7`;

CREATE TABLE `platform_user_friend_7` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_8` */

DROP TABLE IF EXISTS `platform_user_friend_8`;

CREATE TABLE `platform_user_friend_8` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_friend_9` */

DROP TABLE IF EXISTS `platform_user_friend_9`;

CREATE TABLE `platform_user_friend_9` (
  `uid` int(10) unsigned NOT NULL,
  `fids` varchar(12000) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_0` */

DROP TABLE IF EXISTS `platform_user_info_0`;

CREATE TABLE `platform_user_info_0` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_1` */

DROP TABLE IF EXISTS `platform_user_info_1`;

CREATE TABLE `platform_user_info_1` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_2` */

DROP TABLE IF EXISTS `platform_user_info_2`;

CREATE TABLE `platform_user_info_2` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_3` */

DROP TABLE IF EXISTS `platform_user_info_3`;

CREATE TABLE `platform_user_info_3` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_4` */

DROP TABLE IF EXISTS `platform_user_info_4`;

CREATE TABLE `platform_user_info_4` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_5` */

DROP TABLE IF EXISTS `platform_user_info_5`;

CREATE TABLE `platform_user_info_5` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_6` */

DROP TABLE IF EXISTS `platform_user_info_6`;

CREATE TABLE `platform_user_info_6` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_7` */

DROP TABLE IF EXISTS `platform_user_info_7`;

CREATE TABLE `platform_user_info_7` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_8` */

DROP TABLE IF EXISTS `platform_user_info_8`;

CREATE TABLE `platform_user_info_8` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_9` */

DROP TABLE IF EXISTS `platform_user_info_9`;

CREATE TABLE `platform_user_info_9` (
  `uid` int(10) unsigned NOT NULL,
  `puid` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `figureurl` varchar(255) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '-1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `vuid` varchar(16) NOT NULL DEFAULT '',
  `promote_code` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_0` */

DROP TABLE IF EXISTS `platform_user_info_more_0`;

CREATE TABLE `platform_user_info_more_0` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_1` */

DROP TABLE IF EXISTS `platform_user_info_more_1`;

CREATE TABLE `platform_user_info_more_1` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_2` */

DROP TABLE IF EXISTS `platform_user_info_more_2`;

CREATE TABLE `platform_user_info_more_2` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_3` */

DROP TABLE IF EXISTS `platform_user_info_more_3`;

CREATE TABLE `platform_user_info_more_3` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_4` */

DROP TABLE IF EXISTS `platform_user_info_more_4`;

CREATE TABLE `platform_user_info_more_4` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_5` */

DROP TABLE IF EXISTS `platform_user_info_more_5`;

CREATE TABLE `platform_user_info_more_5` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_6` */

DROP TABLE IF EXISTS `platform_user_info_more_6`;

CREATE TABLE `platform_user_info_more_6` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_7` */

DROP TABLE IF EXISTS `platform_user_info_more_7`;

CREATE TABLE `platform_user_info_more_7` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_8` */

DROP TABLE IF EXISTS `platform_user_info_more_8`;

CREATE TABLE `platform_user_info_more_8` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `platform_user_info_more_9` */

DROP TABLE IF EXISTS `platform_user_info_more_9`;

CREATE TABLE `platform_user_info_more_9` (
  `uid` int(10) unsigned NOT NULL,
  `session_key` varchar(100) NOT NULL DEFAULT '',
  `info` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
