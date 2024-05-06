<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Produit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    

    public function store(Request $request)
    {
       try {
        $validator = Produit::validatedProduit($request->all());

        if($validator->fails())
        {
            return response()->json(["errors"=> $validator->errors()], 422);
        }

        $data= $validator->validate();

        $produits= Produit::create($data);

        addImagesToProduct($produits->id, $request);

        return response()->json(["success"=>true, "data"=>$produits,"message"=>"enregistré avec success"],201);
        
       } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
       }
    }



    public function searchProducts(Request $request)
{
    $perPage = request('per_page', 2);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

    $query = Produit::query()->where('name', 'like', "%{$search}%")
            ->with(['reviews', 'images'])
            ->orderBy($sortField, $sortDirection);


 if ($request->has('category_id')) {
    $category = Categorie::findOrFail($request->category_id);
    $query->where(function ($query) use ($category) {
        $query->whereHas('subcategories', function ($subquery) use ($category) {
            $subquery->where('category_id', $category->id);
        })
        ->orWhereDoesntHave('subcategories');
    });
}

    
    if ($request->has('subcategory_id')) {
        $query->where('subcategory_id', $request->subcategory_id);
    }

    if ($request->has('keyword')) {
        $keyword = $request->keyword;
        $query->where('name', 'like', "%$keyword%");
    }

    if ($request->has('min_price')) {
        $minPrice = $request->min_price;
        $query->where('prix', '>=', $minPrice);
    }

    if ($request->has('max_price')) {
        $maxPrice = $request->max_price;
        $query->where('prix', '<=', $maxPrice);
    }

    $products = $query->get();

    // $products = $query->paginate($perPage);

    return response()->json($products);
}


public function update(Request $request, $id)
{
    try{
        $produit = Produit::find($id);
        if (!$produit  ) {
            return response()->json(['message' => 'produit  not found'], 404);
        }

        $validator = Produit::validatedProduit($request->all(), $produit->id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        addImagesToProduct($produit->id, $request);

        $produit ->update($data);

        return response()->json(['message' => 'produit  updated successfully', $data], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }
}
 public function delete ( $id)
{
    try{
        $produit = Produit::find($id);
        if (!$produit  ) {
            return response()->json(['message' => 'produit  not found'], 404);
        }
        foreach ($produit->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $produit->delete();

        return response()->json(['message' => 'produit  delete successfully'], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }


}

public function show ($id)
{
try {
    $produit = Produit::with('images')->find($id);
        if (!$produit) {
            return response()->json(['message' => 'produit  not found'], 404);
        }

        return response()->json($produit);
} catch (Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
}
}


public function topProducts()
{
    $topProduits = Produit::withAvg('reviews', 'rating')
                                ->orderByDesc('reviews_avg_rating')
                                ->take(10)
                                ->get();
    
    return response()->json([$topProduits]);
}
}