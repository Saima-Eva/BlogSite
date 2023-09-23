<?php


namespace app\views;


use app\core\View;

class HomeView extends View
{
    public function __construct()
    {
        parent::__construct();
        $this->putTitle = false;
    }

}