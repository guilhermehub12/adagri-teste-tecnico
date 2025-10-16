<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropriedadeRequest;
use App\Http\Requests\UpdatePropriedadeRequest;
use App\Http\Resources\PropriedadeResource;
use App\Models\Propriedade;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PropriedadeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $propriedades = Propriedade::all();
        return PropriedadeResource::collection($propriedades);
    }

    public function store(StorePropriedadeRequest $request): PropriedadeResource
    {
        $propriedade = Propriedade::create($request->validated());
        return new PropriedadeResource($propriedade);
    }

    public function show(Propriedade $propriedade): PropriedadeResource
    {
        return new PropriedadeResource($propriedade);
    }

    public function update(UpdatePropriedadeRequest $request, Propriedade $propriedade): PropriedadeResource
    {
        $propriedade->update($request->validated());
        return new PropriedadeResource($propriedade);
    }

    public function destroy(Propriedade $propriedade): Response
    {
        $propriedade->delete();
        return response()->noContent();
    }
}
