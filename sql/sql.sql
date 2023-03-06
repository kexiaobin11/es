SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `yunzhi_user`;
CREATE TABLE `yunzhi_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT '' COMMENT '姓名',
  `permissions` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0用户，1管理员',
  `username` varchar(16) NOT NULL COMMENT '用户名',
	`password` varchar(30) not null COMMENT '登录密码',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
	
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


BEGIN;
INSERT INTO `yunzhi_user` VALUES ('1', '张三', '0', 'zhangsan', '123','123123', '123213'), ('2', '李四', '1', 'lisi', '123', '123213', '1232');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;



SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `yunzhi_pay`;
CREATE TABLE `yunzhi_pay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(30) not null COMMENT '支付类别',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

BEGIN;
INSERT INTO `yunzhi_pay` VALUES ('1', '电费', '123123','321321');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;




SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `yunzhi_income`;
CREATE TABLE `yunzhi_income` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) not null COMMENT '收入类型',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

BEGIN;
INSERT INTO `yunzhi_income` VALUES ('1', '工资', '123123','321321');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;



SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;





SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `yunzhi_account`;
CREATE TABLE `yunzhi_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(30) not null COMMENT '支出收入方式',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建日期',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

BEGIN;
INSERT INTO `yunzhi_account` VALUES ('1', '支付宝', '123123','321321'),('2', '微信', '123123','321321');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;





SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `yunzhi_stream`;
CREATE TABLE `yunzhi_stream` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`money` double COMMENT '金额',
	 `account_id` int(11) NOT NULL COMMENT '账户',
	`income_id` int(11)  COMMENT '收入分类',
	`pay_id` int(11)  COMMENT '支出分类',
	`inandex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0支出，1收入',
	`remark` varchar(50) comment '备注',
	`create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付的日期',
	`update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
		
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

BEGIN;
INSERT INTO `yunzhi_stream` VALUES ('2', '328.88', '1', '1','','1','水电费','123123','321321');
COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
