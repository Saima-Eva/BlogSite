<?php

use app\core\Application;

$app = Application::$app;
$view = $view ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Rafayet Ullah">
    <meta name="generator" content="Hugo 0.79.0">
    <title><?php echo $view->getTitle() . " | " . SITE_NAME; ?> </title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/blog/">


    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .link-black {
            color: black;
            text-decoration: none;

        }
    </style>


    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair&#43;Display:700,900&amp;display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->

    {{extra_css}}
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/blog.css">

</head>
<body>
<?php include_once "messages.php" ?>

<div class="container-fluid">
    <?php include_once "header.php"; ?>



</div>

<main class="container-fluid">

    <?php
//    if (Application::$app->request->getFullPath() == "/") {
//        include_once "featured.php";
//    }
    ?>


    <div class="row mt-4">
        <?php if ($view->putTitle){
            echo "<h1 class='mb-4'>{$view->getTitle()}</h1>";
        }?>

        <div class="col-md-9">
            {{content}}
        </div>

        <div class="col-md-3">
            <?php include_once "sidebar.php";?>
        </div>

    </div><!-- /.row -->

</main><!-- /.container -->

<?php include_once "footer.php";?>


<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: Bootstrap Bundle with Popper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

{{extra_js}}

<script src="/js/script.js"></script>



</body>
</html>
