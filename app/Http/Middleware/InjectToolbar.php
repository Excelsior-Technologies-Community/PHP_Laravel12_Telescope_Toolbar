<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectToolbar
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $executionTime = number_format(($endTime - $startTime) * 1000, 2); // ms
        $memoryUsage = number_format(memory_get_peak_usage(true) / 1024 / 1024, 2); // MB

        if (
            app()->environment('local') &&
            $response instanceof Response &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {
            $toolbar = view('toolbar', [
                'time' => now(),
                'executionTime' => $executionTime,
                'memoryUsage' => $memoryUsage,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'status' => $response->getStatusCode(),
            ])->render();

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