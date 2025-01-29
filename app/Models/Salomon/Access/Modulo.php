<?php

namespace App\Models\Salomon\Access;

use App\Models\Gedeon\TablaBase;

class Modulo extends TablaBase
{
    protected $table = 'salomon.modulos';
    protected $primaryKey = 'id_modulo';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'icono',
        'es_activo'
    ];

    protected $casts = [
        'es_activo' => 'boolean'
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'id_modulo');
    }
}
