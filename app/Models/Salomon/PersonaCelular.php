<?php

namespace App\Models\Salomon;

use App\Models\TablaBase;
use Illuminate\Support\Facades\DB;


class PersonaCelular extends TablaBase
{
    protected $table = 'salomon.persona_celulares';
    protected $primaryKey = 'id_persona_celular';

    protected $fillable = [
        'id_persona',
        'numero_celular',
        'es_principal'
    ];

    protected $casts = [
        'es_principal' => 'boolean'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    // Método para establecer como principal
    public static function setPrincipal($idPersona, $idCelular)
    {
        DB::transaction(function () use ($idPersona, $idCelular) {
            // Quitar principal de otros celulares
            self::where('id_persona', $idPersona)
                ->where('es_principal', true)
                ->update(['es_principal' => false]);

            // Establecer el nuevo celular principal
            self::where('id_persona_celular', $idCelular)
                ->update(['es_principal' => true]);
        });
    }
}
