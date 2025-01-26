<?php

namespace App\Models\Gedeon;

use Illuminate\Database\Eloquent\Model;

class TablaBase extends Model
{
    protected $connection = 'pgsql';
    public $timestamps = false;

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];

    protected static function boot()
    {

        parent::boot();

        // Antes de crear
        static::creating(function ($model) {
            $model->fecha_creacion = now();
            $model->fecha_actualizacion = now();
            $model->usuario_creacion = session('user_session')->id_usuario;
            $model->estado = 1;  // Por defecto activo
        });

        // Antes de actualizar
        static::updating(function ($model) {
            $model->fecha_actualizacion = now();
            $model->usuario_actualizacion = session('user_session')->id_usuario;
        });
    }

    protected $fillable = [
        'estado',
        'usuario_creacion',
        'usuario_actualizacion'
    ];

    // Scope para registros activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Scope para ordenar por defecto
    public function scopeOrdenado($query)
    {
        return $query->orderBy('fecha_creacion', 'desc');
    }
}
