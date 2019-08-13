<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlJdk', 'run']);

class CrawlJdk {

    public static function run() {
        $dir = dirname(__FILE__).'/jd';
        $files = scandir($dir);
        foreach($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            echo "$file begin\n";
            $categories = self::parseCategory($file);
            if (!$categories) {
                continue;
            }
            $rows = file_get_contents($dir.'/'.$file);
            if (empty($rows)) {
                echo "$file empty\n";
                continue;
            }
            $rows = iconv('gb2312', 'utf8', $rows);
            $rows = explode("\n", $rows);
            $i = 0;
            foreach($rows as $row) {
                if ($i++ == 0 || trim($row) == '') {
                    continue;
                }
                echo $row."\n";
                $basic_info = explode(',', $row);
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
                    echo "$item[1] parse skuid failed\n";
                    continue;
                }
                $skuid = $matches[1];
                $result = \Explorer\JDKApis::getGoodsInfo($skuid);
                if (!$result || $result->code != '0') {
                    echo "getGoodsInfo failed {$skuid}\n";
                    continue;
                }
                $result = json_decode($result->result, true);
                if (!$result || $result['code'] != 200 || empty($result['data'])) {
                    echo "getGoodsInfo failed {$skuid}\n";
                    continue;
                }
                $full_info = $result['data'][0];
                if (!$full_info) {
                    echo "fetch full info failed $skuid\n";
                    continue;
                }
                $item = array_merge($item, $full_info);
                $item['categories'] = $categories;
                self::saveGoods($item);
                echo "$skuid save\n";
            }
            echo "$file end\n";
            sleep(1);
        }
        echo "done\n";
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
            'shop_title' => '',
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
        if (count($title) != 2) {
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
