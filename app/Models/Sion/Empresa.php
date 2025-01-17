<?php

namespace App\Models\Sion;

use App\Models\Salomon\Persona;
use App\Models\Salomon\PersonaJuridica;
use App\Models\TablaBase;

class Empresa extends TablaBase
{
   protected $table = 'sion.empresas';
   protected $primaryKey = 'id_empresa';

   protected $fillable = [
       'logo',
       'id_persona'
   ];

   // Relación con Persona (debe ser persona jurídica)
   public function persona()
   {
       return $this->belongsTo(Persona::class, 'id_persona');
   }

   // Relación con PersonaJuridica a través de persona
   public function personaJuridica()
   {
       return $this->hasOneThrough(
           PersonaJuridica::class,
           Persona::class,
           'id_persona', // Llave foránea en empresas
           'id_persona', // Llave foránea en personas_juridicas
           'id_persona', // Llave local en empresas
           'id_persona'  // Llave local en personas
       );
   }

   // Relación con Residencias
   public function residencias()
   {
       return $this->hasMany(Residencia::class, 'id_empresa');
   }

   // Accessor para obtener razón social
   public function getRazonSocialAttribute()
   {
       return $this->personaJuridica->razon_social ?? '';
   }

   // Accessor para obtener nombre comercial
   public function getNombreComercialAttribute()
   {
       return $this->personaJuridica->nombre_comercial ?? $this->razon_social;
   }

   // Scope para empresas activas
   public function scopeActivas($query)
   {
       return $query->where('estado', 1);
   }

   // Método para obtener datos completos
   public function getDatosCompletos()
   {
       return $this->load([
           'persona.personaJuridica',
           'persona.documentos.tipoDocumento',
           'persona.direccion',
           'persona.celular',
           'persona.correo'
       ]);
   }
}
