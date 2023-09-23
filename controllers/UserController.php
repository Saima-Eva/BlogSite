<?php


namespace app\controllers;


use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\PostModel;
use app\models\UserModel;
use app\views\LoginView;
use app\views\PostModerationView;
use app\views\RegistrationView;
use app\views\UserModerationView;


class UserController extends Controller
{

    public function login(Request $request)
    {
        $userModel = Application::$app->user;
        if ($userModel->isUserLoggedIn){
            Application::$app->session->setMessage("warning", "Already Logged In",
                "You are already logged in as a user.");
            return $this->redirectHome();
        }

        if ($request->isPost()) {

            $userModel->loadData($request->getBody());
            if ($userModel->login()) {
                return Application::$app->response->redirect("/");
            }
        }
        return $this->render(LoginView::class, [
            "model" => $userModel
        ]);
    }

    public function register(Request $request)
    {
        $userModel = Application::$app->user;
        if ($userModel->isUserLoggedIn){
            Application::$app->session->setMessage("warning", "Already Logged In",
                "You are already logged in as a user.");
            return $this->redirectHome();
        }

        if ($request->isPost()) {
            $userModel->loadData($request->getBody());
            if ($userModel->register()) {
                return Application::$app->response->redirect("/");
            }
        }
        return $this->render(RegistrationView::class, [
            "model" => $userModel
        ]);
    }

    public function logout(Request $request)
    {
        $userModel = Application::$app->user;
        $userModel->logout();
//        if ($request->isPost()) {
//            $userModel->logout();
//        }
        return Application::$app->response->redirect("/");
    }

    public function userModeration(Request $request)
    {
        $model = new UserModel();
        if ($request->isPost()) {
            if (Application::$app->user->isAdminUser()) {
                $body = $request->getBody();
                $model->loadData($body);
                $model->updateUser();
            }
            return $this->redirectSameURI();
        }
        $model->getUsers();
        return $this->render(UserModerationView::class, ["model" => $model]);
    }

}