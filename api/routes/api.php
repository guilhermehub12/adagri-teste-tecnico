<?php

use App\Http\Controllers\Api\ProdutorRuralController;
use App\Http\Controllers\Api\PropriedadeController;
use App\Http\Controllers\Api\RebanhoController;
use App\Http\Controllers\Api\RelatorioController;
use App\Http\Controllers\Api\UnidadeProducaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('produtores-rurais', ProdutorRuralController::class)
    ->parameters(['produtores-rurais' => 'produtor-rural']);

Route::get('propriedades/export/excel', [PropriedadeController::class, 'export']);
Route::apiResource('propriedades', PropriedadeController::class);

Route::apiResource('unidades-producao', UnidadeProducaoController::class)
    ->parameters(['unidades-producao' => 'unidade-producao']);

Route::get('rebanhos/export/pdf', [RebanhoController::class, 'exportPdf']);
Route::apiResource('rebanhos', RebanhoController::class);

Route::get('relatorios/propriedades-por-municipio', [RelatorioController::class, 'propriedadesPorMunicipio']);
Route::get('relatorios/animais-por-especie', [RelatorioController::class, 'animaisPorEspecie']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
