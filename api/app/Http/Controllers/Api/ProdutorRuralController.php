<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdutorRuralRequest;
use App\Http\Requests\UpdateProdutorRuralRequest;
use App\Http\Resources\ProdutorRuralResource;
use App\Models\ProdutorRural;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProdutorRuralController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $produtores = ProdutorRural::all();
        return ProdutorRuralResource::collection($produtores);
    }

    public function store(StoreProdutorRuralRequest $request): ProdutorRuralResource
    {
        $produtor = ProdutorRural::create($request->validated());
        return new ProdutorRuralResource($produtor);
    }

    public function show(ProdutorRural $produtorRural): ProdutorRuralResource
    {
        return new ProdutorRuralResource($produtorRural);
    }

    public function update(UpdateProdutorRuralRequest $request, ProdutorRural $produtorRural): ProdutorRuralResource
    {
        $produtorRural->update($request->validated());
        return new ProdutorRuralResource($produtorRural);
    }

    public function destroy(ProdutorRural $produtorRural): Response
    {
        $produtorRural->delete();
        return response()->noContent();
    }
}
