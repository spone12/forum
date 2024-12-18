<?php

namespace App\Http\Middleware;

use App\Models\ApiLogs;
use Closure;
use Illuminate\Http\Request;
use Throwable;

class ApiLogger
{

    protected $exludedFields = ['password'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        try {
            $response = $next($request);
        } catch (Throwable $e) {
            $this->logRequest($request, $response);
            throw $e;
        }

        $this->logRequest($request, $response);
        return $response;
    }

    private function logRequest(Request $request, $response) {

        $filteredRequest = $request->except($this->exludedFields);
        ApiLogs::create([
            'user_id' => auth()->id(),
            'route' => $request->path(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'headers' => json_encode($request->headers->all()),
            'request_body' => json_encode($filteredRequest),
            'status_code' => $response->getStatusCode(),
            'response_body' => $response->getContent(),
        ]);
    }
}
