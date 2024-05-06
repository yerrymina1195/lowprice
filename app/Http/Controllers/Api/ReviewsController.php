<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    //



    public function store(Request $request)
    {

        $validator= Review::validateReview($request->all());
   

        if($validator->fails())
        {
            return response()->json(["errors"=> $validator->errors()], 422);
        }

        $data= $validator->validate();

        $product = Produit::findOrFail($request->produit_id);

        $data['user_id']= Auth::id();
        $data['produit_id']= $product->id;


        $review= Review::create($data);
        return response()->json(['message' => 'Avis créé avec succès', "data"=>$review], 201);
    }
}
