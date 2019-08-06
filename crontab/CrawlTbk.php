<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlTbk', 'run']);

class CrawlTbk {

    public static function run() {
        $favList = \Explorer\Tbk::getFavoritesList();
        if (!$favList) {
            echo "getFavoritesList failed\n";
            return;
        }
        foreach($favList->results->tbk_favorites as $fav) {
            for($i = 1; $i < 3; $i++) {
                $items = \Explorer\Tbk::getFavoritesItem($fav->favorites_id, $i, 10);
                if (!$items) {
                    echo "getFavoritesItem:$fid failed\n";
                    continue;
                }
                foreach($items->results->uatm_tbk_item as &$item) {
                    $item = (array)$item;
                    $urls = ['click_url', 'coupon_click_url'];
                    foreach($urls as $url) {
                        if (empty($item[$url])) {
                            $item[$url] = '';
                            $item[$url.'_tpwd'] = '';
                            continue;
                        }
                        $tpwd = (array)\Explorer\Tbk::createTpwd($item[$url], $item['title'], $item['pict_url']);
                        if (empty($tpwd['data'])) {
                            echo "createTpwd:{$item['num_iid']} failed\n";
                            continue;
                        }
                        $item[$url.'_tpwd'] = $tpwd['data']->model;
                    }
                    $info = \Explorer\Tbk::getItemInfo($item['num_iid']);
                    if (!$info) {
                        echo "getItemInfo:{$item['num_iid']} failed\n";
                        continue;
                    }
                    $item['cat_name'] = $info->results->n_tbk_item[0]->cat_name;
                    $item['cat_leaf_name'] = $info->results->n_tbk_item[0]->cat_leaf_name;
                    self::saveGoods($item);
                    echo "{$item['num_iid']} saved\n";
                }
            }
        }
        echo "done\n";
    }

    private static function saveGoods($item) {
        $goodsModel = new GoodsModel();
        $categoryModel = new CategoryModel();

        $data = [
            'oid' => (string)$item['num_iid'],
            'platform' => \Constants::GOODS_PLATFORM_TBK,
            'title' => $item['title'],
            'cat_id' => $categoryModel->fetchCatByName($item['cat_name']),
            'leaf_cat_id' => $categoryModel->fetchLeafCatByName($item['cat_leaf_name']),
            'reserve_price' => (float)$item['reserve_price'],
            'final_price' => (float)$item['zk_final_price_wap'],
            'volume' => (int)$item['volume'],
            'click_url' => $item['click_url'],
            'click_url_tpwd' => $item['click_url_tpwd'],
            'coupon_click_url' => $item['coupon_click_url'],
            'coupon_click_url_tpwd' => $item['coupon_click_url_tpwd'],
            'item_url' => $item['item_url'],
            'pict_url' => $item['pict_url'],
            'seller_id' => (string)$item['seller_id'],
            'shop_title' => $item['shop_title'],
            'small_images' => implode('|', $item['small_images']->string),
            'provcity' => $item['provcity'],
            'union_coupon_info' => self::parseCoupon($item),
        ];

        // echo json_encode($data);exit;

        $goods = $goodsModel->exists($item['num_iid'], \Constants::GOODS_PLATFORM_TBK);
        if ($goods) {
            $goodsModel->update($goods['id'], $data);
        } else {
            $goodsModel->create($data);
        }
    }

    private static function parseCoupon($item) {
        if (empty($item['coupon_info'])) {
            return '';
        }
        if (preg_match('/满(\d+)元减(\d+)元/', $item['coupon_info'], $matches)) {
            $starttime = strtotime($item['coupon_start_time']);
            $endtime = strtotime($item['coupon_end_time']) + 86400 - 1;
            $coupon = [
                'starttime' => $starttime,
                'endtime' => $endtime,
                'ext' => [[
                    'needMoney' => (int)$matches[1],
                    'rewardMoney' => (int)$matches[2],
                ]],
            ];
            return json_encode($coupon);
        }
        return '';
    }

}
