<?php

namespace App\Models\Salomon\Access;

use App\Models\Gedeon\TablaBase;
use App\Models\Gedeon\Entidad;
use App\Models\User;

class Role extends TablaBase
{
    protected $table = 'salomon.roles';
    protected $primaryKey = 'id_rol';

    protected $fillable = [
        'nombre',
        'descripcion',
        'id_entidad',
        'es_predeterminado',
        'es_personalizable'
    ];

    protected $casts = [
        'es_predeterminado' => 'boolean',
        'es_personalizable' => 'boolean'
    ];

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'id_entidad');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'salomon.usuario_roles', 'id_rol', 'id_usuario')
                    ->withPivot(['fecha_asignacion', 'fecha_expiracion'])
                    ->withTimestamps();
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'salomon.rol_menus', 'id_rol', 'id_menu');
    }

    public function acciones()
    {
        return $this->hasManyThrough(
            Accion::class,
            RolMenu::class,
            'id_rol',
            'id_menu',
            'id_rol',
            'id_menu'
        );
    }
}
