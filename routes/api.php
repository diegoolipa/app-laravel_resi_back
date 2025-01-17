<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Person\PersonaController;
use App\Http\Controllers\Person\TipoDocumentoController;
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

        Route::apiResource('/tipo-documentos', TipoDocumentoController::class);
        Route::get('/tipo-documentos-naturales', [TipoDocumentoController::class, 'naturales']);
        Route::get('/tipo-documentos-juridicos', [TipoDocumentoController::class, 'juridicos']);
    });

    Route::prefix('v1/configuration')->group(function(){
    });
});
