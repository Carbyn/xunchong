<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlBoqiBrand', 'run']);

class CrawlBoqiBrand {

    const BRAND_URL = 'http://s.boqii.com/apidemo.php';
    const POST_BODY = 'Act=GetThirdCategoryAndBrand&CategoryId=%s';

    private static $brands = [
        '猫猫-干粮系列' => 576,
        '猫猫-零食系列' => 585,
        '猫猫-医疗保健' => [588, 596],
        '猫猫-生活日用' => 608,
        '猫猫-猫咪玩具' => 614,
        '猫猫-猫咪美容' => 677,
        '猫猫-猫砂猫厕' => 907,
        '狗狗-干粮系列' => 621,
        '狗狗-零食系列' => 629,
        '狗狗-医疗保健' => [634, 640],
        '狗狗-生活日用' => 656,
        '狗狗-狗狗美容' => 662,
        '狗狗-狗狗玩具' => 668,
        '狗狗-出行装备' => 875,
    ];

    public static function run() {
        foreach(self::$brands as $category => $cids) {
            echo "$category begin\n";
            if (!is_array($cids)) {
                $cids = [$cids];
            }
            foreach($cids as $cid) {
                $body = sprintf(self::POST_BODY, $cid);
                $data = \Explorer\Fetcher::postWithRetry(self::BRAND_URL, $body);
                if (!$data) {
                    echo "Fetch brand failed: $category\n";
                    continue;
                }
                $data = json_decode($data, true);
                if ($data['ResponseStatus'] != 0 || empty($data['ResponseData']['Brand'])) {
                    echo "Fetch brand failed: $category\n";
                    continue;
                }

                $categories = self::parseCategory($category);
                if (!$categories) {
                    continue;
                }

                foreach($data['ResponseData']['Brand'] as $brand) {
                    $bid = self::saveBrand($brand);
                    self::saveCat2brand($categories[1]->cid, $bid);
                    echo $brand['BrandName']." saved\n";
                }
            }
            echo "$category end\n";
        }
        echo "done\n";
    }

    private static function saveBrand($brand) {
        $brandModel = new BrandModel();
        $row = $brandModel->fetchByName($brand['BrandName']);
        if ($row) {
            return $row->id;
        }
        $data = [
            'name' => $brand['BrandName'],
            'icon' => $brand['BrandAppLogo'],
        ];
        $id = $brandModel->create($data);
        return $id;
    }

    private static function saveCat2brand($cid, $bid) {
        $cat2brandModel = new Cat2brandModel();
        $row = $cat2brandModel->fetchByCidAndBid($cid, $bid);
        if ($row) {
            return;
        }
        $data = compact('cid', 'bid');
        $cat2brandModel->create($data);
    }

    private static function parseCategory($title) {
        $category = explode('-', $title);
        if (count($category) != 2) {
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
        return [$c, $cc];
    }
}
