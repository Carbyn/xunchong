<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['ImportCategory', 'run']);

class ImportCategory {

    public static $categories = [
        ['cid' => 100, 'pcid' => 0, 'name' => '猫猫', 'icon' => ''],
        ['cid' => 200, 'pcid' => 0, 'name' => '狗狗', 'icon' => ''],

        ['cid' => 10000, 'pcid' => 100, 'name' => '干粮系列', 'icon' => ''],
        ['cid' => 10001, 'pcid' => 100, 'name' => '零食系列', 'icon' => ''],

        ['cid' => 20000, 'pcid' => 200, 'name' => '干粮系列', 'icon' => ''],
        ['cid' => 20001, 'pcid' => 200, 'name' => '医疗系列', 'icon' => ''],

        ['cid' => 1000000, 'pcid' => 10000, 'name' => '进口粮', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 1000001, 'pcid' => 10000, 'name' => '国产量', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 1000002, 'pcid' => 10000, 'name' => '冻干量', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],

        ['cid' => 1000100, 'pcid' => 10001, 'name' => '罐头湿粮', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 1000101, 'pcid' => 10001, 'name' => '肉类零食', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 1000102, 'pcid' => 10001, 'name' => '猫草薄荷', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 1000103, 'pcid' => 10001, 'name' => '休闲零食', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],

        ['cid' => 2000000, 'pcid' => 20000, 'name' => '进口粮', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 2000001, 'pcid' => 20000, 'name' => '国产量', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 2000002, 'pcid' => 20000, 'name' => '冻干量', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],

        ['cid' => 2000100, 'pcid' => 20001, 'name' => '皮肤治疗', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 2000101, 'pcid' => 20001, 'name' => '综合护理', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 2000102, 'pcid' => 20001, 'name' => '内外驱虫', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
        ['cid' => 2000103, 'pcid' => 20001, 'name' => '常备药品', 'icon' => 'http://img.boqiicdn.com/Data/Shop/0/0/0/shopcat_pic1515038606_thumb.jpg'],
    ];

    public static function run() {
        $categoryModel = new CategoryModel();
        foreach(self::$categories as $c) {
            if ($cm = $categoryModel->fetch($c['cid'])) {
                $categoryModel->update($cm->id, $c);
                echo "{$c['name']} updated\n";
            } else {
                $categoryModel->create($c);
                echo "{$c['name']} created\n";
            }
        }
        echo "done\n";
    }

}
