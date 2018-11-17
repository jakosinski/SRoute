<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 16.11.2018
 * Time: 23:59
 */

namespace ScytheStudio\Routing;

class RouteHelper {
    /**
     * @var
     */
    static $_instance;

    /**
     * @return mixed
     */
    public static function instance() {
        if (static::$_instance  === null)  static::$_instance = new static;

        return static::$_instance;
    }


    /**
     * @param $Name
     * @param array $parameters
     * @return string
     */
    public function getRouteUrl($Name, $parameters = array()) {
        $router = null;

        foreach (SRouteCollector::instance()->getRoutes() as $route) {
            if($route->getName() == $Name) {

                $router = $route;
                break;
            }
        }

        if($router == null) return "";
        if($router->getURL() == "/") return $router->getURL();

        $exploded = array();


        foreach ($router->getExplodedURL() as $ex_url) {
            if($ex_url[0] == "{" && $ex_url[strlen($ex_url) - 1] == "}") {
                if(!isset($parameters[substr($ex_url, 1, strlen($ex_url)-2)])) {
                    array_push($exploded, $ex_url);
                }
                else {
                    array_push($exploded, $parameters[substr($ex_url, 1, strlen($ex_url)-2)]);
                }
            }
            else {
                array_push($exploded, $ex_url);
            }

        }

        $url = "";
        foreach ($exploded as $url_part) {
            $url = $url.$url_part."/";
        }

        return $url == "" ? "/" : "/".$url;
    }
}
