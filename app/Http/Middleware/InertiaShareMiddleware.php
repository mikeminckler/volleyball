<?php

namespace App\Http\Middleware;

use Closure;

use Inertia\Inertia;
use Illuminate\Support\Facades\Cookie;

class InertiaShareMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Inertia::share([

            'app' => [
            ],
            
            'auth' => function () {
                if (auth()->check()) {
                    return [
                        'user' => auth()->user()->load('roles'),
                    ];
                } else {
                    return ['user' => null];
                }
            },

            //'menu' => auth()->check() ? Menu::getMenu() : null,

            'cookies' => [
                'login_email' => Cookie::get('login-email'),
            ],
            
            'csrf_token' => csrf_token(),

        ]);


        return $next($request);
    }
}
