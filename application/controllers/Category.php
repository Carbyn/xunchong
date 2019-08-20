<?php
class CategoryController extends \Explorer\ControllerAbstract {

    public function listAction() {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->fetchAll();
        foreach($categories as $i => $c) {
            if ($c['cid'] > 200) {
                unset($categories[$i]);
            }
        }
        $this->outputSuccess(compact('categories'));
    }

}
