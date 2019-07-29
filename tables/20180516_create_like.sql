create table `like` (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `goods_id` bigint(20) unsigned not null default 0 comment 'goods id',
    `user_id` bigint(20) unsigned not null default 0 comment 'user id',
    `create_time` int(11) unsigned not null default 0 comment 'create time',
    primary key (`id`),
    unique key uniq_goods_user(`goods_id`, `user_id`)
) Engine=InnoDB default charset=utf8;
