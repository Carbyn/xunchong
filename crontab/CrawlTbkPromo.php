<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlTbkPromo', 'run']);

class CrawlTbkPromo {

    public static function run() {
        $goodsModel = new GoodsModel();
        $pn = 1;
        $ps = 20;
        while(true) {
            $goods_list = $goodsModel->fetchAllByPlatform(Constants::GOODS_PLATFORM_TBK, $pn, $ps);
            if (empty($goods_list)) {
                echo "goods_list empty\n";
                break;
            }
            foreach($goods_list as $goods) {
                $update = [];

                $promos = \Explorer\Tmall::fetchPromo($goods['oid'], $goods['seller_id']);
                sleep(1);
                if (empty($promos)) {
                    echo "fetchPromo:{$goods['id']} failed\n";
                    continue;
                }
                $update['official_coupon_info'] = $promos;
                $goodsModel->update($goods['id'], $update);
                echo "{$goods['id']} updated\n";
            }
            $pn++;
        }
        echo "done\n";
    }

}

