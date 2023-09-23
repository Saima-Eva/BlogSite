<?php


namespace app\views;


use app\core\View;

class PostListView extends View
{
    const PATH = "/post-list";
    public string $datatableID = "postListTable";

    public function __construct()
    {
        parent::__construct();
        $this->loadExtraCSS();
        $this->loadExtraJS();
    }


    protected function loadExtraCSS()
    {
        ob_start();?>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap5.min.css" rel="stylesheet">
        <style>
            th, td {
                max-width: 350px;
            }
        </style>
        <?php
        $this->extra_css = ob_get_clean();
        ob_flush();
        return $this->extra_css;
    }

    protected function loadExtraJS()
    {
        ob_start();
        echo "<script>let tableID = '#{$this->datatableID}';</script>";
        ?>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function() {
                $(tableID).DataTable();
            } );
        </script>
        <?php
        $this->extra_js = ob_get_clean();
        ob_flush();
        return $this->extra_js;
    }


}