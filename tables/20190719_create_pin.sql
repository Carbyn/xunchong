create table `pin` (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `screenshot_id` bigint(20) unsigned not null default 0 comment 'goods screenshot id',
    `user_id` bigint(20) unsigned not null default 0 comment 'user id',
    `create_time` int(11) unsigned not null default 0 comment 'create time',
    primary key (`id`),
    unique key uniq_screenshot_author (`screenshot_id`, `user_id`)
) Engine=InnoDB default charset=utf8;
