<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlJdkPromo', 'run']);

class CrawlJdkPromo {

    public static function run() {
        $goodsModel = new GoodsModel();
        $pn = 1;
        $ps = 20;
        while(true) {
            $goods_list = $goodsModel->fetchAllByPlatform(Constants::GOODS_PLATFORM_JDK, $pn, $ps);
            if (empty($goods_list)) {
                echo "goods_list empty\n";
                break;
            }
            foreach($goods_list as $goods) {
                $update = [];

                $promos = \Explorer\JD::fetchPromo($goods['oid'], $goods['ocid']);
                if (empty($promos)) {
                    echo "fetchPromo:{$goods['id']} failed\n";
                    continue;
                }
                $update['official_coupon_info'] = json_encode($promos);
                $goodsModel->update($goods['id'], $update);
                echo "{$goods['id']} updated\n";
            }
            $pn++;
            sleep(1);
        }
        echo "done\n";
    }

}


