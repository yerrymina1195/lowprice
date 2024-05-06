<?php

use App\Models\Pack;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
function uploadImage($file, $folder)
{
    
    if (!$file) {
        return null;
    }

    $fileName = $file->getClientOriginalName();
    $filePath = $folder . '/' . $fileName;

    Storage::disk('public')->putFileAs($folder , $file, $fileName);

    return $filePath;
};



function deleteImage($path)
{
    if ($path) {
        Storage::disk('public')->delete($path);
    }
}



function canEdit($comment)
{
    return Auth::check() && $comment->user_Id == Auth::id();
}
 function canDelete($comment)
{
    $user = Auth::user();
    return $user && ($comment->user_id == $user->id || $comment->article->user_Id == $user->id);
}


function addImagesToProduct($productId, Request $request)
{
    $request->validate([
        'image.*' => 'required|image|mimes:png,jpg,jpeg,webp'
    ]);

    $product = Produit::findOrFail($productId);


    foreach ($request->file('image') as $image) {
        $fileName = $image->getClientOriginalName();
        $filePath = 'produits/' . $fileName;
        Storage::disk('public')->putFileAs('produits', $image, $fileName);
       
        $product->images()->create(['image' => $filePath]);
    }
}
function addImagesToPack($packId, Request $request)
{
    $request->validate([
        'image.*' => 'required|image|mimes:png,jpg,jpeg,webp'
    ]);

    $pack = Pack::findOrFail($packId);


    foreach ($request->file('image') as $image) {
        $fileName = $image->getClientOriginalName();
        $filePath = 'packs/' . $fileName;
        Storage::disk('public')->putFileAs('packs', $image, $fileName);
       
        $pack->images()->create(['image' => $filePath]);
    }
}




function generateID(){
    return Uuid::uuid4()->toString();
}
