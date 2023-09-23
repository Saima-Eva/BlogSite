<?php


namespace app\views;


class PostReadView extends PostEditorView
{
    public function __construct()
    {
        parent::__construct();
        $this->putTitle = false;
    }
}