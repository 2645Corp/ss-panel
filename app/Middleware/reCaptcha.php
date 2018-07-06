<?php

namespace App\Middleware;

use App\Services\Auth as AuthService;
use App\Utils\Helper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class reCaptcha
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        if (Helper::isLocal()) {
            $response = $next($request, $response);
            return $response;
        }
        if (!AuthService::checkReCaptcha()) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/reCaptcha');
            return $newResponse;
        }
        $response = $next($request, $response);
        return $response;
    }
}