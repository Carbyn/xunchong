CREATE TABLE `cat2brand` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
    `cid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'category id',
    `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'brand id',
    primary key (`id`),
    unique key `uniq_cid_bid` (`cid`, `bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

