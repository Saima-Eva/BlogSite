<?php

use app\core\Application;

$model = $model ?? null;
$category_route = Application::$app->router->getRouteFromNamespace("category");
$profile_route = Application::$app->router->getRouteFromNamespace("profile");
$post_route = Application::$app->router->getRouteFromNamespace("post");

?>
<?php
if ($model->postList) {
    foreach ($model->postList as $post) { ?>
        <div class="p-4 mb-3 bg-light rounded">
            <div class="mb-3">
                <h4><a href="<?php echo "{$post_route['path']}/{$post->id}" ?>" class="text-dark"
                       style="text-decoration: none;"><?php echo $post->title; ?></a></h4>
                <small>
                    Category: <a class="" href="<?php echo "{$category_route['path']}/$post->category" ?>">
                        <?php echo $post->category->getName() ?></a> | Posted by
                    <a class="" href="<?php echo "{$profile_route['path']}/{$post->author->id}" ?>">
                        <?php echo $post->author->getFullName() ?></a>
                    on <?php echo $post->created_at ?></small>
            </div>

            <p class="mb-0"><?php echo $post->content->htmlText(400) ?>
                <b><a href="<?php echo "{$post_route['path']}/{$post->id}" ?>" class="text-dark" style="text-decoration: none;">(read
                        more...))</a></b>
            </p>
        </div>
    <?php }
    $page = $page ?? [];
    echo "<nav class='blog-pagination' aria-label='Pagination'>";
    foreach ($page as $key => $value){
        $path = Application::$app->request->getOnlyPath();
        if ($value<=0){
            echo "<a class='btn btn-outline-secondary disabled' href='#' tabindex='-1' aria-disabled='true'>$key</a>";
        } else {
            echo "<a class='btn btn-outline-primary mx-1' href='$path?page=$value'>$key</a>";
        }
    }
    echo "</nav>";
    $pre_attr = (isset($page["previous"]) && $page["previous"]<=0 ) ? "aria-disabled='true' disabled='disabled'" : "";

} else {
    echo "<h2 class='mb-4'>No post found</h2>";
}

?>


