<?php
class IndexController extends \Explorer\ControllerAbstract{

	public function indexAction() {
        $floors = [
            'tabs' => [
                [
                    'name' => '超值精选',
                    'params' => '',
                ],
                [
                    'name' => '主子心爱',
                    'params' => 'level=1&cid=100',
                ],
                [
                    'name' => '狗狗必备',
                    'params' => 'level=1&cid=200',
                ],
            ],
            'banners' => [
                [
                    'name' => '每日精选',
                    'params' => 'level=1&cid=300',
                    'img' => 'https://img2.epetbar.com/2019-03/20/20/ecd80b8eab5e23fda542a77a1f9740aa.jpg?x-oss-process=style/water',
                ],
            ]
        ];
        $this->outputSuccess(compact('floors'));
	}

}
