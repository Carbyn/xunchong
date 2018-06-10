CREATE TABLE `article` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
      `author` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'author id',
      `mobile` char(11) NOT NULL DEFAULT '' COMMENT 'article mobile',
      `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'article type',
      `event_time` varchar(32) NOT NULL DEFAULT '' COMMENT 'event time',
      `event_address` varchar(256) NOT NULL DEFAULT '' COMMENT 'event address',
      `reward` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'article reward',
      `text` varchar(4096) NOT NULL DEFAULT '' COMMENT 'article text',
      `images` varchar(4096) NOT NULL DEFAULT '' COMMENT 'article image urls',
      `pub_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'article publish time',
      `closed` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'closed 0/1',
      `sid` varchar(64) NOT NULL DEFAULT '' COMMENT 'source id',
      `approved` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'approved 0: no, 1: yes',
      PRIMARY KEY (`id`),
      KEY `sid` (`sid`),
      KEY `type_approved_pubtime` (`type`,`approved`,`pub_time`),
      KEY `approved_pubtime` (`approved`,`pub_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
