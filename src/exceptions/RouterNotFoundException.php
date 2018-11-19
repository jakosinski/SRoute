<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 16.11.2018
 * Time: 22:53
 */

namespace ScytheStudio\Routing\Exceptions;

use Exception;
use Throwable;

class RouterNotFoundException extends Exception{
    public function __construct($message = "", $code = 404, Throwable $previous = null)
    {
        http_response_code(404);
        parent::__construct($message, $code, $previous);
        die();
    }
}