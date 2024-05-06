<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\ProduitImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    //
 public function delete($id)
 {
    $productImage= ProduitImage::findOrFail($id);

        deleteImage($productImage->image);
  

    $productImage->delete();

    return response()->json(['message' => 'Suppression effectuée'], 200);
 }
}
