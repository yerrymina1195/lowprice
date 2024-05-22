<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Banniere;
use Exception;
use Illuminate\Http\Request;

class BanniereController extends Controller
{

  /**
 * @OA\Get(
 *     path="/api/banniere",
 *     summary="Get all bannieres",
 *     tags={"Banniere"},
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

        $data = Banniere::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }

    /**
 * @OA\Post(
 *     path="/api/banniere/store",
 *     summary="Create a new banniere",
 *     tags={"Banniere"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="banniere object to be created",
 *         @OA\JsonContent(
 *             required={"name", "image", "pagelink"},
 *             @OA\Property(property="name", type="string", example="New banniere"),
 *             @OA\Property(property="image", type="string", format="url", example="image.jpg"),
 *             @OA\Property(property="pagelink", type="string", format="url", example="https://example.com/")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="banniere created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example="1"),
 *             @OA\Property(property="name", type="string", example="New banniere"),
 *             @OA\Property(property="image", type="string", format="url", example="banniere/image.jpg"),
 *             @OA\Property(property="pagelink", type="string", format="url", example="https://example.com/")
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


    /**
 * @OA\Put(
 *     path="/api/banniere/update/{id}",
 *     summary="Mettre à jour une Banniere",
 *     tags={"Banniere"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du banniere",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Objet du banniere à mettre à jour",
 *         @OA\JsonContent(
 *             required={"name", "image", "pagelink"},
 *             @OA\Property(property="name", type="string", example="New banniere"),
 *             @OA\Property(property="image", type="string", format="url", example="image.jpg"),
 *             @OA\Property(property="pagelink", type="string", format="url", example="https://example.com/")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="vBanniere updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="New banniere"),
 *             @OA\Property(property="image", type="string", format="url", example="image.jpg"),
 *             @OA\Property(property="pagelink", type="string", format="url", example="https://example.com/")
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

    /**
 * @OA\Delete(
 *     path="/api/banniere/delete/{id}",
 *     summary="Supprimer une banniere",
 *     tags={"Banniere"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du baniiere",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Banniere delete",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Banniere supprimée avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Banniere non trouvée"
 *     )
 * )
 */
    
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
