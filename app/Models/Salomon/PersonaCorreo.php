<?php

namespace App\Models\Salomon;

use App\Models\TablaBase;
use Illuminate\Support\Facades\DB;

class PersonaCorreo extends TablaBase
{
   protected $table = 'salomon.persona_correos';
   protected $primaryKey = 'id_persona_correo';

   protected $fillable = [
       'id_persona',
       'correo',
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
   public static function setPrincipal($idPersona, $idCorreo)
   {
       DB::transaction(function () use ($idPersona, $idCorreo) {
           // Quitar principal de otros correos
           self::where('id_persona', $idPersona)
               ->where('es_principal', true)
               ->update(['es_principal' => false]);

           // Establecer el nuevo correo principal
           self::where('id_persona_correo', $idCorreo)
               ->update(['es_principal' => true]);
       });
   }
}


