<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

use App\Services\DetikConnectService;

class DetikConnect
{
    /**
     * The Detik Service instance.
     *
     * @var \App\Services\DetikConnectService;
     */
    protected $detikService;

    public function __construct()
    {
        $this->detikService = new DetikConnectService('backend');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $sessionId  = $this->detikService->getSessionId();
        $userDetail = $this->detikService->getUserIdentityBySession();

        // invalid sessionid (invalid dts-g ketika kondisi prod dipindah ke devel)
        if ($this->detikService->getIdentityStatus('error')) {
            $sessionId = null;
        }

        $role = $this->detikService->getRole();

        $requestUrl   = $_SERVER['REQUEST_URI'];
        $loginUrl     = config('detikconnect.url.login');
        $nextUrl      = config('detikconnect.url.dashboard');
        $fullLoginUrl = route('backend.login');

        // sessionid kosong
        if (empty($sessionId)) {
            if ($requestUrl == $loginUrl) {
                return $next($request);
            }

            return redirect($fullLoginUrl);
        }

        // is not staff
        if (!$role) {
            if ($request->requestUri == $loginUrl) {
                return $next($request);
            }

            return redirect($fullLoginUrl)
                ->with('error', 'user not registered!');
        }

        if (!empty($sessionId) && $role) {
            View::share([
                'role'           => strtolower($this->detikService->getRole()),
                'username'       => $userDetail['name'] ?? $userDetail['username'],
                'profilePicture' => $userDetail['profilePicture'],
            ]);

            // set session
            if ($request->session()->get('role') != $role) {
                $request->session()->put('role', $role);
            }

            $request->session()->put('username', $userDetail['username'] ?? NULL );
            $request->merge(['identity' => $userDetail]);
            if ($requestUrl == $nextUrl || $requestUrl != $loginUrl) {
                return $next($request);
            }

            return redirect($nextUrl);
        }
    }
}
