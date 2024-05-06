<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Exception;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
  
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = Categorie::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }


    public function store(Request $request)
    {
        try {
            $folder = 'Categories';

            $validator = Categorie::validatedCategory($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cate = $validator->validated();
            $imagePath = uploadImage($request->file('image'), $folder);
            $cate['image']= $imagePath;
            $data = Categorie::create($cate);

            return response()->json([
                'message' => 'Categorie added successfully',
                'success' => true,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $Category = Categorie::find($id);

        if (!$Category) {
            return response()->json(['message' => 'Categorie non trouvé'], 404);
        }

        return response()->json($Category);
    }

    public function delete($id)
    {
        $Category = Categorie::find($id);
        if ($Category) {
            deleteImage($Category->image);
            $Category->delete();
            return response()->json('Category delete');
        }
        return response()->json(['message' => 'Category not found'], 404);
    }

    public function update(Request $request, $id)
    {
        try {
            $folder = 'Categories';
            $category = Categorie::find($id);
            if (!$category) {
                return response()->json(['message' => 'category not found'], 404);
            }

            $validator = Categorie::validatedCategory($request->all(), $category->id);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            if ($request->hasFile('image')) {
                deleteImage($category->image);
                $imagePath = uploadImage($request->file('image'), $folder);
                $data['image'] = $imagePath;
            }

            $category->update($data);

            return response()->json(['message' => 'category updated successfully', $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }
}
