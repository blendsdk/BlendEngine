<?php

namespace Blend\Component\Support;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Blend\Component\Routing\Route;

abstract class RedirectController
{

    public abstract function redirect(
    $routeName
    , Route $route
    , Request $request
    , UrlGeneratorInterface $urlGenerator);
}
