<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectToolbar
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            app()->environment('local') &&
            $response instanceof Response &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {
            $toolbar = view('toolbar')->render();

            $content = $response->getContent();

            $content = str_replace(
                '</body>',
                $toolbar . '</body>',
                $content
            );

            $response->setContent($content);
        }

        return $response;
    }
}