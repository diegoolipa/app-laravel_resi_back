<?php

namespace App\Http\Middleware;

use App\Models\Gedeon\Anio;
use Closure;
use stdClass;

class LoadUserData
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && !session()->has('user_session')) {

            $user = auth()->user()->load([
                'persona.personaNatural',
                'persona.personaJuridica',
                'departamentoActual.departamento.entidad.empresa'
            ]);

            $userData = new stdClass();
            $userData->id_usuario = $user->id;
            $userData->id_persona = $user->persona->id_persona ?? null;
            $userData->id_departamento = $user->departamentoActual->id_departamento ?? null;
            $userData->id_entidad = $user->departamentoActual->departamento->entidad->id_entidad ?? null;
            $userData->id_empresa = $user->departamentoActual->departamento->entidad->empresa->id_empresa ?? null;
            $userData->id_anio = Anio::where('es_activo', true)->where('estado', 1)->value('id_anio');
            $userData->token = $request->bearerToken();
            $userData->nombres = $user->persona->personaNatural->nombres_completos ??
                               $user->persona->personaJuridica->razon_social ?? null;
            $userData->es_admin = $user->is_admin ?? false;
            $userData->valida = true;

            session(['user_session' => $userData]);
            $request->merge(['user_session' => (array) $userData]);
        }

        return $next($request);
    }
}
