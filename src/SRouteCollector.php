<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 16.11.2018
 * Time: 23:47
 */

namespace ScytheStudio\Routing;


use ScytheStudio\Routing\Exceptions\RouterNameMustBeUnique;

class SRouteCollector {
    use \ScytheStudio\Routing\InstanceTrait;
    
    protected $routes;

    private function __construct() {
        $this->routes = array();
    }

    public function add(SRoute $SRoute) {
        foreach ($this->getRoutes() as $route) {
            if($route != $SRoute) {
                if ($SRoute->getName() != "") {
                    if ($route->getName() == $SRoute->getName()) {
                        throw new RouterNameMustBeUnique();
                    }
                }
            }
        }
        array_push($this->routes, $SRoute);
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function length() {
        return count($this->routes);
    }

    public function getRoute($Name) {
        foreach($this->getRoutes() as $route) {
            if($route->getName() == $Name) {
                return $route;
            }
        }

        return null;
    }
}