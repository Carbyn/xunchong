create table `comment` (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `article_id` bigint(20) unsigned not null default 0 comment 'article id',
    `author_id` bigint(20) unsigned not null default 0 comment 'author id',
    `author_name` varchar(32) not null default '' comment 'author name',
    `text` varchar(1024) not null default '' comment 'comment content',
    `reply_author_id` bigint(20) unsigned not null default 0 comment 'reply author id',
    `reply_author_name` varchar(32) not null default '' comment 'reply author name',
    `pub_time` int(11) unsigned not null default 0 comment 'comment publish time',
    primary key (`id`),
    key `article_id_time` (`article_id`, `pub_time`)
) Engine=InnoDB default charset=utf8;
