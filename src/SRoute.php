<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 14.11.2018
 * Time: 20:06
 */

namespace ScytheStudio\Routing;

use ScytheStudio\Routing\Request;
use ScytheStudio\Routing\Exceptions\RouterNameMustBeString;
use ScytheStudio\Routing\Exceptions\ControllerMustBeFunctionOrClass;
use ScytheStudio\Routing\Exceptions\InvalidMiddleware;

/**
 * Class SRoute
 * @package ScytheStudio\Routing
 */
class SRoute {

    private $_name;

    private $url;

    private $exploded_url;

    private $method;

    private $controller;

    private $controllerType;

    private $middlewares = [];

    private $ajax = false;

    private $https = false;

    private $patterns = array();

    protected function __construct($method = "GET", $url, $controller) {
        $this->method = $method;
        $this->url = $url == "" ? "/" : $url;
        $this->exploded_url = $this->url == "/" ? [] : ($this->url[strlen($this->url)-1] == "/" ? explode("/", substr(ltrim($this->url, "/"), 0, strlen(ltrim($this->url, "/")) - 1)) :  explode("/", ltrim($this->url, "/")));

        if(!is_string($controller)) {
            if(!is_callable($controller)) {
                throw new ControllerMustBeFunctionOrClass();
            }

            $this->controller = $controller;
            $this->controllerType = 0;
        }
        else {
            if(!strpos($controller, '@')) {
                throw new ControllerMustBeFunctionOrClass();
            }

            $contr = explode("@", $controller);

            if(!is_callable(array($contr[0], $contr[1]))) {
                throw new ControllerMustBeFunctionOrClass();
            }

            $this->controllerType = 1;
            $this->controller = array("CLASS" => $contr[0], "METHOD" => $contr[1]);
        }
    }


    public function name($name) {
        if(!is_string($name)) {
            throw new RouterNameMustBeString();
        }

        $this->_name = $name;

        return $this;
    }

    public function middleware($middleware) {
        if(!is_string($middleware)) {
            throw new InvalidMiddleware();
        }

        if(!is_callable(array($middleware, "handle"))) {
            throw new InvalidMiddleware();
        }

        array_push($this->middlewares, $middleware);

        return $this;
    }

    public function where($Args) {
        $this->patterns = $Args;
        return $this;
    }

    public function invokeController(ARRAY $ARGS = array()) {
        foreach ($this->middlewares as $middleware) {
            $middleware = new $middleware();
            if(!$middleware->handle()) {
                die();
            }
        }
        switch ($this->controllerType) {
            case 0:
                $function = $this->controller;
                $function(...$ARGS);
                break;
            case 1:
                $class = new $this->controller["CLASS"];
                $method = $this->controller["METHOD"];
                $class->$method(...$ARGS);
                break;
        }

        $current_link = Request::instance()->getProtocol().Request::instance()->getHost().Request::instance()->getPath();
        if(isset($_SESSION["old_inputs_data"][$current_link])) {
            if(Request::instance()->getMethod() == "GET") {
                unset($_SESSION["old_inputs_data"][$current_link]);
            }
        }
    }

    public function save() {
        SRouteCollector::instance()->add($this);
    }

    public function ajax() {
        $this->ajax = true;
        return $this;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getName() {
        return $this->_name;
    }

    public function getExplodedUrl() {
        return $this->exploded_url;
    }

    public function getURL() {
        return $this->url;
    }

    public function https() {
        $this->https = true;
        return $this;
    }

    public function needHTTPS() {
        return $this->https;
    }
    
    public function needAjax() {
        return $this->ajax;
    }

    public function getPatterns() {
        return $this->patterns;
    }

    public function getControllerType() {
        return $this->controllerType;
    }

    public static function get($url, $controller) {
        return new static("GET", $url, $controller);
    }

    public static function post($url, $controller) {
        return new static("POST", $url, $controller);
    }

    public static function put($url, $controller) {
        return new static("PUT", $url, $controller);
    }

    public static function delete($url, $controller) {
        return new static("DELETE", $url, $controller);
    }

    public static function any($url, $controller) {
        return new static("ANY", $url, $controller);
    }
}