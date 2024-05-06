<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategorie;
use Exception;
use Illuminate\Http\Request;

class SubCategorieController extends Controller
{

    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = SubCategorie::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }
    public function store (Request $request)
    {
       try {
        $validator = SubCategorie::validatedSubCategory($request->all());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $subcategorie= SubCategorie::create($data);

        return response()->json([
            'message' => 'article added successfully',
            'success' => true,
            'data' => $subcategorie
        ], 200);

       } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
       }


    }
    public function update (Request $request, $id)
    {
 try{
        $subcategory = SubCategorie::find($id);
        if (!$subcategory ) {
            return response()->json(['message' => 'subcategory  not found'], 404);
        }

        $validator = SubCategorie::validatedSubCategory($request->all(), $subcategory->id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
    

        $subcategory->update($data);

        return response()->json(['message' => 'subcategory updated successfully', $data], 200);
    } catch (Exception $e) {
        return response()->json($e, 500);
    }
    }


    public function delete($id){
        $subcategory = SubCategorie::find($id);
        if (!$subcategory ) {
            return response()->json(['message' => 'subcategory  not found'], 404);
        }

        $subcategory->delete();
        return response()->json(["message"=>'suppression faite']);
    }


    public function show($id)
    {
        $subCategory = SubCategorie::find($id);

        if (!$subCategory) {
            return response()->json(['message' => 'SubCategorie non trouvé'], 404);
        }

        return response()->json($subCategory);
    }


    }

