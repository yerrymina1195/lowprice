<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackImage;
use Illuminate\Http\Request;

class PackImageController extends Controller
{
    //
    public function delete($id)
    {
       $packImage= PackImage::findOrFail($id);
   
           deleteImage($packImage->image);
     
   
       $packImage->delete();
   
       return response()->json(['message' => 'Suppression effectuée'], 200);
    }
}
