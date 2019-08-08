<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlJdk', 'run']);

class CrawlJdk {

    public static function run() {
        $categories = \Exploer\JDKApis::getGoodsCategory();
    }
}

