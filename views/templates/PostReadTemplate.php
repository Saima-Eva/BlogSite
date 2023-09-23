<?php

use app\core\Application;
use app\core\Form;
use app\models\CommentModel;

$model = $model ?? null;
$form = new Form();

$profile_route = Application::$app->router->getRouteFromNamespace("profile");
$editor_route = Application::$app->router->getRouteFromNamespace("post-editor");
$login_route = Application::$app->router->getRouteFromNamespace("login");

?>
<div class="p-2">
    <article class="blog-post">
        <h1 class="blog-post-title"><?php echo $model->title ?>
            <?php if ($model->author->isCurrentUser()) { ?>
                &nbsp;<a class="btn btn-sm btn-outline-primary" href="<?php echo "{$editor_route['path']}/{$model->id}" ?>">Edit</a>
            <?php } ?>
        </h1>
        <p class="blog-post-meta"><?php echo $model->created_at ?> by <a href="<?php echo "{$profile_route['path']}/{$model->author_id}" ?>"><?php
                echo $model->author->getFullName() ?></a></p>
        <hr>
        <?php echo html_entity_decode($model->content) ?>
    </article>
    <div class="col-8">
        <h2 class="m-4">Comments</h2>
        <div class="container">
            <?php
            $commentModel = new CommentModel();
            foreach ($model->loadComments() as $comment) {?>
            <div class="p-4 mx-4 mb-3 bg-light rounded">
                <div class="mb-3">
                    <h6 class="mb-0">
                        <a class="" style="text-decoration:none" href="<?php echo "{$profile_route['path']}/{$comment->author->id}" ?>">
                            <?php echo $comment->author->getFullName()?>
                        </a>
                    </h6>
                    <small class="">Commented on <?php echo $comment->created_at?></small>

                    <?php if ($comment->author->isCurrentUser()){
                        $form = $form::begin('', 'post');
                        echo $form->button("Delete", "delete-comment", "secondary",
                            "submit", "badge", $comment->id->getValue());
                        $form::end();

                    } ?>
                </div>

                <p class="mb-0"><?php echo html_entity_decode($comment->content) ?></p>
            </div>
            <?php } ?>
        </div>

        <div class="container my-4">
            <h4 class="h4 mb-4">Write a comment</h4>
            <?php
            if (Application::$app->user->isUserLoggedIn) {
                $form = $form::begin('', 'post');
                echo $form->field($commentModel, 'content', 'textarea', ["rows" => 5], false);
                echo $form->button("Post Comment", "comment");
                $form::end();
            } else {?>
                <div class="p-4 mb-3 bg-light rounded">
                    Please <a class="" href="<?php echo $login_route["path"] ?>">Login</a> to comment
                </div><?php
            }
            ?>
        </div>

    </div>
</div>
