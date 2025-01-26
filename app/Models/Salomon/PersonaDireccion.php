<?php

namespace App\Models\Salomon;

use App\Models\Gedeon\TablaBase;
use Illuminate\Support\Facades\DB;

class PersonaDireccion extends TablaBase
{
    protected $table = 'salomon.persona_direcciones';
    protected $primaryKey = 'id_persona_direccion';

    protected $fillable = [
        'id_persona',
        'direccion',
        'distrito',
        'provincia',
        'departamento',
        'pais',
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
    public static function setPrincipal($idPersona, $idDireccion)
    {
        DB::transaction(function () use ($idPersona, $idDireccion) {
            // Quitar principal de otras direcciones
            self::where('id_persona', $idPersona)
                ->where('es_principal', true)
                ->update(['es_principal' => false]);

            // Establecer la nueva dirección principal
            self::where('id_persona_direccion', $idDireccion)
                ->update(['es_principal' => true]);
        });
    }
}
