<?php
include_once(dirname(__FILE__).'/Base.php');

if ($argc < 3) {
    echo "need path and filename\n";
    return;
}

$app->execute(['CrawlJdkWorker', 'run'], $argv[1], $argv[2]);

class CrawlJdkWorker {

    public static function run($path, $filename) {
        $categories = self::parseCategory($filename);
        if (!$categories) {
            return;
        }
        $rows = file_get_contents($path);
        if (empty($rows)) {
            echo "$filename empty\n";
            return;
        }
        $rows = iconv('gbk', 'utf8', $rows);
        $rows = explode("\n", $rows);
        $i = 0;
        foreach($rows as $row) {
            if ($i++ == 0 || trim($row) == '') {
                continue;
            }
            self::processRow($categories, $row);
        }
    }

    private static function processRow($categories, $row) {
        $basic_info = preg_split('#\s,#', $row);
        $item = [
            'title' => trim($basic_info[0]),
            'item_url' => trim($basic_info[1]),
            'volume' => intval(trim($basic_info[2])),
            'final_price' => (float)trim($basic_info[3]),
            'tk_rate' => (float)trim($basic_info[4]),
            'click_url' => trim($basic_info[6]),
            'coupon_click_url' => trim($basic_info[7]),
        ];
        if (!preg_match('#http://item.jd.com/(\d+).html#', $item['item_url'], $matches)) {
            echo "$row\n";
            echo "{$item['item_url']} parse skuid failed\n";
            return;
        }
        $skuid = $matches[1];
        $result = \Explorer\JDKApis::getGoodsInfo($skuid);
        if (!$result || $result->code != '0' || !property_exists($result, 'result')) {
            echo "getGoodsInfo failed {$skuid}\n";
            return;
        }
        $result = json_decode($result->result, true);
        if (!$result || $result['code'] != 200 || empty($result['data'])) {
            echo "getGoodsInfo failed {$skuid}\n";
            return;
        }
        $full_info = $result['data'][0];
        if (!$full_info) {
            echo "fetch full info failed $skuid\n";
            return;
        }
        $item = array_merge($item, $full_info);
        $item['categories'] = $categories;
        self::saveGoods($item);
        echo "$skuid save\n";
    }

    private static function saveGoods($item) {
        $goodsModel = new GoodsModel();
        $categoryModel = new CategoryModel();

        $data = [
            'oid' => (string)$item['skuId'],
            'platform' => \Constants::GOODS_PLATFORM_JDK,
            'title' => $item['goodsName'],
            'cat_id' => $item['categories'][0]->cid,
            's_cat_id' => $item['categories'][1]->cid,
            'leaf_cat_id' => $item['categories'][2]->cid,
            'reserve_price' => 0,
            'final_price' => (float)$item['wlUnitPrice'],
            'volume' => (int)$item['inOrderCount'],
            'tk_rate' => (float)$item['commisionRatioWl'],
            'click_url' => $item['click_url'],
            'click_url_tpwd' => '',
            'coupon_click_url' => $item['coupon_click_url'],
            'coupon_click_url_tpwd' => '',
            'item_url' => $item['item_url'],
            'pict_url' => $item['imgUrl'],
            'seller_id' => (string)$item['shopId'],
            'shop_title' => $item['isJdSale'] ? '自营' : '第三方',
            'provcity' => '',
            'union_coupon_info' => '',
        ];

        $data['score'] = intval(($data['volume']/100*0.5 + $data['tk_rate']*0.5) * 100);

        $promos = \Explorer\JD::fetchPromo($item['skuId'], $item['cid3']);
        if ($promos) {
            $data['official_coupon_info'] = json_encode($promos);
        }

        $goods = $goodsModel->exists($item['skuId'], \Constants::GOODS_PLATFORM_JDK);
        if ($goods) {
            $goodsModel->update($goods['id'], $data);
        } else {
            $goodsModel->create($data);
        }
    }

    private static function parseCategory($title) {
        $title = explode('_', $title);
        if (empty($title)) {
            echo "$title format unsupport\n";
            return false;
        }
        $category = explode('-', $title[0]);
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

}

