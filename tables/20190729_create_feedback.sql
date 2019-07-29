create table `feedback` (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `user_id` bigint(20) unsigned not null default 0 comment 'user id',
    `contact` varchar(32) not null default '' comment 'contact',
    `content` varchar(1024) not null default '' comment 'content',
    `create_time` int(11) unsigned not null default 0 comment 'create time',
    primary key (`id`)
) Engine=InnoDB default charset=utf8;

