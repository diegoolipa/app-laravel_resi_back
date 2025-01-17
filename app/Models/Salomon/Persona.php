<?php

namespace App\Models\Salomon;

use App\Models\TablaBase;
use App\Models\User;

class Persona extends TablaBase
{
    protected $table = 'salomon.personas';
    protected $primaryKey = 'id_persona';

    protected $fillable = [
        'tipo_persona',
        'id_usuario'
    ];

    // Relación con User
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario'); // users.id -> persona.id_usuario
    }

    // Relación con documentos
    public function documentos()
    {
        return $this->hasMany(PersonaDocumento::class, 'id_persona');
    }

    // Método para buscar documento específico
    public function getDocumento($tipoDocumentoId)
    {
        return $this->documentos()
            ->where('id_tipo_documento', $tipoDocumentoId)
            ->first();
    }

    // Método para asignar documento
    public function asignarDocumento($tipoDocumentoId, $numeroDocumento)
    {
        return $this->documentos()->create([
            'id_tipo_documento' => $tipoDocumentoId,
            'numero_documento' => $numeroDocumento
        ]);
    }

    // Relación con datos naturales
    public function personaNatural()
    {
        return $this->hasOne(PersonaNatural::class, 'id_persona');
    }

    // Relación con datos jurídicos
    public function personaJuridica()
    {
        return $this->hasOne(PersonaJuridica::class, 'id_persona');
    }

    // Relación con dirección
    public function direcciones()
    {
        return $this->hasMany(PersonaDireccion::class, 'id_persona');
    }

    // Relación con celular
    public function celulars()
    {
        return $this->hasMany(PersonaCelular::class, 'id_persona');
    }

    // Relación con correo
    public function correos()
    {
        return $this->hasMany(PersonaCorreo::class, 'id_persona');
    }

    // Obtener celular principal
    public function correoPrincipal()
    {
        return $this->hasOne(PersonaCorreo::class, 'id_persona')
            ->where('es_principal', true)
            ->where('estado', 1);
    }

    // Obtener celular principal
    public function celularPrincipal()
    {
        return $this->hasOne(PersonaCelular::class, 'id_persona')
            ->where('es_principal', true)
            ->where('estado', 1);
    }

    // Obtener dirección principal
    public function direccionPrincipal()
    {
        return $this->hasOne(PersonaDireccion::class, 'id_persona')
            ->where('es_principal', true)
            ->where('estado', 1);
    }
}
