<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlTbkUpdate', 'run']);

class CrawlTbkUpdate {

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

                $info = \Explorer\Tbk::getItemInfo($goods['oid']);
                if (!$info || !property_exists($info, 'results') || !property_exists($info->results, 'n_tbk_item')) {
                    echo "getItemInfo:{$goods['id']} failed\n";
                    $update = ['status' => 1];
                } else {
                    $item = $info->results->n_tbk_item[0];
                    $update = [
                        'reserve_price' => $item->reserve_price,
                        'final_price' => $item->zk_final_price,
                        'volume' => $item->volume,
                    ];
                    $update['score'] = intval(($update['volume']/100*0.5 + $goods['tk_rate']*0.5)*100);
                }
                usleep(100000);
                $goodsModel->update($goods['id'], $update);
                echo "{$goods['id']} updated\n";
            }
            $pn++;
        }
        echo "done\n";
    }

}


