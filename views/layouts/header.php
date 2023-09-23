<?php

use app\core\Application;
use app\core\Form;

$view = $view ?? null;
$user = Application::$app->user;
$category_route = Application::$app->router->getRouteFromNamespace("category");
$home_route = Application::$app->router->getRouteFromNamespace("home");

?>
<header class="blog-header py-4">
    <div class="row flex-nowrap justify-content-between align-items-center">
        <div class="col-4 pt-1">
<!--            <a class="link-secondary" href="#">Subscribe</a>-->
        </div>
        <div class="col-4 text-center">
            <a class="blog-header-logo text-dark" href="<?php echo $home_route['path'] ?>"><?php echo SITE_NAME; ?></a>
        </div>
        <div class="col-4 d-flex justify-content-end align-items-center">

<!--            <a class="link-secondary" href="#" aria-label="Search">-->
<!--                <form action="" method="post">-->
<!--                    <input type="text" placeholder="">-->
<!--                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor"-->
<!--                         stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="mx-3" role="img"-->
<!--                         viewBox="0 0 24 24"><title>Search</title>-->
<!--                        <circle cx="10.5" cy="10.5" r="7.5"/>-->
<!--                        <path d="M21 21l-5.2-5.2"/>-->
<!--                    </svg>-->
<!--                </form>-->
<!--            </a>-->
            <?php
            echo $view->getButtonFromNamespace("home");
            if (!$user->isUserLoggedIn) {
                echo $view->getButtonFromNamespace("login");
                echo $view->getButtonFromNamespace("register");
            } else {
                echo "<a class='btn btn-sm btn-outline-secondary mx-1 dropdown-toggle' role='button'
                   data-bs-toggle='dropdown' aria-expanded='false'>{$user->getFullName()}</a>";
                $userOptions = $view->getUserOptions();
                echo "<ul class='dropdown-menu my-1'>";
                echo "<li>{$view->getButtonFromNamespace("profile", "dropdown-item")}</li>";

                foreach ($userOptions[0] as $option) {
                    echo "<li>{$view->getButtonFromNamespace($option["namespace"], "dropdown-item")}</li>";
                }
                if ($user->isAdminUser() && sizeof($userOptions[1])) {
                    echo "<li><hr class='dropdown-divider'></li>";
                    foreach ($userOptions[1] as $option) {
                        echo "<li>{$view->getButtonFromNamespace($option["namespace"], "dropdown-item")}</li>";
                    }
                }
                echo '</ul>';
                echo $view->getButtonFromNamespace("logout");
            }
            ?>
        </div>
    </div>
</header>


<div class=" py-1 m-2 bg-light rounded">
    <nav class="nav d-flex justify-content-between">
        <?php foreach (Application::$app->category->categoryArray as $key => $value) {
            echo "<a class='p-2 link-secondary link-black' href='{$category_route['path']}/$key'>$value</a>";
        }
        ?>
    </nav>
</div>