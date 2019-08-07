<?php
class CategoryController extends \Explorer\ControllerAbstract {

    public function listAction() {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->fetchAll();
        $this->outputSuccess(compact('categories'));
    }

}
