<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Banniere;
use Exception;
use Illuminate\Http\Request;

class BanniereController extends Controller
{
    //



    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = Banniere::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }


    public function store(Request $request)
    {
        try {
            $folder = 'bannieres';

            $validator = Banniere::validatedBanniere($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cate = $validator->validated();
            $imagePath = uploadImage($request->file('image'), $folder);
            $cate['image']= $imagePath;
            $data = banniere::create($cate);

            return response()->json([
                'message' => 'banniere added successfully',
                'success' => true,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }





    public function update(Request $request, $id)
    {
        try {
            $folder = 'bannieres';
            $banniere = Banniere::find($id);
            if (!$banniere) {
                return response()->json(['message' => 'Banniere not found'], 404);
            }

            $validator = Banniere::validatedBanniere($request->all(), $banniere->id);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            if ($request->hasFile('image')) {
                deleteImage($banniere->image);
                $imagePath = uploadImage($request->file('image'), $folder);
                $data['image'] = $imagePath;
            }

            $banniere->update($data);

            return response()->json(['message' => 'Banniere updated successfully',"data"=> $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }


    
    public function delete($id)
    {
        $banniere = Banniere::find($id);
        if ($banniere) {
            deleteImage($banniere->image);
            $banniere->delete();
            return response()->json('Banniere delete');
        }
        return response()->json(['message' => 'Banniere not found'], 404);
    }
}
