<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\LogBeforeAfter;

class LogBeforeAfterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $log = new LogBeforeAfter;
        $log->ip = $request->ip();
        $log->ruta= $request->path();
        $log->request = json_encode($request->all());
        $log->microtimes = (float)(microtime(true));
        $log->response ="";
        $log->save();
        $request->global_log_id = $log->id;
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $cLog = LogBeforeAfter::find($request->global_log_id);
        $cLog->response = $response->getContent();
        $cLog->microtimes = (float)(microtime(true)) - (float)$cLog->microtimes;

        $cLog->save();

    }

}
