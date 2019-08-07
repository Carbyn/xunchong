create table `report` (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `goods_id` bigint(20) unsigned NOT NULl comment 'goods id',
    `content` varchar(1024) NOT NULL DEFAULT '' comment 'report content',
    `update_time` int(11) unsigned NOT NULL DEFAULT 0 comment 'update time',
    primary key (`id`)
) Engine=InnoDB default charset=utf8;

