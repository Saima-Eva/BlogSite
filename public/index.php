<?php

define("ROOT_DIR", dirname(__DIR__) );
define("SITE_NAME", "Simple MVC Blog" );

require_once ROOT_DIR . '/vendor/autoload.php';

use app\controllers\CategoryController;
use app\controllers\PostController;
use app\controllers\UserController;
use app\core\Application;
use app\core\Router;

Dotenv\Dotenv::createImmutable(ROOT_DIR. "/conf")->load();

$app = new Application();


$app->router->setRoute("/", [PostController::class, 'postAll'],
    Router::PERMISSION_PUBLIC, "Home", "home");
$app->router->setRoute("/profile", [PostController::class, 'postAll'],
    Router::PERMISSION_PUBLIC, "Profile", "profile");
$app->router->setRoute("/category", [PostController::class, 'postAll'],
    Router::PERMISSION_PUBLIC, "Category", "category");


$app->router->setRoute("/login", [UserController::class, 'login'],
    Router::PERMISSION_PUBLIC, "Sign In", "login");
$app->router->setRoute("/register", [UserController::class, 'register'],
    Router::PERMISSION_PUBLIC, "Sign Up", "register");
$app->router->setRoute("/logout", [UserController::class, 'logout'],
    Router::PERMISSION_USER, "Sign out", "logout");



$app->router->setRoute("/post", [PostController::class, 'postRead'],
    Router::PERMISSION_PUBLIC, "Post", "post");
$app->router->setRoute("/post-editors", [PostController::class, 'postEditor'],
    Router::PERMISSION_USER, "Write a Post", "post-editor");
$app->router->setRoute("/post-list", [PostController::class, 'postList'],
    Router::PERMISSION_USER, "Post List", "post-list");

$app->router->setRoute("/post-moderation", [PostController::class, 'postModeration'],
    Router::PERMISSION_ADMIN, "Post Moderation", "post-moderation");
$app->router->setRoute("/user-moderation", [UserController::class, 'userModeration'],
    Router::PERMISSION_ADMIN, "User Moderation", "user-moderation");
$app->router->setRoute("/category-moderation", [CategoryController::class, 'categoryModeration'],
    Router::PERMISSION_ADMIN, "Category Moderation", "category-moderation");


$app->run();