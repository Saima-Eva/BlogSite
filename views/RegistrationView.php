<?php


namespace app\views;


use app\core\View;

class RegistrationView extends View
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout(self::FULLPAGE);

    }

}