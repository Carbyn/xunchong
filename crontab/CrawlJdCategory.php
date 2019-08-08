<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlJdCategory', 'run']);

class CrawlJdCategory {

    // jd 宠物生活一级类目id
    const CATID_XUNCHONG = 6994;

    public static function run() {
        $categories = \Explorer\JDKApis::getGoodsCategory(self::CATID_XUNCHONG, 1);
        var_dump($categories);exit;
        if ($categories) {
        }
    }
}

