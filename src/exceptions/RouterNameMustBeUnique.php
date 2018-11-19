<?php
/**
 * Created by ScytheStudio.
 * User: Jakub Kosiński
 * Date: 16.11.2018
 * Time: 23:55
 */

namespace ScytheStudio\Routing\Exceptions;

use Exception;
use Throwable;

class RouterNameMustBeUnique extends Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        die();
    }
}