<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Checkjwt
{
    protected $except = [
        'POST:login',
        'POST:logout',
        'POST:register',
        'POST:api/client',
        'GET:api/produit',
        'GET:api/event',
        'GET:api/coach',
        'GET:api/offres',
        'GET:api/offres/{id}',
        'GET:api/specialite',
        'GET:specialite.show',
        'GET:api/produit/{id}',
        'GET:api/personal_trainies',
        'GET:api/personal_trainies/{id}',
        'POST:api/reservationcontroller',
        'POST:api/inscription_event',
        'POST:api/commandecontroller',
        'POST:api/inscription_offrecontroller',
        'POST:api/storeClient',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    protected function shouldPassThrough($request)
    {
        // Vérifiez si la route est dans la liste des routes exclues
        $currentRoute = $request->route()->getName();
        $currentMethod = $request->getMethod();
    
        foreach ($this->except as $except) {
            list($method, $route) = explode(':', $except);
    
            if ($currentMethod === $method && ($currentRoute === $route || $request->is($route))) {
                return true;
            }
        }
    
        return false;
    }
}
