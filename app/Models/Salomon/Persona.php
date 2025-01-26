<?php

namespace App\Models\Salomon;

use App\Models\Gedeon\TablaBase;
use App\Models\User;
use App\Models\Gedeon\Anio;

class Persona extends TablaBase
{
    protected $table = 'salomon.personas';
    protected $primaryKey = 'id_persona';

    protected $fillable = [
        'tipo_persona',
        'id_usuario'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function documentos()
    {
        return $this->hasMany(PersonaDocumento::class, 'id_persona');
    }

    public function getAnioActivo()
    {
        return Anio::where('es_activo', true)
            ->where('estado', 1)
            ->first();
    }

    public function personaNatural()
    {
        return $this->hasOne(PersonaNatural::class, 'id_persona');
    }

    public function personaJuridica()
    {
        return $this->hasOne(PersonaJuridica::class, 'id_persona');
    }

    public function direcciones()
    {
        return $this->hasMany(PersonaDireccion::class, 'id_persona');
    }

    public function celulars()
    {
        return $this->hasMany(PersonaCelular::class, 'id_persona');
    }

    public function correos()
    {
        return $this->hasMany(PersonaCorreo::class, 'id_persona');
    }

    public function correoPrincipal()
    {
        return $this->hasOne(PersonaCorreo::class, 'id_persona')
            ->where('es_principal', true)
            ->where('estado', 1);
    }

    public function celularPrincipal()
    {
        return $this->hasOne(PersonaCelular::class, 'id_persona')
            ->where('es_principal', true)
            ->where('estado', 1);
    }

    public function direccionPrincipal()
    {
        return $this->hasOne(PersonaDireccion::class, 'id_persona')
            ->where('es_principal', true)
            ->where('estado', 1);
    }

    public function getDocumento($tipoDocumentoId)
    {
        return $this->documentos()
            ->where('id_tipo_documento', $tipoDocumentoId)
            ->first();
    }

    public function asignarDocumento($tipoDocumentoId, $numeroDocumento)
    {
        return $this->documentos()->create([
            'id_tipo_documento' => $tipoDocumentoId,
            'numero_documento' => $numeroDocumento
        ]);
    }
}
