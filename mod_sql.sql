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

'itemid' => $val['itemid'],
            'itemshorttitle' => $val['itemshorttitle'],
            'itemdesc' => $val['itemdesc'],
            'itemprice' => $val['itemprice'],
            'itemsale' => $val['itemsale'],
            'itempic' => $val['itempic'] . '_310x310.jpg',
            'itemendprice' => $val['itemendprice'],
            'url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $val['activityid'] . '&itemId=' . $val['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid,
            'couponnum' => $val['couponnum'],
            'couponreceive2' => $val['couponreceive2'],
            'couponmoney' => $val['couponmoney'],
            'couponexplain' => $val['couponexplain'],
            'couponstarttime' => $val['couponstarttime'],
            'couponendtime' => $val['couponendtime'],
            'shoptype' => $val['shoptype'],
            'taobao_image' => explode(',', $val['taobao_image']),
            'itempic_copy' => 'http://img.haodanku.com/' . $val['itempic_copy'] . '-600',
            'fqcat' => $val['fqcat'],
            'sellernick' => $val['sellernick'],
            'discount' => $val['discount'],
            'activity_type' => $val['activity_type'],
            'video_url' => $val['videoid'] ? 'http://cloud.video.taobao.com/play/u/1/p/1/e/6/t/1/' . $val['videoid'] . 'mp4' : '',
            'share' => array(
                'share_title' => $val['itemshorttitle'] . '  领券后￥' . $val['itemprice'],
                'share_pic' => 'http://img.haodanku.com/' . $val['itempic_copy'] . '-100',
                'share_url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $val['activityid'] . '&itemId=' . $val['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid
            ),


 CREATE TABLE `tb` (
   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
   `min_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '好单库id',
   `itemid` bigint(20) NOT NULL DEFAULT '0' COMMENT '淘宝id',
   `activityid` varchar(100) NOT NULL DEFAULT '' COMMENT '券id',
   `sellerid` bigint(20) NOT NULL DEFAULT '0' COMMENT '商家id userid',
   `itemshorttitle` varchar(100) NOT NULL DEFAULT '' COMMENT '短标题',
   `itemdesc` varchar(300) NOT NULL DEFAULT '' COMMENT '长标题',
   `itemprice` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '原价',
   `itemendprice` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '券后价',
   `itemsale` int(11) NOT NULL DEFAULT '0' COMMENT '销量',
   `itempic` varchar(200) NOT NULL DEFAULT '' COMMENT '淘宝主图',
   `couponnum` int(11) NOT NULL DEFAULT '0' COMMENT '券量',
   `couponreceive` int(11) NOT NULL DEFAULT '0' COMMENT '领取量',
   `couponmoney` int(11) NOT NULL DEFAULT '0' COMMENT '券价',
   `couponexplain` varchar(100) NOT NULL DEFAULT '' COMMENT '券使用条件',
   `couponstarttime` bigint(20) NOT NULL DEFAULT '0' COMMENT '券开始时间',
   `couponendtime` bigint(20) NOT NULL DEFAULT '0' COMMENT '券结束时间',
   `shoptype` varchar(2) NOT NULL DEFAULT '' COMMENT '店铺类型 B C',
   `taobao_image` varchar(2000) NOT NULL DEFAULT '' COMMENT '淘宝图片 ,分割',
   `taobao_detail` text NOT NULL COMMENT '淘宝详情页',
   `videoid` bigint(20) NOT NULL DEFAULT '0' COMMENT '视频ID',
   `itempic_copy` varchar(200) NOT NULL DEFAULT '' COMMENT '分享图',
   `fqcat` int(11) NOT NULL DEFAULT '0' COMMENT '分类id',
   `shopname` varchar(100) NOT NULL DEFAULT '' COMMENT '店铺名称',
   `tktype` varchar(20) NOT NULL DEFAULT '' COMMENT '淘客计划',
   `tkrates` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '佣金比例',
   `activity_type` varchar(100) NOT NULL DEFAULT '' COMMENT '活动类型',
   `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态 1-正常 2-失效',
   `created_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间戳',
   `updated_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间戳',
   PRIMARY KEY (`id`),
   UNIQUE KEY `itemid` (`itemid`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='淘宝商品表';

 CREATE TABLE `tb_order` (
   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
   `trade_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '订单id',
   `tk_status` int(11) NOT NULL DEFAULT '0' COMMENT '订单状态',
   `trade_parent_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '父订单id',
   `num_iid` bigint(20) NOT NULL DEFAULT '0' COMMENT '淘宝id',
   `item_title` varchar(300) NOT NULL DEFAULT '' COMMENT '标题',
   `item_num` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
   `site_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '媒体id',
   `adzone_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '广告位id',
   `alipay_total_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '付款金额',
   `income_rate` decimal(8,2) NOT NULL DEFAULT '0.0000' COMMENT '收入比率(%)',
   `pub_share_pre_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '效果预估',
   `create_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '下单时间',
   `terminal_type` int(11) NOT NULL DEFAULT '0' COMMENT '成交平台 1-PC 2-无线',
   `created_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间戳',
   `updated_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间戳',
   PRIMARY KEY (`id`),
   UNIQUE KEY `trade_id` (`trade_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='淘宝订单表';


 CREATE TABLE `user_pid` (
   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
   `memberid_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '账号id',
   `site_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '媒体id',
   `adzone_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '广告位id',
   `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
   `created_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间戳',
   `updated_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间戳',
   PRIMARY KEY (`id`),
   UNIQUE KEY `pid` (`site_id`,`adzone_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户跟单表';


