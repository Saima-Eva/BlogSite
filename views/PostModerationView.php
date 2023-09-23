<?php


namespace app\views;


class PostModerationView extends PostListView
{
    public function __construct()
    {
        $this->datatableID = "postModerationTable";
        parent::__construct();
    }

    protected function loadExtraCSS()
    {
        $this->extra_css = parent::loadExtraCSS();
        ob_start(); ?>
        <?php
        $this->extra_css .= ob_get_clean();
        ob_flush();
    }

    protected function loadExtraJS()
    {
        $this->extra_js = parent::loadExtraJS();
        ob_start(); ?>
        <script>
            let modalID = "#exampleModal";
            let modalKeys = [["id", 0], ["status", 3]];
        </script>
        <script src="/js/modal.js"></script>

        <?php
        $this->extra_js .= ob_get_clean();
        ob_flush();
    }
}