create table `like` (
    `id` bigint(20) unsigned auto_increment comment 'primary key',
    `article_id` bigint(20) unsigned not null default 0 comment 'article id',
    `author_id` bigint(20) unsigned not null default 0 comment 'author id',
    `pub_time` int(11) unsigned not null default 0 comment 'time',
    primary key (`id`),
    unique key uniq_article_author (`article_id`, `author_id`)
) Engine=InnoDB default charset=utf8;
