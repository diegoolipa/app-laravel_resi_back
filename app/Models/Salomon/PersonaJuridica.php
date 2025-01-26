<?php

namespace App\Models\Salomon;

use App\Models\Gedeon\TablaBase;
use Illuminate\Support\Facades\DB;

class PersonaJuridica extends TablaBase
{
    protected $table = 'salomon.persona_juridicas';
    protected $primaryKey = 'id_persona';
    public $incrementing = false; // Importante porque usamos llave foránea como PK

    protected $fillable = [
        'id_persona',
        'razon_social',
        'nombre_comercial',
        'sitio_web'
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    // Accessor para nombre de visualización
    public function getNombreDisplayAttribute()
    {
        return $this->nombre_comercial ?? $this->razon_social;
    }
}
