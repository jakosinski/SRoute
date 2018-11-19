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
    use \ScytheStudio\Routing\InstanceTrait;

    protected $route;

    protected $server_name;

    protected $server_addr;

    protected $method;

    protected $host;

    protected $files;

    protected $ajax = false;

    protected $protocol;

    protected $inputs;

    protected $referer;

    private $path;

    



    protected function __construct() {
        $this->server_name = $_SERVER["SERVER_NAME"];
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $this->path = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/";
        $this->host = $_SERVER["HTTP_HOST"];

        $this->files = isset($_FILES) ? $_FILES : array();
        $this->inputs = isset($_POST) ? $_POST : array();

        $this->referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null;

        if($this->getReferer()) {
            $_SESSION["old_inputs_data"] = array($this->getReferer() => $this->inputs);
        }

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->ajax = true;
        }
    }

    public function matchRoute() {
        $exploded = $this->getPath() == "/" ? [] : ($this->getPath()[strlen($this->getPath())-1] == "/" ? explode("/", substr(ltrim($this->getPath(), "/"), 0, strlen(ltrim($this->getPath(), "/")) - 1)) :  explode("/", ltrim($this->getPath(), "/")));;
        foreach (SRouteCollector::instance()->getRoutes() as $route) {
            if($route->getMethod() == $this->getMethod() || $route->getMethod() == "ANY") {

                if($this->ajax && !$route->needAjax()) throw new RouterNotFoundException();
                if(!$this->ajax && $route->needAjax()) throw new RouterNotFoundException();
                if($route->needHTTPS() && $this->getProtocol() != "https://") throw new RouterNotFoundException();

                if(count($exploded) == 0 && count($route->getExplodedUrl()) == 0) {
                    return $route->invokeController();
                }

                if(count($exploded) == count($route->getExplodedUrl())) {
                    $ARGS = array();
                    $match = true;

                    foreach ($exploded as $index => $url_part) {
                        if($route->getExplodedUrl()[$index][0] == "{" && $route->getExplodedUrl()[$index][strlen($route->getExplodedUrl()[$index]) - 1] == "}") {
                            $arg = substr($route->getExplodedUrl()[$index], 1, strlen($route->getExplodedUrl()[$index]) - 2);
                            if(isset($route->getPatterns()[$arg])) {
                                if(!@preg_match($route->getPatterns()[$arg], $url_part)) {
                                    $match = false;
                                    break;
                                }
                            }
                            array_push($ARGS, $url_part);
                        }
                        else {
                            if($route->getExplodedUrl()[$index] != $url_part) {
                                $match = false;
                                break;
                            }
                            // TODO: IF INFINITY ROUTE (*)
                        }
                    }

                    if($match) {
                        return $route->invokeController($ARGS);
                    }
                }
            }
        }
        throw new RouterNotFoundException();
    }


    public function getRoute()
    {
        return $this->route;
    }

    public function getServerName()
    {
        return $this->server_name;
    }

    public function getServerAddr() {
        return $this->server_addr;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getPath() {
        return $this->path;
    }

    public function getProtocol() {
        return $this->protocol;
    }

    public function getHost() {
        return $this->host;
    }

    public function getFiles() {
        return $this->files;
    }

    public function getReferer() {
        return $this->referer;
    }

    public function file($Name) {
        return (isset($this->getFiles()[$Name])) ? $this->getFiles()[$Name] : null; 
    }

    public function getInputs() {
        return $this->inputs;
    }

    public function input($Name) {
        return (isset($this->getInputs()[$Name])) ? $this->getInputs()[$Name] : null;
    }

    public function old($Name) {
        $current_link = $this->getProtocol().$this->getHost().$this->getPath();
        return (isset($_SESSION["old_inputs_data"][$current_link][$Name])) ? $_SESSION["old_inputs_data"][$current_link][$Name] : null;
    }
}