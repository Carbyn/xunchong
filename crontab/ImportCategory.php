<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['ImportCategory', 'run']);

class ImportCategory {

    public static $categories = [
        ['cid' => 100, 'pcid' => 0, 'name' => '猫猫', 'icon' => ''],
        ['cid' => 200, 'pcid' => 0, 'name' => '狗狗', 'icon' => ''],

        ['cid' => 10000, 'pcid' => 100, 'name' => '猫咪主粮', 'icon' => ''],
        ['cid' => 10001, 'pcid' => 100, 'name' => '猫咪零食', 'icon' => ''],
        ['cid' => 10002, 'pcid' => 100, 'name' => '猫日用品', 'icon' => ''],
        ['cid' => 10003, 'pcid' => 100, 'name' => '猫咪玩具', 'icon' => ''],
        ['cid' => 10004, 'pcid' => 100, 'name' => '猫咪出行', 'icon' => ''],

        ['cid' => 20000, 'pcid' => 200, 'name' => '狗狗主粮', 'icon' => ''],
        ['cid' => 20001, 'pcid' => 200, 'name' => '狗狗零食', 'icon' => ''],
        ['cid' => 20002, 'pcid' => 200, 'name' => '狗日用品', 'icon' => ''],
        ['cid' => 20003, 'pcid' => 200, 'name' => '狗狗玩具', 'icon' => ''],
        ['cid' => 20004, 'pcid' => 200, 'name' => '狗狗出行', 'icon' => ''],

        ['cid' => 1000000, 'pcid' => 10000, 'name' => '猫干粮', 'icon' => '/static/category/mao/maoganliang.jpg'],
        ['cid' => 1000001, 'pcid' => 10000, 'name' => '猫罐头/湿粮', 'icon' => '/static/category/mao/maoguantou.jpg'],
        ['cid' => 1000100, 'pcid' => 10001, 'name' => '猫零食', 'icon' => '/static/category/mao/maolingshi.jpg'],
        ['cid' => 1000200, 'pcid' => 10002, 'name' => '猫窝', 'icon' => '/static/category/mao/maowo.jpg'],
        ['cid' => 1000201, 'pcid' => 10002, 'name' => '猫砂', 'icon' => '/static/category/mao/maosha.jpg'],
        ['cid' => 1000202, 'pcid' => 10002, 'name' => '食具水具', 'icon' => '/static/category/mao/shijushuiju.jpg'],
        ['cid' => 1000203, 'pcid' => 10002, 'name' => '清洁除味', 'icon' => '/static/category/mao/qingjiechuwei.jpg'],
        ['cid' => 1000204, 'pcid' => 10002, 'name' => '笼子', 'icon' => '/static/category/mao/longzi.jpg'],
        ['cid' => 1000205, 'pcid' => 10002, 'name' => '猫砂盆', 'icon' => '/static/category/mao/maoshapen.jpg'],
        ['cid' => 1000300, 'pcid' => 10003, 'name' => '猫玩具', 'icon' => '/static/category/mao/maowanju.jpg'],
        ['cid' => 1000301, 'pcid' => 10003, 'name' => '猫爬架', 'icon' => '/static/category/mao/maopajia.jpg'],
        ['cid' => 1000302, 'pcid' => 10003, 'name' => '猫抓板', 'icon' => '/static/category/mao/maozhuaban.jpg'],
        ['cid' => 1000400, 'pcid' => 10004, 'name' => '牵引绳/胸背带', 'icon' => '/static/category/mao/qianyinsheng.jpg'],
        ['cid' => 1000401, 'pcid' => 10004, 'name' => '航空箱/便携包', 'icon' => '/static/category/mao/hangkongxiang.jpg'],
        ['cid' => 1000402, 'pcid' => 10004, 'name' => '宠物鞋服', 'icon' => '/static/category/mao/chongwuxiefu.jpg'],
        ['cid' => 1000403, 'pcid' => 10004, 'name' => '外出用品', 'icon' => '/static/category/mao/waichuyongpin.jpg'],
        ['cid' => 1000404, 'pcid' => 10004, 'name' => '宠物配饰', 'icon' => '/static/category/mao/chongwupeishi.jpg'],

        ['cid' => 2000000, 'pcid' => 20000, 'name' => '狗干粮', 'icon' => '/static/category/gou/gouganliang.jpg'],
        ['cid' => 2000001, 'pcid' => 20000, 'name' => '狗罐头/湿粮', 'icon' => '/static/category/gou/gouguantou.jpg'],
        ['cid' => 2000100, 'pcid' => 20001, 'name' => '狗零食', 'icon' => '/static/category/gou/goulingshi.jpg'],
        ['cid' => 2000200, 'pcid' => 20002, 'name' => '狗厕所', 'icon' => '/static/category/gou/goucesuo.jpg'],
        ['cid' => 2000201, 'pcid' => 20002, 'name' => '食具水具', 'icon' => '/static/category/gou/shijushuiju.jpg'],
        ['cid' => 2000202, 'pcid' => 20002, 'name' => '清洁除味', 'icon' => '/static/category/gou/qingjiechuwei.jpg'],
        ['cid' => 2000203, 'pcid' => 20002, 'name' => '笼子/围栏', 'icon' => '/static/category/gou/longzi.jpg'],
        ['cid' => 2000204, 'pcid' => 20002, 'name' => '尿垫', 'icon' => '/static/category/gou/niaodian.jpg'],
        ['cid' => 2000300, 'pcid' => 20003, 'name' => '狗玩具', 'icon' => '/static/category/gou/gouwanju.jpg'],
        ['cid' => 2000400, 'pcid' => 20004, 'name' => '牵引绳/胸背带', 'icon' => '/static/category/gou/qianyinsheng.jpg'],
        ['cid' => 2000401, 'pcid' => 20004, 'name' => '航空箱/便携包', 'icon' => '/static/category/gou/hangkongxiang.jpg'],
        ['cid' => 2000402, 'pcid' => 20004, 'name' => '宠物鞋服', 'icon' => '/static/category/gou/chongwuxiefu.jpg'],
        ['cid' => 2000403, 'pcid' => 20004, 'name' => '外出用品', 'icon' => '/static/category/gou/waichuyongpin.jpg'],
        ['cid' => 2000404, 'pcid' => 20004, 'name' => '宠物配饰', 'icon' => '/static/category/gou/chongwupeishi.jpg'],
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
