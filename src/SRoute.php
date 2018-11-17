<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 14.11.2018
 * Time: 20:06
 */

namespace ScytheStudio\Routing;

use ScytheStudio\Routing\Exceptions\RouterNameMustBeString;
use ScytheStudio\Routing\Exceptions\ControllerMustBeFunctionOrClass;
use ScytheStudio\Routing\Exceptions\InvalidMiddleware;

/**
 * Class SRoute
 * @package ScytheStudio\Routing
 */
class SRoute {

    /**
     * @var
     */
    private $_name;
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $exploded_url;
    /**
     * @var string
     */
    private $method;
    /**
     * @var array
     */
    private $controller;
    /**
     * @var int
     */
    public $controllerType;
    /**
     * @var array
     */
    public $middlewares = [];
    /**
     * @var bool
     */
    private $ajax = false;

    /**
     * SRoute constructor.
     * @param string $method
     * @param $url
     * @param $controller
     * @throws ControllerMustBeFunctionOrClass
     */
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

    /**
     * @param $name
     * @return $this
     * @throws RouterNameMustBeString
     */
    public function name($name) {
        if(!is_string($name)) {
            throw new RouterNameMustBeString();
        }

        $this->_name = $name;

        return $this;
    }

    /**
     * @param $middleware
     * @return $this
     * @throws InvalidMiddleware
     */
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

    /**
     * @param array $ARGS
     * @return mixed
     */
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
                return $function(...$ARGS);
            case 1:
                $class = new $this->controller["CLASS"];
                $method = $this->controller["METHOD"];
                return $class->$method(...$ARGS);
        }
    }

    /**
     *
     */
    public function save() {
        SRouteCollector::instance()->add($this);
    }

    /**
     * @return $this
     */
    public function ajax() {
        $this->ajax = true;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @return array
     */
    public function getExplodedUrl() {
        return $this->exploded_url;
    }

    /**
     * @return string
     */
    public function getURL() {
        return $this->url;
    }


    /**
     * @return bool
     */
    public function needAjax() {
        return $this->ajax;
    }

    /**
     * @param $url
     * @param $controller
     * @return SRoute
     * @throws ControllerMustBeFunctionOrClass
     */
    public static function get($url, $controller) {
        return new static("GET", $url, $controller);
    }

    /**
     * @param $url
     * @param $controller
     * @return SRoute
     * @throws ControllerMustBeFunctionOrClass
     */
    public static function post($url, $controller) {
        return new static("POST", $url, $controller);
    }

    /**
     * @param $url
     * @param $controller
     * @return SRoute
     * @throws ControllerMustBeFunctionOrClass
     */
    public static function put($url, $controller) {
        return new static("PUT", $url, $controller);
    }

    /**
     * @param $url
     * @param $controller
     * @return SRoute
     * @throws ControllerMustBeFunctionOrClass
     */
    public static function delete($url, $controller) {
        return new static("DELETE", $url, $controller);
    }

    /**
     * @param $url
     * @param $controller
     * @return SRoute
     * @throws ControllerMustBeFunctionOrClass
     */
    public static function any($url, $controller) {
        return new static("ANY", $url, $controller);
    }
}