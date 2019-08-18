<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlJdkUpdate', 'run']);

class CrawlJdkUpdate {

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

                $result = \Explorer\JDKApis::getGoodsInfo($goods['oid']);
                if (!$result || $result->code != '0' || !property_exists($result, 'result')) {
                    echo "getGoodsInfo failed {$goods['id']}\n";
                    continue;
                }
                $result = json_decode($result->result, true);
                if (!$result || $result['code'] != 200 || empty($result['data'])) {
                    echo "getGoodsInfo failed {$goods['id']}\n";
                    $update['status'] = 1;
                } else {
                    $item = $result['data'][0];
                    $update = [
                        'final_price' => (float)$item['wlUnitPrice'],
                        'volume' => (int)$item['inOrderCount'],
                        'ocid' => (int)$item['cid3'],
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



