<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livraison;
use Exception;
use Illuminate\Http\Request;

class LivraisonController extends Controller
{
    //


    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = Livraison::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)->get();

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
       try {
        $validator = Livraison::validatedLivraison($request->all());

        if($validator->fails())
        {
            return response()->json(["errors"=> $validator->errors()], 422);
        }

        $data= $validator->validate();

        $livraison= Livraison::create($data);

        return response()->json(["success"=>true, "data"=>$livraison,"message"=>"enregistré avec success"],201);
        
       } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
       }
    }



    public function update(Request $request, $id)
{
    try{
        $livraison = Livraison::find($id);
        if (!$livraison ) {
            return response()->json(['message' => 'pack  not found'], 404);
        }

        $validator = Livraison::validatedLivraison($request->all(), $livraison->id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();


        $livraison ->update($data);

        return response()->json(['message' => 'livraison  updated successfully', $data], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }
}
 public function delete ( $id)
{
    try{
        $livraison = Livraison::find($id);
        if (!$livraison  ) {
            return response()->json(['message' => 'livraison  not found'], 404);
        }


        $livraison->delete();

        return response()->json(['message' => 'livraison  delete successfully'], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }
}

public function show ($id)
{
try {
    $livraison = Livraison::find($id);
        if (!$livraison  ) {
            return response()->json(['message' => 'livraison  not found'], 404);
        }

        return response()->json($livraison);
} catch (Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
}
}
}
