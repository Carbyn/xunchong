<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlTpwd', 'run']);

class CrawlTpwd {

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

                $tpwd = (array)\Explorer\Tbk::createTpwd($goods['click_url'], $goods['title'], $goods['pict_url']);
                if (empty($tpwd['data'])) {
                    echo "createTpwd:{$goods['id']} failed\n";
                    continue;
                }
                $update['click_url_tpwd'] = $tpwd['data']->model;

                if (!empty($goods['coupon_click_url'])) {
                    $tpwd = (array)\Explorer\Tbk::createTpwd($goods['coupon_click_url'], $goods['title'], $goods['pict_url']);
                    if (empty($tpwd['data'])) {
                        echo "createTpwd:{$goods['id']} failed\n";
                        continue;
                    }
                    $update['coupon_click_url_tpwd'] = $tpwd['data']->model;
                }

                $goodsModel->update($goods['id'], $update);
                echo "{$goods['id']} updated\n";
            }
            $pn++;
            sleep(1);
        }
        echo "done\n";
    }

}
