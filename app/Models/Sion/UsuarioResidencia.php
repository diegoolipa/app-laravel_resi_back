<?php

namespace App\Models\Sion;

use App\Models\Sion\Residencia;
use App\Models\TablaBase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsuarioResidencia extends TablaBase
{
    protected $table = 'sion.usuario_residencias';
    protected $primaryKey = 'id_usuario_residencia';

    protected $fillable = [
        'id_usuario',
        'id_residencia',
        'es_actual',
        'observaciones'
    ];

    protected $casts = [
        'es_actual' => 'boolean'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function residencia()
    {
        return $this->belongsTo(Residencia::class, 'id_residencia');
    }

    // Método para cambiar la residencia actual
    public static function cambiarResidenciaActual($idUsuario, $idResidencia)
    {
        // Comenzar transacción
        DB::beginTransaction();

        try {
            // Desactivar la residencia actual si existe
            self::where('id_usuario', $idUsuario)
                ->where('es_actual', true)
                ->update(['es_actual' => false]);

            // Activar la nueva residencia
            self::updateOrCreate(
                [
                    'id_usuario' => $idUsuario,
                    'id_residencia' => $idResidencia
                ],
                [
                    'es_actual' => true,
                    'estado' => 1
                ]
            );

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
