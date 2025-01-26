<?php

namespace App\Models\Salomon;

use App\Models\Gedeon\TablaBase;
use Illuminate\Support\Facades\DB;

class TipoDocumento extends TablaBase
{
    protected $connection = 'pgsql';  // Especifica la conexión
    protected $table = 'salomon.tipo_documentos';  // Especifica el esquema completo
    protected $primaryKey = 'id_tipo_documento';

    protected $fillable = [
        'nombre',
        'sigla',
        'es_persona_natural',
        'regla',
        'orden',

        'estado',
        'usuario_creacion',
        'usuario_actualizacion'
    ];

    protected $casts = [
        'es_persona_natural' => 'boolean'
    ];

    // Relación con PersonaDocumento
    public function personaDocumentos()
    {
        return $this->hasMany(PersonaDocumento::class, 'id_tipo_documento');
    }

    // Scope para documentos de personas naturales
    public function scopePersonaNatural($query)
    {
        return $query->where('es_persona_natural', true);
    }

    // Scope para documentos de personas jurídicas
    public function scopePersonaJuridica($query)
    {
        return $query->where('es_persona_natural', false);
    }

    // Scope para ordenar
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden');
    }

    // Scope para activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }
}
