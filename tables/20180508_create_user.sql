create table user (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `name` varchar(32) not null default '' comment 'user name',
    `openid` varchar(128) not null default '' comment 'wx openid',
    `mobile` char(11) not null default '' comment 'user mobile number',
    `avatar` varchar(256) not null default '' comment 'user avatar',
    `password` char(32) not null default '' comment 'user password',
    `address` varchar(64) not null default '' comment 'user address',
    `register_time` int(11) unsigned not null default 0 comment 'user register time',
    primary key (`id`),
    unique key uniq_openid(`openid`)
) Engine=InnoDB default charset=utf8;
