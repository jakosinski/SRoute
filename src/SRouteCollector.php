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
    /**
     * @var
     */
    protected static $_instance;
    /**
     * @var array
     */
    protected $routes;

    /**
     * SRouteCollector constructor.
     */
    public function __construct() {
        $this->routes = array();
    }

    /**
     * @return mixed
     */
    public static function instance() {
        if (static::$_instance  === null)  static::$_instance = new static;

        return static::$_instance;
    }

    /**
     * @param \ScytheStudio\Routing\SRoute $SRoute
     * @throws RouterNameMustBeUnique
     */
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

    /**
     * @return array
     */
    public function getRoutes() {
        return $this->routes;
    }
}