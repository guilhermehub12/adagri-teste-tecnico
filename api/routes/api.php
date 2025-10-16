<?php

use App\Http\Controllers\Api\ProdutorRuralController;
use App\Http\Controllers\Api\PropriedadeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('produtores-rurais', ProdutorRuralController::class)
    ->parameters(['produtores-rurais' => 'produtor-rural']);

Route::apiResource('propriedades', PropriedadeController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
