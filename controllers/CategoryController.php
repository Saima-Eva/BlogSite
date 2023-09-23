<?php


namespace app\controllers;


use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\CategoryModel;
use app\views\CategoryModerationView;


class CategoryController extends Controller
{

    public function categoryModeration(Request $request)
    {
        $model = new CategoryModel();
        if ($request->isPost()) {

            $body = $request->getBody();
            $model->loadData($body);

            if ($model->id->getValue()){
                if (isset($body["delete"])){
                    $model->deleteCategory();
                } else {
                    $model->updateCategory();
                }
            } else {
                $model->addCategory();
            }

            return $this->redirectSameURI();
        }
        $model->selectCategories();
        return $this->render(CategoryModerationView::class, ["model" => $model]);
    }

}