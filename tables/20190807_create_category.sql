CREATE TABLE `category` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
    `cid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT 'category id',
    `pcid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT 'category parent id',
    `name` varchar(32) NOT NULL DEFAULT '' COMMENT 'category name',
    `icon` varchar(1024) NOT NULL DEFAULT '' COMMENT 'category icon',
    primary key (`id`),
    key `idx_cid` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
