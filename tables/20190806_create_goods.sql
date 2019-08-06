CREATE TABLE `goods` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
    `oid` varchar(32) NOT NULL DEFAULT '' COMMENT 'original id',
    `platform` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '1: tbk, 2: jd',
    `title` varchar(128) NOT NULL DEFAULT '' COMMENT 'title',
    `cat_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'cat id',
    `leaf_cat_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'leaf cat id',
    `reserve_price` decimal(20, 2) NOT NULL DEFAULT 0 COMMENT 'reserve price, ￥1.23',
    `final_price` decimal(20, 2) NOT NULL DEFAULT 0 COMMENT 'final price, ￥1.23',
    `volume` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'volume',
    `tk_rate` decimal(20, 2) NOT NULL DEFAULT 0 COMMENT 'tk_rate',
    `click_url` varchar(1024) NOT NULL DEFAULT '' COMMENT 'click url',
    `click_url_tpwd` varchar(64) NOT NULL DEFAULT '' COMMENT 'click url tpwd',
    `coupon_click_url` varchar(1024) NOT NULL DEFAULT '' COMMENT 'coupon click url',
    `coupon_click_url_tpwd` varchar(1024) NOT NULL DEFAULT '' COMMENT 'coupon click url tpwd',
    `item_url` varchar(1024) NOT NULL DEFAULT '' COMMENT 'item url',
    `pict_url` varchar(256) NOT NULL DEFAULT '' COMMENT 'pict url',
    `seller_id` varchar(32) NOT NULL DEFAULT '' COMMENT 'seller_id',
    `shop_title` varchar(64) NOT NULL DEFAULT '' COMMENT 'shop title',
    `small_images` varchar(2048) NOT NULL DEFAULT '' COMMENT 'small images, implode with |',
    `provcity` varchar(32) NOT NULL DEFAULT '' COMMENT 'provcity',
    `union_coupon_info` varchar(512) NOT NULL DEFAULT '' COMMENT 'coupon info',
    `official_coupon_info` varchar(5120) NOT NULL DEFAULT '' COMMENT 'coupon info',
    primary key (`id`),
    unique key `uniq_oid_platform` (`oid`, `platform`),
    key `idx_cat` (`cat_id`),
    key `idx_leaf_cat` (`leaf_cat_id`),
    FULLTEXT KEY `ft_title` (`title`) /*!50100 WITH PARSER `ngram` */
) ENGINE=InnoDB AUTO_INCREMENT=20190806 DEFAULT CHARSET=utf8;
