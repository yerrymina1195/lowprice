<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Socialink;
use Exception;
use Illuminate\Http\Request;

class SocialinkController extends Controller
{
    //


    
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = Socialink::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }
    public function store (Request $request)
    {
       try {
        $validator = Socialink::validatedSocialink($request->all());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $socialink= Socialink::create($data);

        return response()->json([
            'message' => 'socialink added successfully',
            'success' => true,
            'data' =>  $socialink
        ], 200);

       } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
       }


    }
    public function update (Request $request, $id)
    {
 try{
    $socialink = Socialink::find($id);
        if (! $socialink ) {
            return response()->json(['message' => ' Socialink  not found'], 404);
        }
        $validator =Socialink::validatedSocialink($request->all(),$socialink->id);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $data = $validator->validated();


        $socialink->update($data);

        return response()->json(['message' => ' socialink updated successfully', $data], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }
    }

    public function show ($id) {

        try {
            $socialink = Socialink::find($id);
            if (! $socialink ) {
                return response()->json(['message' => ' Law  not found'], 404);
            }
            return response()->json(['success' => true, 'data'=> $socialink, ], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }

    }
}
