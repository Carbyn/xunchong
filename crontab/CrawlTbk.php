<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlTbk', 'run']);

class CrawlTbk {

    public static function run() {
        $pn = 1;
        $brandModel = new BrandModel();
        $brands = $brandModel->fetchAll();
        while(true) {
            $favList = \Explorer\Tbk::getFavoritesList($pn++, 1);
            if (!$favList || !property_exists($favList, 'results') || !property_exists($favList->results, 'tbk_favorites')) {
                echo "getFavoritesList failed\n";
                break;
            }
            foreach($favList->results->tbk_favorites as $fav) {
                echo "{$fav->favorites_title} begin\n";

                $categories = self::parseCategory($fav->favorites_title);
                if (!$categories) {
                    continue;
                }
                $i = 1;
                while(true) {
                    $items = \Explorer\Tbk::getFavoritesItem($fav->favorites_id, $i++, 10);
                    if (!$items || !property_exists($items, 'results') || !property_exists($items->results, 'uatm_tbk_item')) {
                        echo "getFavoritesItem:{$fav->favorites_id} failed\n";
                        break;
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
                                $item[$url.'_tpwd'] = '';
                                echo "createTpwd:{$item['num_iid']} failed\n";
                                continue;
                            }
                            $item[$url.'_tpwd'] = $tpwd['data']->model;
                        }
                        if (empty($item['click_url']) || empty($item['pict_url'])) {
                            echo "item click_url or pict_url empty: {$item['num_iid']}\n";
                            continue;
                        }
                        $item['brand_id'] = self::matchBrand($brands, $item['title']);
                        $item['categories'] = $categories;
                        self::saveGoods($item);
                        echo "{$item['num_iid']} saved\n";
                    }
                    sleep(1);
                }
                echo "{$fav->favorites_title} end\n";
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
            'cat_id' => $item['categories'][0]->cid,
            's_cat_id' => $item['categories'][1]->cid,
            'leaf_cat_id' => $item['categories'][2]->cid,
            'brand_id' => (int)$item['brand_id'],
            'reserve_price' => (float)$item['reserve_price'],
            'final_price' => (float)$item['zk_final_price_wap'],
            'volume' => (int)$item['volume'],
            'tk_rate' => (float)$item['tk_rate'],
            'click_url' => $item['click_url'],
            'click_url_tpwd' => $item['click_url_tpwd'],
            'coupon_click_url' => $item['coupon_click_url'],
            'coupon_click_url_tpwd' => $item['coupon_click_url_tpwd'],
            'item_url' => $item['item_url'],
            'pict_url' => $item['pict_url'],
            'seller_id' => (string)$item['seller_id'],
            'shop_title' => $item['shop_title'],
            'provcity' => $item['provcity'],
            'union_coupon_info' => self::parseCoupon($item),
        ];

        $data['score'] = intval(($data['volume']/100*0.5 + $data['tk_rate']*0.5) * 100);

        if (!empty($item['small_images'])) {
            $item['small_images'] = (array)$item['small_images'];
            if (!empty($item['small_images']['string'])) {
                $data['small_images'] = implode('|', $item['small_images']['string']);
            }
        }

        // todo, it's not working now
        // $promos = \Explorer\Tmall::fetchPromo($item['num_iid'], $item['seller_id']);
        // if ($promos) {
        //     $data['official_coupon_info'] = json_encode($promos);
        // }

        // echo json_encode($data);exit;

        $goods = $goodsModel->exists($item['num_iid'], \Constants::GOODS_PLATFORM_TBK);
        if ($goods) {
            $goodsModel->update($goods['id'], $data);
        } else {
            $goodsModel->create($data);
        }
    }

    private static function parseCategory($title) {
        $category = explode('-', $title);
        if (count($category) == 2 && $category[0] == '每日精选') {
            $category = ['精选', '精选', '每日精选'];
        }
        if (count($category) != 3) {
            echo "{$title} format unsupport\n";
            return false;
        }
        $categoryModel = new CategoryModel();
        $c = $categoryModel->fetchCatByPcidAndName(0, $category[0]);
        if (!$c) {
            echo "{$category[0]} not found\n";
            return false;
        }
        $cc = $categoryModel->fetchCatByPcidAndName($c->cid, $category[1]);
        if (!$cc) {
            echo "{$c->cid}, $category[1] not found\n";
            return false;
        }
        $ccc = $categoryModel->fetchCatByPcidAndName($cc->cid, $category[2]);
        if (!$ccc) {
            echo "{$cc->cid}, $category[2] not found\n";
            return false;
        }
        return [$c, $cc, $ccc];
    }

    private static function matchBrand($brands, $title) {
        foreach($brands as $b) {
            if (mb_strpos($title, $b['name']) !== false) {
                return $b['id'];
            }
        }
        return false;
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
