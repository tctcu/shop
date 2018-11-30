 CREATE TABLE `admin_user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL COMMENT '姓名',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `mobile` char(11) NOT NULL DEFAULT '0' COMMENT '手机号',
  `salt` char(4) NOT NULL,
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '类型 1-普通 6-管理员',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态 2-禁止登录',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户表';

 CREATE TABLE `admin_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `access_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `access_id` (`access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户权限关联表';

CREATE TABLE `admin_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(40) DEFAULT '',
  `m` varchar(20) DEFAULT '' COMMENT 'module',
  `c` varchar(20) DEFAULT '' COMMENT 'controller',
  `a` varchar(30) DEFAULT '' COMMENT 'action',
  PRIMARY KEY (`id`),
  KEY `m` (`m`),
  KEY `c` (`c`),
  KEY `a` (`a`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='后台权限表';



insert into admin_user (uid, name, password, mobile, salt, type, created_at, updated_at)
values ('1', '张顺灵', '43e708ad2d43852f8c496f83b05a2f13', '15305634799', '1234', '9', '1530981900', '1530981900');


insert into admin_access (id, title, m, c, a) values (1001, '商品列表', 'admin', 'shop', 'index');
insert into admin_access (id, title, m, c, a) values (1002, '广告列表', 'admin', 'shop', 'banner');
insert into admin_access (id, title, m, c, a) values (1003, '增加/编辑广告', 'admin', 'shop', 'createbanner');
insert into admin_access (id, title, m, c, a) values (1004, '删除广告', 'admin', 'shop', 'delbanner');

insert into admin_access (id, title, m, c, a) values (2001, '用户列表', 'admin', 'user', 'index');

insert into admin_access (id, title, m, c, a) values (3001, '统计列表', 'admin', 'stat', 'index');

insert into admin_access (id, title, m, c, a) values (8001, '后台用户管理', 'admin', 'adminuser', 'index');
insert into admin_access (id, title, m, c, a) values (8002, '编辑用户', 'admin', 'adminuser', 'create');
insert into admin_access (id, title, m, c, a) values (8003, '权限控制', 'admin', 'adminuser', 'role');
insert into admin_access (id, title, m, c, a) values (8004, '密码重置', 'admin', 'adminuser', 'reset');


INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,1001,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,2001,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,3001,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,8001,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,8002,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,8003,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,8004,1530981900);


CREATE TABLE `banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `position` varchar(20) NOT NULL DEFAULT 'banner' COMMENT '位置',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '类型 1-链接 2-商品ID',
  `pic` varchar(200) NOT NULL DEFAULT '' COMMENT '图片url',
  `goto` varchar(500) NOT NULL DEFAULT '' COMMENT '根据type决定',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='banner广告表';

CREATE TABLE `user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(4) NOT NULL DEFAULT '' COMMENT '盐',
  `device` varchar(64) NOT NULL DEFAULT ''  COMMENT '设备号',
  `device_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '设备类型 1-IOS 2-安卓',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态 2-禁止登录',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

ALTER TABLE user add `z_user_id` varchar(16) NOT NULL DEFAULT '' COMMENT '支付宝id' after `status`,
add `z_avatar` varchar(400) NOT NULL DEFAULT '' COMMENT '支付宝头像' after `status`,
add `z_province` varchar(20) NOT NULL DEFAULT '' COMMENT '支付宝省' after `status`,
add `z_city` varchar(20) NOT NULL DEFAULT '' COMMENT '支付宝城市' after `status`,
add `z_nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '支付宝昵称' after `status`,
add `z_is_student_certified` varchar(2) NOT NULL DEFAULT '' COMMENT '支付宝是否学生 T-是' after `status`,
add `z_user_type` varchar(2) NOT NULL DEFAULT '' COMMENT '支付宝是否公司 1-是 2-个人' after `status`,
add `z_user_status` varchar(2) NOT NULL DEFAULT '' COMMENT '支付宝用户状态 Q-快速注册 T-已认证 B-冻结 W-未激活' after `status`,
add `z_is_certified` varchar(2) NOT NULL DEFAULT '' COMMENT '支付宝是否实名认证 T-是' after `status`,
add `z_gender` varchar(10) NOT NULL DEFAULT '' COMMENT '支付宝性别 F-女 M-男' after `status`;

ALTER TABLE user
add `w_sex` varchar(10) NOT NULL DEFAULT '' COMMENT '微信性别 0-女 1-男' after `status`,
add `w_country` varchar(20) NOT NULL DEFAULT '' COMMENT '微信国家' after `status`,
add `w_province` varchar(20) NOT NULL DEFAULT '' COMMENT '微信省' after `status`,
add `w_city` varchar(20) NOT NULL DEFAULT '' COMMENT '微信城市' after `status`,
add `w_headimgurl` varchar(400) NOT NULL DEFAULT '' COMMENT '微信头像' after `status`,
add `w_nickname` varchar(200) NOT NULL DEFAULT '' COMMENT '微信昵称' after `status`,
add `w_unionid` varchar(40) NOT NULL DEFAULT '' COMMENT '微信unionid' after `status`,
add `w_openid` varchar(40) NOT NULL DEFAULT '' COMMENT '微信openid' after `status`;