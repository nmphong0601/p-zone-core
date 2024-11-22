<?php

namespace PZone\Core\Front\Middleware;

use PZone\Core\Front\Models\ShopCurrency;
use Closure;
use Session;

class Currency
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
        $currency = session('currency') ?? pz_store('currency');
        if (!array_key_exists($currency, pz_currency_all_active())) {
            $currency = array_key_first(pz_currency_all_active());
        }
        ShopCurrency::setCode($currency);
        return $next($request);
    }
}
