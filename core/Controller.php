<?php


namespace app\core;

abstract class Controller
{
    public function render($view, $params = [])
    {
        $view = new $view();
        return $view->render($params);
    }

    public function loginRequired()
    {
        if (!Application::$app->user->isUserLoggedIn){
            Application::$app->response->redirect("/login");
            Application::$app->session->setMessage("warning", "Login Required",
                "You need to login to access the requested page");
            exit();
        }
    }

    public function currentUserID()
    {
        return Application::$app->user->id->getValue();
    }

    public function redirect($path)
    {
        return Application::$app->response->redirect($path);
    }

    public function redirectHome()
    {
        return Application::$app->response->redirect("/");
    }

    public function redirectSameURI()
    {
        return Application::$app->response->redirect(Application::$app->request->getOnlyPath());
    }
}