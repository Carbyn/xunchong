CREATE TABLE `query` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
    `query` varchar(128) NOT NULL DEFAULT '' COMMENT 'query words',
    `cid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'query cid',
    `is_lacked` tinyint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'query result is lacked or not',
    `times` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'query times',
    primary key (`id`),
    unique key `uniq_query_cid` (`query`, `cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
