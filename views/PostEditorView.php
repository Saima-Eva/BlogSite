<?php


namespace app\views;


use app\core\View;

class PostEditorView extends View
{

    public function __construct()
    {
        parent::__construct();
        $this->loadExtraJS();
    }


    protected function loadExtraJS()
    {
        ob_start();?>
        <script src="\vendors\tinymce_5.6.2\tinymce\js\tinymce\tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            tinymce.init({
                selector: 'textarea#content',
                skin: 'bootstrap',
                plugins: 'lists, link, image, media',
                toolbar: 'h1 h2 bold italic strikethrough blockquote bullist numlist backcolor | link image media | removeformat help',
                menubar: true,
                branding: false
            });
        </script>
        <?php
        $this->extra_js = ob_get_clean();
        ob_flush();
    }

    protected function loadExtraCSS()
    {
        ob_start(); ?>
        <style>
            .float-right {
                float: right !important;

            .row {
                margin-left: auto !important;
            }
            }
        </style>
        <?php
        $this->extra_css = ob_get_clean();
        ob_flush();
    }
}