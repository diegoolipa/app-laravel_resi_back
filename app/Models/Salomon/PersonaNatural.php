<?php

namespace App\Models\Salomon;

use App\Models\Gedeon\TablaBase;
use Illuminate\Support\Facades\DB;

class PersonaNatural extends TablaBase
{
    protected $table = 'salomon.persona_naturales';
    protected $primaryKey = 'id_persona';
    public $incrementing = false; // Importante porque usamos llave foránea como PK

    protected $fillable = [
        'id_persona',  // Añade id_persona a los fillable

        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'id_tipo_genero',
        'id_tipo_estado_civil',
        'nombres_completos'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date'
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    // Relación con TipoGenero
    public function genero()
    {
        return $this->belongsTo(TipoGenero::class, 'id_tipo_genero');
    }

    // Relación con TipoEstadoCivil
    public function estadoCivil()
    {
        return $this->belongsTo(TipoEstadoCivil::class, 'id_tipo_estado_civil');
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    // Accessor para nombre abreviado
    public function getNombreAbreviadoAttribute()
    {
        return trim("{$this->nombres} {$this->apellido_paterno}");
    }
}
