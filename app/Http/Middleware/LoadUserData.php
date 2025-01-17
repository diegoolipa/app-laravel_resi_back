<?php

namespace App\Http\Middleware;

use Closure;
use stdClass;

class LoadUserData
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && !session()->has('user_session')) {
            $user = auth()->user()->load([
                'persona',
                'residenciaActiva.residencia.empresa'
            ]);

            $userData = new stdClass();
            $userData->id_usuario = $user->id;
            $userData->id_persona = $user->persona->id_persona ?? null;
            $userData->id_residencia = $user->residenciaActiva->residencia->id_residencia ?? null;
            $userData->id_empresa = $user->residenciaActiva->residencia->empresa->id_empresa ?? null;
            $userData->token = $request->bearerToken();
            $userData->nombres = $user->persona->personaNatural->nombres_completos ?? $user->persona->personaJuridica->razon_social ?? null;
            $userData->es_admin = $user->is_admin ?? false;
            $userData->valida = true;

            session(['user_session' => $userData]);
            $request->merge(['user_session' => (array) $userData]);        }

        return $next($request);
    }
}
