<?php

namespace App\Models\Sion;

use App\Models\Gedeon\TablaBase;
use App\Models\Gedeon\Departamento;
use App\Models\Salomon\UsuarioDepartamento;
use App\Models\User;

class ResiResidencia extends TablaBase
{
   protected $table = 'sion.resi_residencias';
   protected $primaryKey = 'id_residencia';

   protected $fillable = [
       'id_departamento'
   ];

   // Relación con Departamento
   public function departamento()
   {
       return $this->belongsTo(Departamento::class, 'id_departamento');
   }

   // Relación con usuarios por tabla usuario_departamentos
   public function usuarios()
   {
       return $this->hasManyThrough(
           User::class,
           UsuarioDepartamento::class,
           'id_departamento',
           'id',
           'id_departamento',
           'id_usuario'
       );
   }

   // Usuarios activos de la residencia
   public function usuariosActivos()
   {
       return $this->usuarios()
           ->where('usuario_departamentos.es_activo', true);
   }
}
