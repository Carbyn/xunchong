<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['ImportCategory', 'run']);

class ImportCategory {

    public static $categories = [
        ['cid' => 100, 'pcid' => 0, 'name' => '猫猫', 'icon' => ''],
        ['cid' => 200, 'pcid' => 0, 'name' => '狗狗', 'icon' => ''],

        ['cid' => 10000, 'pcid' => 100, 'name' => '干粮系列', 'icon' => ''],
        ['cid' => 10001, 'pcid' => 100, 'name' => '零食系列', 'icon' => ''],
        ['cid' => 10002, 'pcid' => 100, 'name' => '医疗保健', 'icon' => ''],
        ['cid' => 10003, 'pcid' => 100, 'name' => '生活日用', 'icon' => ''],
        ['cid' => 10004, 'pcid' => 100, 'name' => '猫咪玩具', 'icon' => ''],
        ['cid' => 10005, 'pcid' => 100, 'name' => '猫咪美容', 'icon' => ''],
        ['cid' => 10006, 'pcid' => 100, 'name' => '猫砂猫厕', 'icon' => ''],

        ['cid' => 20000, 'pcid' => 200, 'name' => '干粮系列', 'icon' => ''],
        ['cid' => 20001, 'pcid' => 200, 'name' => '零食系列', 'icon' => ''],
        ['cid' => 20002, 'pcid' => 200, 'name' => '医疗保健', 'icon' => ''],
        ['cid' => 20003, 'pcid' => 200, 'name' => '生活日用', 'icon' => ''],
        ['cid' => 20004, 'pcid' => 200, 'name' => '狗狗美容', 'icon' => ''],
        ['cid' => 20005, 'pcid' => 200, 'name' => '狗狗玩具', 'icon' => ''],
        ['cid' => 20006, 'pcid' => 200, 'name' => '出行装备', 'icon' => ''],

        ['cid' => 1000000, 'pcid' => 10000, 'name' => '进口粮', 'icon' => '/static/category/mao/jinkouliang.jpg'],
        ['cid' => 1000001, 'pcid' => 10000, 'name' => '国产粮', 'icon' => '/static/category/mao/guochanliang.jpg'],
        ['cid' => 1000002, 'pcid' => 10000, 'name' => '处方粮', 'icon' => '/static/category/mao/chufangliang.jpg'],
        ['cid' => 1000003, 'pcid' => 10000, 'name' => '冻干粮', 'icon' => '/static/category/mao/dongganliang.jpg'],

        ['cid' => 1000100, 'pcid' => 10001, 'name' => '罐头湿粮', 'icon' => '/static/category/mao/guantoushiliang.jpg'],
        ['cid' => 1000101, 'pcid' => 10001, 'name' => '猫草薄荷', 'icon' => '/static/category/mao/maocaobohe.jpg'],
        ['cid' => 1000102, 'pcid' => 10001, 'name' => '肉类零食', 'icon' => '/static/category/mao/xiuxianlingshi.jpg'],

        ['cid' => 1000200, 'pcid' => 10002, 'name' => '皮肤治疗', 'icon' => '/static/category/mao/pifuzhiliao.jpg'],
        ['cid' => 1000201, 'pcid' => 10002, 'name' => '内外驱虫', 'icon' => '/static/category/mao/neiwaiquchong.jpg'],
        ['cid' => 1000202, 'pcid' => 10002, 'name' => '常备药品', 'icon' => '/static/category/mao/changbeiyaopin.jpg'],
        ['cid' => 1000203, 'pcid' => 10002, 'name' => '美毛化毛', 'icon' => '/static/category/mao/meimaohuamao.jpg'],
        ['cid' => 1000204, 'pcid' => 10002, 'name' => '补钙健骨', 'icon' => '/static/category/mao/bugaijiangu.jpg'],
        ['cid' => 1000205, 'pcid' => 10002, 'name' => '综合营养', 'icon' => '/static/category/mao/zongheyingyang.jpg'],

        ['cid' => 1000300, 'pcid' => 10003, 'name' => '出行用品', 'icon' => '/static/category/mao/chuxingyongpin.jpg'],
        ['cid' => 1000301, 'pcid' => 10003, 'name' => '猫咪餐具', 'icon' => '/static/category/mao/maomicanju.jpg'],
        ['cid' => 1000302, 'pcid' => 10003, 'name' => '猫咪住所', 'icon' => '/static/category/mao/maomizhusuo.jpg'],
        ['cid' => 1000303, 'pcid' => 10003, 'name' => '服装饰品', 'icon' => '/static/category/mao/fuzhuangshipin.jpg'],

        ['cid' => 1000400, 'pcid' => 10004, 'name' => '猫爬架', 'icon' => '/static/category/mao/maopajia.jpg'],
        ['cid' => 1000401, 'pcid' => 10004, 'name' => '猫抓板', 'icon' => '/static/category/mao/maozhuaban.jpg'],
        ['cid' => 1000402, 'pcid' => 10004, 'name' => '逗猫玩具', 'icon' => '/static/category/mao/doumaowanju.jpg'],

        ['cid' => 1000500, 'pcid' => 10005, 'name' => '洗护用品', 'icon' => '/static/category/mao/xihuxiangbo.jpg'],
        ['cid' => 1000501, 'pcid' => 10005, 'name' => '疏剪工具', 'icon' => '/static/category/mao/shujiangongju.jpg'],

        ['cid' => 1000600, 'pcid' => 10006, 'name' => '猫砂', 'icon' => '/static/category/mao/kuangwumaosha.jpg'],
        ['cid' => 1000601, 'pcid' => 10006, 'name' => '猫咪厕所', 'icon' => '/static/category/mao/maomicesuo.jpg'],
        ['cid' => 1000602, 'pcid' => 10006, 'name' => '清洁除味', 'icon' => '/static/category/mao/paibianqingjie.jpg'],

        ['cid' => 2000000, 'pcid' => 20000, 'name' => '进口粮', 'icon' => '/static/category/gou/jinkouliang.jpg'],
        ['cid' => 2000001, 'pcid' => 20000, 'name' => '国产粮', 'icon' => '/static/category/gou/guochanliang.jpg'],
        ['cid' => 2000002, 'pcid' => 20000, 'name' => '处方粮', 'icon' => '/static/category/gou/chufangliang.jpg'],
        ['cid' => 2000003, 'pcid' => 20000, 'name' => '冻干粮', 'icon' => '/static/category/gou/dongganliang.jpg'],

        ['cid' => 2000100, 'pcid' => 20001, 'name' => '罐头湿粮', 'icon' => '/static/category/gou/guantoushiliang.jpg'],
        ['cid' => 2000101, 'pcid' => 20001, 'name' => '肉类零食', 'icon' => '/static/category/gou/rouleilingshi.jpg'],
        ['cid' => 2000102, 'pcid' => 20001, 'name' => '磨牙洁齿', 'icon' => '/static/category/gou/moyajiechi.jpg'],
        ['cid' => 2000103, 'pcid' => 20001, 'name' => '奶酪饼干', 'icon' => '/static/category/gou/nailaobinggan.jpg'],

        ['cid' => 2000200, 'pcid' => 20002, 'name' => '皮肤治疗', 'icon' => '/static/category/gou/pifuzhiliao.jpg'],
        ['cid' => 2000201, 'pcid' => 20002, 'name' => '内外驱虫', 'icon' => '/static/category/gou/neiwaiquchong.jpg'],
        ['cid' => 2000202, 'pcid' => 20002, 'name' => '常备药品', 'icon' => '/static/category/gou/changbeiyaopin.jpg'],
        ['cid' => 2000203, 'pcid' => 20002, 'name' => '美毛护肤', 'icon' => '/static/category/gou/meimaohufu.jpg'],
        ['cid' => 2000204, 'pcid' => 20002, 'name' => '补钙健骨', 'icon' => '/static/category/gou/bugaijiangu.jpg'],
        ['cid' => 2000205, 'pcid' => 20002, 'name' => '综合营养', 'icon' => '/static/category/gou/zongheyingyang.jpg'],

        ['cid' => 2000300, 'pcid' => 20003, 'name' => '狗狗餐具', 'icon' => '/static/category/gou/gougoucanju.jpg'],
        ['cid' => 2000301, 'pcid' => 20003, 'name' => '狗狗住所', 'icon' => '/static/category/gou/gougouzhusuo.jpg'],
        ['cid' => 2000302, 'pcid' => 20003, 'name' => '排便清洁', 'icon' => '/static/category/gou/paibianqingjie.jpg'],
        ['cid' => 2000303, 'pcid' => 20003, 'name' => '服装饰品', 'icon' => '/static/category/gou/fuzhuangshipin.jpg'],
        ['cid' => 2000304, 'pcid' => 20003, 'name' => '训练用品', 'icon' => '/static/category/gou/xunlianyongpin.jpg'],

        ['cid' => 2000400, 'pcid' => 20004, 'name' => '洗护用品', 'icon' => '/static/category/gou/xihuxiangbo.jpg'],
        ['cid' => 2000401, 'pcid' => 20004, 'name' => '疏剪工具', 'icon' => '/static/category/gou/shujiangongju.jpg'],

        ['cid' => 2000500, 'pcid' => 20005, 'name' => '互动玩具', 'icon' => '/static/category/gou/hudongwanju.jpg'],
        ['cid' => 2000501, 'pcid' => 20005, 'name' => '磨牙玩具', 'icon' => '/static/category/gou/moyawanju.jpg'],
        ['cid' => 2000502, 'pcid' => 20005, 'name' => '益智玩具', 'icon' => '/static/category/gou/yizhiwanju.jpg'],
        ['cid' => 2000503, 'pcid' => 20005, 'name' => '训练玩具', 'icon' => '/static/category/gou/xunlianwanju.jpg'],
        ['cid' => 2000504, 'pcid' => 20005, 'name' => '漏食玩具', 'icon' => '/static/category/gou/loushiwanju.jpg'],
        ['cid' => 2000505, 'pcid' => 20005, 'name' => '毛绒玩具', 'icon' => '/static/category/gou/maorongwanju.jpg'],

        ['cid' => 2000600, 'pcid' => 20006, 'name' => '航空箱包', 'icon' => '/static/category/gou/hangkongxiangbao.jpg'],
        ['cid' => 2000601, 'pcid' => 20006, 'name' => '牵引系列', 'icon' => '/static/category/gou/qianyinxilie.jpg'],
        ['cid' => 2000602, 'pcid' => 20006, 'name' => '胸背套装', 'icon' => '/static/category/gou/xiongbeitaozhuang.jpg'],
        ['cid' => 2000603, 'pcid' => 20006, 'name' => '项圈狗牌', 'icon' => '/static/category/gou/xiangquangoupai.jpg'],
        ['cid' => 2000604, 'pcid' => 20006, 'name' => '外出辅助', 'icon' => '/static/category/gou/waichufuzhu.jpg'],
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
