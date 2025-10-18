<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdutorRuralController;
use App\Http\Controllers\Api\PropriedadeController;
use App\Http\Controllers\Api\RebanhoController;
use App\Http\Controllers\Api\RelatorioController;
use App\Http\Controllers\Api\UnidadeProducaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // CRUD Produtor Rural
    Route::apiResource('produtores-rurais', ProdutorRuralController::class)
        ->parameters(['produtores-rurais' => 'produtor-rural']);

    // CRUD Propriedades
    Route::apiResource('propriedades', PropriedadeController::class);

    // CRUD Unidade de Produção
    Route::apiResource('unidades-producao', UnidadeProducaoController::class)
    ->parameters(['unidades-producao' => 'unidade-producao']);
    
    // CRUD Rebanhos
    Route::apiResource('rebanhos', RebanhoController::class);
    
    // Exports (Excel/PDF)
    Route::get('propriedades/export/excel', [PropriedadeController::class, 'export']);
    Route::get('rebanhos/export/pdf', [RebanhoController::class, 'exportPdf']);

    // Reports
    Route::get('relatorios/propriedades-por-municipio', [RelatorioController::class, 'propriedadesPorMunicipio']);
    Route::get('relatorios/animais-por-especie', [RelatorioController::class, 'animaisPorEspecie']);
    Route::get('relatorios/hectares-por-cultura', [RelatorioController::class, 'hectaresPorCultura']);
});
