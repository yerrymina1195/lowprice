<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Regle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegleController extends Controller
{
    //


    
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = Regle::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }
    public function store (Request $request)
    {
       try {
        $validator = Regle::validatedRegle($request->all());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $regles= Regle::create($data);

        return response()->json([
            'message' => 'article added successfully',
            'success' => true,
            'data' =>  $regles
        ], 200);

       } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
       }


    }
    public function update (Request $request, $id)
    {
 try{
    $regles = Regle::find($id);
        if (! $regles ) {
            return response()->json(['message' => ' Law  not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ], [
            'required' => 'Le champ :attribute est requis.',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $data = $validator->validated();


        $regles->update($data);

        return response()->json(['message' => ' Law updated successfully', $data], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }
    }

    public function show ($id) {

        try {
            $regles = Regle::find($id);
            if (! $regles ) {
                return response()->json(['message' => ' Law  not found'], 404);
            }
            return response()->json(['success' => true, 'data'=> $regles, ], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }

    }
}
