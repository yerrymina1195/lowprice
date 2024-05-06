<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackController extends Controller
{
    //

    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = Pack::query()->with('images')
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)->get();

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
       try {
        $validator = Pack::validatedPack($request->all());

        if($validator->fails())
        {
            return response()->json(["errors"=> $validator->errors()], 422);
        }

        $data= $validator->validate();

        $packs= Pack::create($data);

        addImagesToPack($packs->id, $request);

        return response()->json(["success"=>true, "data"=>$packs,"message"=>"enregistré avec success"],201);
        
       } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
       }
    }



    public function update(Request $request, $id)
{
    try{
        $packs = Pack::find($id);
        if (!$packs  ) {
            return response()->json(['message' => 'pack  not found'], 404);
        }

        $validator = Pack::validatedPack($request->all(), $packs->id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        addImagesToPack($packs->id, $request);

        $packs ->update($data);

        return response()->json(['message' => 'pack  updated successfully', $data], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }
}
 public function delete ( $id)
{
    try{
        $packs = Pack::find($id);
        if (!$packs  ) {
            return response()->json(['message' => 'pack  not found'], 404);
        }
        foreach ($packs->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $packs->delete();

        return response()->json(['message' => 'pack  delete successfully'], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }
}

public function show ($id)
{
try {
    $packs = Pack::with('images')->find($id);
        if (!$packs  ) {
            return response()->json(['message' => 'pack  not found'], 404);
        }

        return response()->json($packs);
} catch (Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
}
}
}