<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnidadeProducaoRequest;
use App\Http\Requests\UpdateUnidadeProducaoRequest;
use App\Http\Resources\UnidadeProducaoResource;
use App\Models\UnidadeProducao;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UnidadeProducaoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $unidades = UnidadeProducao::all();
        return UnidadeProducaoResource::collection($unidades);
    }

    public function store(StoreUnidadeProducaoRequest $request): UnidadeProducaoResource
    {
        $unidade = UnidadeProducao::create($request->validated());
        return new UnidadeProducaoResource($unidade);
    }

    public function show(UnidadeProducao $unidadeProducao): UnidadeProducaoResource
    {
        return new UnidadeProducaoResource($unidadeProducao);
    }

    public function update(UpdateUnidadeProducaoRequest $request, UnidadeProducao $unidadeProducao): UnidadeProducaoResource
    {
        $unidadeProducao->update($request->validated());
        return new UnidadeProducaoResource($unidadeProducao);
    }

    public function destroy(UnidadeProducao $unidadeProducao): Response
    {
        $unidadeProducao->delete();
        return response()->noContent();
    }
}
