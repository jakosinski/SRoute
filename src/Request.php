<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 14.11.2018
 * Time: 17:15
 */

namespace ScytheStudio\Routing;


use ScytheStudio\Routing\Exceptions\RouterNotFoundException;

class Request {
    /**
     * @var
     */
    protected static $_instance;

    /**
     * @var
     */
    protected $route;
    /**
     * @var
     */
    protected $server_name;
    /**
     * @var
     */
    protected $server_addr;
    /**
     * @var
     */
    protected $method;
    /**
     * @var string
     */
    private $path;
    /**
     * @var array
     */
    public $back = array();
    /**
     * @var bool
     */
    protected $ajax = false;


    /**
     * Request constructor.
     */
    public function __construct() {
        $this->server_name = $_SERVER["SERVER_NAME"];
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->path = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/";
        $this->back = array(
          "HTTP_REFERER" => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null,
        );

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->ajax = true;
        }
    }

    /**
     * @return mixed
     */
    public static function instance() {
        if (static::$_instance  === null)  static::$_instance = new static;

        return static::$_instance;
    }


    /**
     * @return mixed
     * @throws RouterNotFoundException
     */
    public function matchRoute() {
        $exploded = $this->getPath() == "/" ? [] : ($this->getPath()[strlen($this->getPath())-1] == "/" ? explode("/", substr(ltrim($this->getPath(), "/"), 0, strlen(ltrim($this->getPath(), "/")) - 1)) :  explode("/", ltrim($this->getPath(), "/")));;
        foreach (SRouteCollector::instance()->getRoutes() as $route) {
            if($route->getMethod() == $this->getMethod() || $route->getMethod() == "ANY") {

                if($this->ajax && !$route->needAjax()) throw new RouterNotFoundException();
                if(!$this->ajax && $route->needAjax()) throw new RouterNotFoundException();

                if(count($exploded) == 0 && count($route->getExplodedUrl()) == 0) {
                    return $route->invokeController();
                    die();
                }

                if(count($exploded) == count($route->getExplodedUrl())) {
                    $ARGS = array();
                    $match = true;

                    foreach ($exploded as $index => $url_part) {
                        if($route->getExplodedUrl()[$index][0] == "{" && $route->getExplodedUrl()[$index][strlen($route->getExplodedUrl()[$index]) - 1] == "}") {
                           array_push($ARGS, $url_part);
                        }
                        else {
                            if(!$route->getExplodedUrl()[$index] == $url_part) {
                                $match = false;
                                break;
                            }
                            // TODO: IF INFINITY ROUTE (*)
                        }
                    }

                    if($match) {
                        return $route->invokeController($ARGS);
                        exit();
                    }
                }
            }
        }
        throw new RouterNotFoundException();
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return mixed
     */
    public function getServerName()
    {
        return $this->server_name;
    }

    /**
     * @return mixed
     */
    public function getServerAddr()
    {
        return $this->server_addr;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getBack()
    {
        return $this->back;
    }
}