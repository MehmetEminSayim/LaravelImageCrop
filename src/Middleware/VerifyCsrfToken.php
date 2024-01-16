<?php

namespace Imagecrop\Mehmeteminsayim\Middleware;

use IlluminateFoundationHttpMiddlewareVerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "upload"
    ];
}
