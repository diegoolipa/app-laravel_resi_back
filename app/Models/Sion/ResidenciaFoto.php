<?php

namespace App\Models\Sion;

use App\Models\Gedeon\TablaBase;

class ResidenciaFoto extends TablaBase
{
   protected $table = 'residencia_fotos';
   protected $primaryKey = 'id_residencia_foto';

   protected $fillable = [
       'id_residencia',
       'url_foto',
       'descripcion',
       'es_principal',
       'orden'
   ];

   protected $casts = [
       'es_principal' => 'boolean'
   ];

   // Relación con Residencia
   public function residencia()
   {
       return $this->belongsTo(ResiResidencia::class, 'id_residencia');
   }

   // Scope para foto principal
   public function scopeFotoPrincipal($query)
   {
       return $query->where('es_principal', true);
   }
}
