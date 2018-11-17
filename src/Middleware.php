<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 16.11.2018
 * Time: 23:36
 */

namespace ScytheStudio\Routing;


/**
 * Interface Middleware
 * @package ScytheStudio
 */
interface Middleware {
    /**
     * @return mixed
     */
    public function handle();
}