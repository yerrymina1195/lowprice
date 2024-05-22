<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategorie;
use Exception;
use Illuminate\Http\Request;

class SubCategorieController extends Controller
{
  /**
 * @OA\Get(
 *     path="/api/subcategory",
 *     summary="Get all subcategories",
 *     tags={"Subcategories"},
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="name", type="string", example="Fruit"),
 *                  @OA\Property(property="categorie_id", type="integer", example="1"),
 *             )
 *         )
 *     )
 * )
 */
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
/**
 * @OA\Post(
 *     path="/api/subcategory/store",
 *     summary="Create a new subcategory",
 *     tags={"Subcategories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Subcategory object to be created",
 *         @OA\JsonContent(
 *             required={"categorie_id", "name"},
 *             @OA\Property(property="categorie_id", type="integer", example="1"),
 *             @OA\Property(property="name", type="string", example="Subcategory Name")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subcategory created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="SubCategorie added successfully"),
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/Subcategory")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string")
 *         )
 *     )
 * )
 */


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
            'message' => 'SubCategorie added successfully',
            'success' => true,
            'data' => $subcategorie
        ], 200);

       } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
       }


    }

        /**
 * @OA\Put(
 *     path="/api/subcategory/update/{id}",
 *     summary="Mettre à jour une sous catégorie",
 *     tags={"Subcategories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la sous catégorie",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Objet de la sous catégorie à mettre à jour",
 *         @OA\JsonContent(
 *             required={"categorie_id", "name"},
 *             @OA\Property(property="categorie_id", type="integer", example="1"),
 *             @OA\Property(property="name", type="string", example="Subcategory Name")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="subcategory updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", ref="#/components/schemas/Subcategory")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description=" subcategory  not found"
 *     )
 * )
 */
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
    /**
 * @OA\Delete(
 *     path="/api/subcategory/delete/{id}",
 *     summary="Supprimer une sous catégorie",
 *     tags={"Subcategories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la sous catégorie",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description=" supprimée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="suppression faite")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="subcategory  not found"
 *     )
 * )
 */

    public function delete($id){
        $subcategory = SubCategorie::find($id);
        if (!$subcategory ) {
            return response()->json(['message' => 'subcategory  not found'], 404);
        }

        $subcategory->delete();
        return response()->json(["message"=>'suppression faite']);
    }


        /**
 * @OA\Get(
 *     path="/api/subcategory/show/{id}",
 *     summary="Récupérer les détails d'une sous catégorie",
 *     tags={"Subcategories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la sous catégorie",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description=" sous Catégorie récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", ref="#/components/schemas/Subcategory")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="SubCategorie non trouvé"
 *     )
 * )
 */
    public function show($id)
    {
        $subCategory = SubCategorie::find($id);

        if (!$subCategory) {
            return response()->json(['message' => 'SubCategorie non trouvé'], 404);
        }

        return response()->json($subCategory);
    }


    }

