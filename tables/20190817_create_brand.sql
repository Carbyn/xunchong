CREATE TABLE `brand` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
    `name` varchar(64) NOT NULL DEFAULT '' COMMENT 'brand name',
    `icon` varchar(1024) NOT NULL DEFAULT '' COMMENT 'brand icon',
    primary key (`id`),
    unique key `uniq_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
