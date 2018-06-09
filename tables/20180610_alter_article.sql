alter table `article` add column `sid` varchar(64) not null default '' comment 'source id';
alter table `article` add key `sid` (`sid`);
