<?php

namespace App\Http\Middleware;

use Closure;
use App\Libraries\XmppPebind\XmppPrebind;

class XamppSession
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
        $xmppPrebind = new XmppPrebind('192.168.0.32', 'http://192.168.0.32:7070/http-bind/', '', false, false);
        $xmppPrebind->connect('1', '12345678');
        $xmppPrebind->auth();
        $sessionInfo = $xmppPrebind->getSessionInfo();
        session($sessionInfo);
        return $next($request);
    }
}
