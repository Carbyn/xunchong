create table article (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `type` tinyint(3) unsigned not null default 0 comment 'article type',
    `author` bigint(20) unsigned not null default 0 comment 'author id',
    `content` text not null default '' comment 'article content, including text, images and video links',
    `pub_time` int(11) unsigned not null default 0 comment 'article publish time',
    `event_time` int(11) unsigned not null default 0 comment 'event time, such as: lost/picked/',
    primary key (`id`),
    key `author_pub_time` (`author`, `pub_time`),
    key `pub_time_type` (`pub_time`, `type`)
) Engine=InnoDB default charset=utf8;
