<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoProduit;
use Exception;
use Illuminate\Http\Request;

class PromoProductController extends Controller
{
    //

    // public function index()
    // {
    //     $perPage = request('per_page', 10);
    // $sortField = request('sort_field', 'created_at');
    // $sortDirection = request('sort_direction', 'desc');

    // $data = PromoProduit::with(['produits', 'promos'])
    //     ->orderBy($sortField, $sortDirection)
    //     ->paginate($perPage);

    // return response()->json($data, 200);
    // }



    public function index()
{
    $perPage = request('per_page', 10);
    $search = request('search', '');
    $sortField = request('sort_field', 'created_at');
    $sortDirection = request('sort_direction', 'desc');

    $data = PromoProduit::query()
        ->with(['produits', 'promos'])
        ->orderBy($sortField, $sortDirection)
        ->paginate($perPage);

    $transformedData = $data->map(function ($promoProduit) {
        // dd($promoProduit->produits->images);
        return [
            'id' => $promoProduit->id,
            'prixpromo' => $promoProduit->prixpromo,
            'produitname'=>$promoProduit->produits->name,
            'produitnameprix'=> $promoProduit->produits->prix,
            'produitImage' => $promoProduit->produits->images->isEmpty() ? null : $promoProduit->produits->images[0]->image,
            'promotitle'=> $promoProduit->promos->title,
            'created_at' => $promoProduit->created_at,
            'updated_at' => $promoProduit->updated_at,
        ];
    });

    return response()->json($transformedData, 200);
}

    public function store(Request $request)
    {
        try {

            $validator = PromoProduit::validatedPromoProduit($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data= $validator->validated();

            $promoproduct = PromoProduit::create($data);

            return response()->json([
                'message' => 'Produit added with promo successfully',
                'success' => true,
                'data' =>  $promoproduct
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $promoProduits = PromoProduit::find($id);
            if (!$promoProduits  ) {
                return response()->json(['message' => 'PromoProduit not found'], 404);
            }
    
            $validator = PromoProduit::validatedPromoProduit($request->all());
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $data = $validator->validated();
    
            $promoProduits ->update($data);
    
            return response()->json(['message' => 'PromoProduit updated successfully', $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function delete($id)
    {
        $promoProduits = PromoProduit::find($id);
            if (!$promoProduits  ) {
                return response()->json(['message' => 'PromoProduit not found'], 404);
            }

            $promoProduits->delete();

            return response()->json(["message"=>'deleted successfully']);

    }

}
