<?php


namespace app\core;


abstract class View
{
    const MAIN = "main";
    const FULLPAGE = "fullpage";
    protected string $layout = self::MAIN;
    protected string $template;
    protected string $content = "";
    protected string $layoutContent = "";
    protected string $extra_js = "";
    protected string $extra_css = "";
    protected string $extra_header = "";
    protected array $placeholders;
    public bool $putTitle = true;

    public function __construct()
    {
        $this->setDefaultTemplate();
        $this->placeholders = ["content", "extra_js", "extra_css", "extra_header"];
    }

    public function render($params = [])
    {
        return $this->renderView($params);
    }

    public function renderView(array $params = [])
    {
        $params["view"] = $this;
        $this->layoutContent = $this->layoutContent($params);
        $this->content = $this->renderOnlyView($params);
        $this->replacePlaceholders();
        return $this->layoutContent;
    }

    private function replacePlaceholders()
    {
        foreach ($this->placeholders as $ph) {
            if (property_exists($this, $ph)) {
                $this->layoutContent = str_replace("{{" . $ph . "}}", $this->$ph, $this->layoutContent);
            }
        }
    }

    protected function layoutContent($params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include_once ROOT_DIR . "/views/layouts/" . "{$this->getLayout()}.php";
        return ob_get_clean();
    }

    protected function renderOnlyView($params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include_once ROOT_DIR . "/views/templates/" . "{$this->getTemplate()}.php";
        return ob_get_clean();
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template): void
    {
        $this->template = $template;
    }

    private function setDefaultTemplate()
    {
        $viewPath = explode('\\', static::class);
        $this->setTemplate(str_replace("View", "Template", end($viewPath)));

    }

    protected function loadExtraCSS()
    {
        ob_start(); ?>
        <?php
        $this->extra_css = ob_get_clean();
        ob_flush();
    }

    protected function loadExtraJS()
    {
        ob_start(); ?>
        <?php
        $this->extra_js = ob_get_clean();
        ob_flush();
    }

    public function getUserOptions()
    {
//        $publicRoutes = Application::$app->router->getPermittedRoutes(Router::PERMISSION_PUBLIC);
        $userRoutes = Application::$app->router->getPermittedRoutes(Router::PERMISSION_USER);
        $userRoutes = array_filter($userRoutes, fn($u) => $u["namespace"]!="logout");
        $adminRoutes = Application::$app->router->getPermittedRoutes(Router::PERMISSION_ADMIN);
        return [$userRoutes, $adminRoutes];
    }

    public function getTitle()
    {
        $route = Application::$app->router->getRoute();
        return $route ? $route["title"] : "Not Found";
    }

    public function getButtonFromNamespace($namespace, $extraClass="btn-outline-secondary mx-1")
    {
        $route = Application::$app->router->getRouteFromNamespace($namespace);
        return $route ? "<a class='btn btn-sm $extraClass' href='{$route['path']}'>{$route["title"]}</a>" : "";
    }



}