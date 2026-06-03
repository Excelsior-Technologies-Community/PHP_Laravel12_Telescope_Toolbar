<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InjectToolbar
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);

        if (!$request->acceptsHtml() || $request->is('telescope*') || $request->ajax()) {
            return $response;
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        $queriesCount = 0;
        $duplicateQueries = 0;
        $queriesList = [];

        if (Schema::hasTable('telescope_entries')) {
            $queries = DB::table('telescope_entries')
                ->where('type', 'query')
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get();

            $queriesCount = $queries->count();
            $sqlCounts = [];

            foreach ($queries as $q) {
                $content = json_decode($q->content, true);
                if (isset($content['sql'])) {
                    $sql = $content['sql'];
                    $sqlCounts[$sql] = ($sqlCounts[$sql] ?? 0) + 1;
                    $queriesList[] = [
                        'sql' => $sql,
                        'time' => $content['time'] ?? 0
                    ];
                }
            }

            foreach ($sqlCounts as $sql => $count) {
                if ($count > 1) {
                    $duplicateQueries += ($count - 1);
                }
            }
        }

        $payload = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => array_map(fn($h) => $h, $request->headers->all()),
            'session' => $request->hasSession() ? $request->session()->all() : [],
            'input' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
        ];

        $toolbarHtml = view('toolbar', [
            'time' => $executionTime,
            'memory' => $memoryUsage,
            'queriesCount' => $queriesCount,
            'duplicateQueries' => $duplicateQueries,
            'queriesList' => $queriesList,
            'payload' => $payload
        ])->render();

        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if ($pos !== false) {
            $content = substr($content, 0, $pos) . $toolbarHtml . substr($content, $pos);
            $response->setContent($content);
        }

        return $response;
    }
}