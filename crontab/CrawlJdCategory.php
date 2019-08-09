<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlJdCategory', 'run']);

class CrawlJdCategory {

    // jd 宠物生活一级类目id
    const CATID_XUNCHONG = 6994;

    public static function run() {
        $categories = self::extractJdCategory(self::CATID_XUNCHONG, 1);
        if ($categories) {
            foreach ($categories as &$c1) {
                $c1['children'] = self::extractJdCategory($c1['cid'], 2);
            }
            echo "Crawl jd category succ\n";
            echo json_encode($categories)."\n";
        }
        return;
    }

    private static function extractJdCategory($parentId, $grade) {
        $rsp = \Explorer\JDKApis::getGoodsCategory($parentId, $grade);
        if ($rsp && !empty($rsp->result)) {
            if (false !== ($res = json_decode($rsp->result))) {
                if (!empty($res->data)) {
                    foreach ($res->data as $item) {
                        $categories[] = [
                            'cid'  => $item->id,
                            'pcid' => $item->parentId,
                            'name' => $item->name,
                            'icon' => '',
                        ];
                    }
                }
            }
        }
        return isset($categories) ? $categories : false;
    }
}

