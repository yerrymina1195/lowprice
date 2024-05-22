<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Exception;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
  /**
 * @OA\Get(
 *     path="/api/category",
 *     summary="Get all categories",
 *     tags={"Categories"},
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="name", type="string", example="Alimentaion"),
 *                 @OA\Property(property="image", type="string", format="url", example="Categories/lait")
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

        $data = Categorie::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }


    /**
 * @OA\Post(
 *     path="/api/category/store",
 *     summary="Create a new category",
 *     tags={"Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Category object to be created",
 *         @OA\JsonContent(
 *             required={"name", "image"},
 *             @OA\Property(property="name", type="string", example="New Category"),
 *             @OA\Property(property="image", type="string", format="url", example="image.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example="1"),
 *             @OA\Property(property="name", type="string", example="New Category"),
 *             @OA\Property(property="image", type="string", format="url", example="Category/image.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */

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


    /**
 * @OA\Get(
 *     path="/api/category/show/{id}",
 *     summary="Récupérer les détails d'une catégorie",
 *     tags={"Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la catégorie",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Catégorie récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Nom de la catégorie"),
 *             @OA\Property(property="image", type="string", format="url", example="https://exemple.com/image.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Catégorie non trouvée"
 *     )
 * )
 */
    public function show($id)
    {
        $Category = Categorie::find($id);

        if (!$Category) {
            return response()->json(['message' => 'Categorie non trouvé'], 404);
        }

        return response()->json($Category);
    }

    /**
 * @OA\Delete(
 *     path="/api/category/delete/{id}",
 *     summary="Supprimer une catégorie",
 *     tags={"Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la catégorie",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Catégorie supprimée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Catégorie supprimée avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Catégorie non trouvée"
 *     )
 * )
 */

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

    /**
 * @OA\Put(
 *     path="/api/category/update/{id}",
 *     summary="Mettre à jour une catégorie",
 *     tags={"Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la catégorie",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Objet de la catégorie à mettre à jour",
 *         @OA\JsonContent(
 *             required={"name", "image"},
 *             @OA\Property(property="name", type="string", example="Nouveau nom de catégorie"),
 *             @OA\Property(property="image", type="string", format="url", example="https://exemple.com/nouvelle-image.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Catégorie mise à jour avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Nouveau nom de catégorie"),
 *             @OA\Property(property="image", type="string", format="url", example="https://exemple.com/nouvelle-image.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Catégorie non trouvée"
 *     )
 * )
 */
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
