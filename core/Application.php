<?php


namespace app\core;


use app\models\CategoryModel;
use app\models\UserModel;

class Application
{
    public static Application $app;
    public Router $router;
    public Request $request;
    public Response $response;
    private Controller $controller;
    public Session $session;
    public Database $db;
    public UserModel $user;
    public CategoryModel $category;



    public function __construct()
    {
        $config = $this->loadConf();
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config["db"]);
        $this->user = new UserModel();
        $this->user->verifyUser();
        $this->category = new CategoryModel();
        $this->category->selectCategories();
    }

    public function run()
    {
        echo $this->router->resolve();
    }

    public function getController(): Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    public function loadConf()
    {
        return [
            'db' => [
                'dsn' => $_ENV["DB_DSN"],
                'username' => $_ENV["DB_USER"],
                'password' => $_ENV["DB_PASSWORD"],

            ]
        ];
    }

    public function getAuthors()
    {
        $json = json_decode(file_get_contents(ROOT_DIR."/composer.json"), true);
        return $json["authors"];
    }
}