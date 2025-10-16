<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRebanhoRequest;
use App\Http\Requests\UpdateRebanhoRequest;
use App\Http\Resources\RebanhoResource;
use App\Models\Rebanho;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

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

    public function exportPdf(Request $request): HttpResponse
    {
        $filters = $request->only(['especie', 'propriedade_id', 'produtor_id']);

        $query = Rebanho::with('propriedade.produtor');

        if (!empty($filters['especie'])) {
            $query->where('especie', $filters['especie']);
        }

        if (!empty($filters['propriedade_id'])) {
            $query->where('propriedade_id', $filters['propriedade_id']);
        }

        if (!empty($filters['produtor_id'])) {
            $query->whereHas('propriedade', function ($q) use ($filters) {
                $q->where('produtor_id', $filters['produtor_id']);
            });
        }

        $rebanhos = $query->get();

        $pdf = Pdf::loadView('exports.rebanhos', [
            'rebanhos' => $rebanhos,
            'filtros' => $filters,
        ]);

        return $pdf->download('rebanhos.pdf');
    }
}
