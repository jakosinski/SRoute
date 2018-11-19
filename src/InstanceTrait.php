<?php

namespace ScytheStudio\Routing;

trait InstanceTrait {
    private static $_instance;


    public function instance() {
            if(self::$_instance === null) {
                self::$_instance = new static;
            }

        return self::$_instance;
    }

    private function __clone() {}
    private function __wakeup() {}
}