<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as RoutingController;

/**
 * Laravel 11 en adelante deja la clase base vacia. Agregamos los traits
 * de autorizacion y validacion para poder usar $this->authorize().
 */
abstract class Controller extends RoutingController
{
    use AuthorizesRequests, ValidatesRequests;
}
