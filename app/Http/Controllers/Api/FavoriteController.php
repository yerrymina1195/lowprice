<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    //


    public function index()
    {
        $favoriteProducts = auth()->user()->favoriteProducts()->with('images')->get()->makeHidden(['quantity','pivot','images']);

        $transformedProducts = $favoriteProducts->map(function ($product) {
            $firstImage = $product->images->first();
            $productData = $product->toArray();
            $productData['image'] = $firstImage ? $firstImage->image  : null;
            return $productData;
        });
        return response()->json(["data"=> $transformedProducts]);
    }

    
    public function addFavorite($productid)
    {
        auth()->user()->favoriteProducts()->attach($productid);

        return response()->json(["message"=>'favoris ajouté']);
    }
    public function removeFavorite($productid)
    {
        auth()->user()->favoriteProducts()->detach($productid);

        return response()->json(["message"=>'favoris supprimé']);
    }



}
