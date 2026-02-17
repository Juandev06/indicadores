<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store;


class AutoLogout
{
    protected $session;
    protected $timeout = 12;

    public function __construct(Store $session)
    {
        $this->session = $session;
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
        $is_logged_in = $request->path() != 'logout';

        if(!session('LoggerUser')) {
            $this->session->put('LoggerUser', time());
        } elseif(time() - $this->session->get('LoggerUser') > $this->timeout) {
            $this->session->forget('LoggerUser');
            $cookie = cookie('intend', $is_logged_in ? url()->current() : 'home');
            auth()->logout();
        }

        $is_logged_in ? $this->session->put('LoggerUser', time()) : $this->session->forget('LoggerUser');
        
        return $next($request);
    }
}
