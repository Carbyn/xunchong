create table article (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `author` bigint(20) unsigned not null default 0 comment 'author id',
    `mobile` char(11) not null default '' comment 'article mobile',
    `type` tinyint(3) unsigned not null default 0 comment 'article type',
    `event_time` varchar(32) not null default '' comment 'event time',
    `event_address` varchar(256) not null default '' comment 'event address',
    `reward` int(11) unsigned not null default 0 comment 'article reward',
    `text` varchar(4096) not null default '' comment 'article text',
    `images` varchar(4096) not null default '' comment 'article image urls',
    `pub_time` int(11) unsigned not null default 0 comment 'article publish time',
    `closed` tinyint(3) unsigned not null default 0 comment 'closed 0/1',
    primary key (`id`),
    key `type_pub_time` (`type`, `pub_time`),
    key `pub_time` (`pub_time`)
) Engine=InnoDB default charset=utf8;
