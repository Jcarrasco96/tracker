<?php

namespace app\core\services;

class Renderer
{

    private string $controllerName;

    public function __construct(string $controllerName = 'site')
    {
        $this->controllerName = $controllerName;
    }

    public function render(string $view, array $params = [], string $layout = 'main'): string
    {
        $pageTitle = $params['pageTitle'] ?? ucfirst($view);

        extract($params);

        $content = $this->renderPartial($view, $params);

        ob_start();
        require VIEWS_PATH . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php';
        return ob_get_clean() ?: die('Internal error on the server. Contact the administrator. Error 0x0025');
    }

    public function renderPartial(string $view, array $params = []): string
    {
        $viewPath = VIEWS_PATH . DIRECTORY_SEPARATOR . $this->controllerName . DIRECTORY_SEPARATOR . "$view.php";

        if (!file_exists($viewPath)) {
//            throw new Exception("The view $viewPath does not exist. 2");
            die("The view $viewPath does not exist. Contact the administrator. Error 0x0002");
        }

        extract($params);

        if (isset($params["statusCode"])) {
            http_response_code($params["statusCode"]);
        }

        ob_start();
        require $viewPath;
        return ob_get_clean() ?: die('Internal error on the server. Contact the administrator. Error 0x0026');
    }

    public static function renderStyles(array $styles): string
    {
        $html = '';
        foreach ($styles as $style) {
            $attributes = self::buildAttributes($style['attributes'] ?? []);
            $html .= "<link rel=\"stylesheet\" href=\"{$style['file']}\"$attributes>\n";
        }
        return $html;
    }

    public static function renderScripts(array $scripts): string
    {
        $html = '';
        foreach ($scripts as $script) {
            $attributes = self::buildAttributes($script['attributes'] ?? []);
            $html .= "<script src=\"{$script['file']}\"$attributes></script>\n";
        }
        return $html;
    }

    private static function buildAttributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $key";
                }
            } else {
                $html .= " $key=\"$value\"";
            }
        }
        return $html;
    }

}