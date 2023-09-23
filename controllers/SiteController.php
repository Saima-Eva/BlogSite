<?php


namespace app\controllers;


use app\core\Controller;
use app\views;



class SiteController extends Controller
{


    public function contact()
    {
        $params = [
            "name" => "Simple MVC Blog"
        ];
        return $this->render(views\ContactView::class, $params);
    }




}