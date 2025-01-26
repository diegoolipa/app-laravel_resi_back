<?php

namespace App\Models\Salomon;

use App\Models\Gedeon\TablaBase;
use Illuminate\Support\Facades\DB;

class PersonaDocumento extends TablaBase
{
   protected $table = 'salomon.persona_documentos';

   // Indicar que no tiene una única primary key
   protected $primaryKey = null;
   public $incrementing = false;

   protected $fillable = [
       'id_persona',
       'id_tipo_documento',
       'numero_documento'
   ];

   // Relación con Persona
   public function persona()
   {
       return $this->belongsTo(Persona::class, 'id_persona');
   }

   // Relación con TipoDocumento
   public function tipoDocumento()
   {
       return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
   }

   // Scope para buscar por número de documento
   public function scopePorNumeroDocumento($query, $numero)
   {
       return $query->where('numero_documento', $numero);
   }

   // Método para obtener documento formateado
   public function getDocumentoFormateadoAttribute()
   {
       return "{$this->tipoDocumento->sigla}: {$this->numero_documento}";
   }
}
