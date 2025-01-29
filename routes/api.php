<?php

use App\Http\Controllers\AccessManagement\AccionController;
use App\Http\Controllers\AccessManagement\MenuController;
use App\Http\Controllers\AccessManagement\ModuloController;
use App\Http\Controllers\AccessManagement\RoleController;
use App\Http\Controllers\AccessManagement\RolMenuAccionController;
use App\Http\Controllers\AccessManagement\RolMenuController;
use App\Http\Controllers\AccessManagement\UsuarioRolController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Person\PersonaController;
use App\Http\Controllers\Person\PersonaJuridicaController;
use App\Http\Controllers\Person\PersonaNaturalController;
use App\Http\Controllers\Person\TipoDocumentoController;
use App\Http\Controllers\Person\TipoEstadoCivilController;
use App\Http\Controllers\Person\TipoGeneroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// ? autenticacion
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

//? Mantenimiento Persona
// CRUD Persona Usuario
Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('v1/person')->group(function(){
        Route::get("/persona", [PersonaController::class, 'listar']);
        Route::post("/persona", [PersonaController::class, 'guardar']);
        Route::get("/persona/{id}", [PersonaController::class, 'mostrar']);
        Route::put("/persona/{id}", [PersonaController::class, 'actualizar']);
        Route::delete("/persona/{id}", [PersonaController::class, 'eliminar']);

        Route::apiResource('/tipo-documento', TipoDocumentoController::class);
        Route::get('/tipo-documento-selector', [TipoDocumentoController::class, 'selector']);
        Route::get('/tipo-documento-naturales', [TipoDocumentoController::class, 'naturales']);
        Route::get('/tipo-documento-juridicos', [TipoDocumentoController::class, 'juridicos']);

        Route::apiResource('/tipo-genero', TipoGeneroController::class);
        Route::get('/tipo-genero-selector', [TipoGeneroController::class, 'selector']);

        Route::apiResource('tipo-estado-civil', TipoEstadoCivilController::class);
        Route::get('tipo-estado-civil-selector', [TipoEstadoCivilController::class, 'selector']);

        Route::apiResource('/persona-natural', PersonaNaturalController::class);
        Route::apiResource('/persona-juridica', PersonaJuridicaController::class);


    });
    Route::prefix('v1/setup')->group(function(){
        // Roles
        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{id}/menus', [RoleController::class, 'asignarMenus']);
        Route::post('roles/{id}/acciones', [RoleController::class, 'asignarAcciones']);

        // Módulos
        Route::apiResource('modulos', ModuloController::class);

        // Menús
        Route::apiResource('menus', MenuController::class);

        // Acciones
        Route::apiResource('acciones', AccionController::class);

        // Rol-Menú
        Route::apiResource('rol-menus', RolMenuController::class);

        // Rol-Menú-Acciones
        Route::apiResource('rol-menu-acciones', RolMenuAccionController::class);

        // Usuario-Roles
        Route::apiResource('usuario-roles', UsuarioRolController::class);
    });
    Route::prefix('v1/configuration')->group(function(){
    });
});
