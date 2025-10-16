<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRebanhoRequest;
use App\Http\Requests\UpdateRebanhoRequest;
use App\Http\Resources\RebanhoResource;
use App\Models\Rebanho;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RebanhoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $rebanhos = Rebanho::all();
        return RebanhoResource::collection($rebanhos);
    }

    public function store(StoreRebanhoRequest $request): RebanhoResource
    {
        $rebanho = Rebanho::create($request->validated());
        return new RebanhoResource($rebanho);
    }

    public function show(Rebanho $rebanho): RebanhoResource
    {
        return new RebanhoResource($rebanho);
    }

    public function update(UpdateRebanhoRequest $request, Rebanho $rebanho): RebanhoResource
    {
        $rebanho->update($request->validated());
        return new RebanhoResource($rebanho);
    }

    public function destroy(Rebanho $rebanho): Response
    {
        $rebanho->delete();
        return response()->noContent();
    }
}
