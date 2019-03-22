
-- 2016-8-8 18:02:17
ALTER TABLE fish_goods ADD COLUMN `readed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否已读' AFTER `fish_text`;
ALTER TABLE fish_goods ADD COLUMN `like` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否喜欢' AFTER `readed`;

-- 2016-8-0 18:04:08
CREATE TABLE `coding_user` (
  `id` int(11) NOT NULL,
  `name` varchar(16) CHARACTER SET latin1 NOT NULL,
  `followed` tinyint(1) NOT NULL DEFAULT '0',
  `spidered` tinyint(1) NOT NULL DEFAULT '0',
  `date` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fish_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fish_id` bigint(32) NOT NULL DEFAULT '0',
  `fish_title` varchar(64) NOT NULL DEFAULT '',
  `fish_pool_id` varchar(32) NOT NULL DEFAULT '0',
  `fish_pool_name` varchar(64) NOT NULL DEFAULT '',
  `fish_time` int(11) NOT NULL DEFAULT '0',
  `fish_price` int(8) NOT NULL DEFAULT '0',
  `fish_image_url` text NOT NULL,
  `fish_url` char(64) NOT NULL DEFAULT '',
  `fish_text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fish_id` (`fish_id`,`fish_title`)
) ENGINE=MyISAM AUTO_INCREMENT=729 DEFAULT CHARSET=utf8;
