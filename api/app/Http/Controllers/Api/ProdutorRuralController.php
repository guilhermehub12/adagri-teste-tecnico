<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdutorRuralRequest;
use App\Http\Requests\UpdateProdutorRuralRequest;
use App\Http\Requests\UploadFotoProdutorRequest;
use App\Http\Resources\ProdutorRuralResource;
use App\Models\ProdutorRural;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProdutorRuralController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['nome', 'cpf_cnpj', 'email']);
        $perPage = $request->input('per_page', 15);

        $produtores = ProdutorRural::filter($filters)->paginate($perPage);
        $produtores->withQueryString();
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

    public function uploadFoto(UploadFotoProdutorRequest $request, ProdutorRural $produtorRural): ProdutorRuralResource
    {
        // Remove foto anterior se existir
        if ($produtorRural->foto) {
            Storage::disk('public')->delete('produtores/' . basename($produtorRural->foto));
        }

        // Armazena nova foto
        $foto = $request->file('foto');
        $nomeArquivo = time() . '_' . $foto->getClientOriginalName();
        $caminhoArquivo = $foto->storeAs('produtores', $nomeArquivo, 'public');

        // Atualiza produtor com caminho da foto
        $produtorRural->update(['foto' => $caminhoArquivo]);

        return new ProdutorRuralResource($produtorRural);
    }

    public function deleteFoto(ProdutorRural $produtorRural): JsonResponse
    {
        if (!$produtorRural->foto) {
            return response()->json(['message' => 'Produtor não possui foto.'], 404);
        }

        // Remove foto do storage
        Storage::disk('public')->delete('produtores/' . basename($produtorRural->foto));

        // Remove referência do banco
        $produtorRural->update(['foto' => null]);

        return response()->json(['message' => 'Foto removida com sucesso.']);
    }
}
