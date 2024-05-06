<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoBanniere;
use Exception;
use Illuminate\Http\Request;

class PromoBanniereController extends Controller
{
    //


    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = PromoBanniere::query()
            ->where('title', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)->get();

        return response()->json($data, 200);
    }
    public function store(Request $request)
    {
        try {
            $folder = 'promos';

            $validator = PromoBanniere::validatedPromoBanniere($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cate = $validator->validated();
            $imagePath = uploadImage($request->file('image'), $folder);
            $cate['image']= $imagePath;
            $data = PromoBanniere::create($cate);

            return response()->json([
                'message' => 'Promo added successfully',
                'success' => true,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    
    public function show($id)
    {
        $promoBanniere = PromoBanniere::find($id);

        if (!$promoBanniere) {
            return response()->json(['message' => 'PromoBanniere non trouvé'], 404);
        }

        return response()->json($promoBanniere);
    }

    public function delete($id)
    {
        $promoBanniere = PromoBanniere::find($id);
        if ($promoBanniere) {
            deleteImage($promoBanniere->image);
            $promoBanniere->delete();
            return response()->json('PromoBanniere delete');
        }
        return response()->json(['message' => 'PromoBanniere not found'], 404);
    }

    public function update(Request $request, $id)
    {
        try {
            $folder = 'promos';
            $promoBanniere = PromoBanniere::find($id);
            if (!$promoBanniere) {
                return response()->json(['message' => 'PromoBanniere not found'], 404);
            }

            $validator = PromoBanniere::validatedPromoBanniere($request->all(), $promoBanniere->id);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            if ($request->hasFile('image')) {
                deleteImage($promoBanniere->image);
                $imagePath = uploadImage($request->file('image'), $folder);
                $data['image'] = $imagePath;
            }

            $promoBanniere->update($data);

            return response()->json(['message' => 'PromoBanniere updated successfully', $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }
}
