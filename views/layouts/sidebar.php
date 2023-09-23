<?php

use app\core\Application;

$app = Application::$app;

$category_route = $app->router->getRouteFromNamespace("category")
?>
<div class="p-4 mb-3 bg-light rounded">
    <h4 class="font-italic">About</h4>
    <p class="mb-0">This is <em>Simple MVC Blog</em>. This is build by PHP OOP with MVC concept.  </p>
</div>
<div class="p-4 mb-3 bg-light rounded">
    <h4 class="font-italic"><?php echo $category_route["title"] ?></h4>
    <ol class="list-unstyled mb-0">
        <?php
        foreach ($app->category->categoryArray as $key => $value){
            echo "<li><a href='{$category_route['path']}/$key'>$value</a></li>";
        }
        ?>
    </ol>
</div>

