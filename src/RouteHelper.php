<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 16.11.2018
 * Time: 23:59
 */

namespace ScytheStudio\Routing;

class RouteHelper {
    use \ScytheStudio\Routing\InstanceTrait;



    public function getRouteUrl($Name, $parameters = array()) {
        $router = SRouteCollector::instance()->getRoute($Name);
        $server = Request::instance()->getProtocol().Request::instance()->getHost();
        if($router == null) return "";
        if($router->getURL() == "/") return $server.$router->getURL();

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

        return $url == "" ? $server."/" : $server."/".$url;
    }
}
