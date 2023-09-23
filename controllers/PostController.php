<?php


namespace app\controllers;


use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\CommentModel;
use app\models\PostModel;
use app\models\UserModel;
use app\views\HomeView;
use app\views\LoginView;
use app\views\PostEditorView;
use app\views\PostModerationView;
use app\views\PostReadView;
use app\views\PostListView;

class PostController extends Controller
{
    public function postEditor(Request $request)
    {
        $isPostExist = false;
        $model = new PostModel();
        $post_id = $request->getPath()[1][0] ?? null;
        if ($post_id) {
            $isPostExist = $model->isPostExist($post_id, $loginRequired = true);
            if (!$isPostExist) {
                return $this->redirect($request->getPath()[0]);
            };
        }

        if ($request->isPost()) {
            $body = $request->getBody();
            $body["author_id"] = $this->currentUserID();
            $body["status"] = isset($body["publish"]) ? PostModel::STATUS_PENDING : PostModel::STATUS_UNPUBLISHED;
            $model->loadData($body);
            if ($post_id && $isPostExist) {
                $saved = $model->updatePost();
            } else {
                $saved = $model->writePost();
            }
            if ($saved) {
                return $this->redirect(PostListView::PATH);
            } else {
                return $this->redirectSameURI();
            }


        }
        return $this->render(PostEditorView::class, ["model" => $model]);
    }

    public function postRead(Request $request)
    {
        $isPostExist = false;
        $model = new PostModel();
        $post_id = $request->getPath()[1][0] ?? null;
        if ($post_id) {
            $isPostExist = $model->isPostExist($post_id);
            if (!$isPostExist)
                return $this->redirectHome();
        } else {
            return $this->redirectHome();
        }

        if ($request->isPost()) {
            $body = $request->getBody();
            $body["author_id"] = $this->currentUserID();
            $body["post_id"] = $post_id;

            if (isset($body["comment"]) && $isPostExist) {
                $this->loginRequired();
                $commentModel = new CommentModel();
                $commentModel->loadData($body);
                $commentModel->writeComment();
            } elseif (isset($body["delete-comment"]) && $isPostExist) {
                $this->loginRequired();
                $body["id"] = $body["delete-comment"];
                $commentModel = new CommentModel();
                $commentModel->loadData($body);
                $commentModel->deleteComment();
            }
            return $this->redirectSameURI();

        }
        return $this->render(PostReadView::class, ["model" => $model]);
    }

    public function postList(Request $request)
    {
        $model = new PostModel();
        $model->getAuthorPosts();
        return $this->render(PostListView::class, ["model" => $model]);
    }

    public function postModeration(Request $request)
    {
        $model = new PostModel();
        if ($request->isPost()) {
            $body = $request->getBody();
            $model->loadData($body);
            $model->updateStatus();
            return $this->redirectSameURI();
        }
        $model->getPosts();
        return $this->render(PostModerationView::class, ["model" => $model]);
    }

    public function handlePage(Request $request)
    {
        $page = $request->getBody()["page"] ?? "1";
        $page = intval($page);
        $previous = $page-1;
        $next = $page+1;
        return ["pagination" => ["Older" => $previous, "Newer" => $next], "page" => $page];
    }

    public function postAll(Request $request)
    {
        $model = new PostModel();
        $route = Application::$app->router->getRoute();
        $parameter = $request->getPath()[1][0] ?? null;
        $page = $this->handlePage($request);
        if ($page["page"]==0)
            return $this->redirectSameURI();
        $model->page = $page["page"];

        switch ($route["namespace"]){
            case "home":
                $model->getHomePosts();
                break;
            case "category":
                if (!$parameter)
                    return $this->redirectHome();
                $model->loadData(["category" => $parameter]);
                if (!$model->isFormValid)
                    return $this->redirectHome();
                $model->getCategoryPosts();
                break;
            case "profile":
                if (!$parameter)
                    $this->loginRequired();
                $userModel = new UserModel();
                $userModel->loadData(["id" => $parameter]);
                $isUserExist = $userModel->isValidUser($userModel->id);
                if (!$isUserExist)
                    return $this->redirectHome();
                else
                    $model->loadData(["author_id" => $userModel->id->getValue()]);

                $model->getProfilePosts($parameter);
                break;
        }

        if ($model->end)
            $page["pagination"]["Newer"] = 0;

        return $this->render(HomeView::class, [
            "model" => $model,
            "page" => $page["pagination"]
        ]);
    }
}