<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoBanniere;
use Exception;
use Illuminate\Http\Request;

class PromoBanniereController extends Controller
{
    //

      /**
 * @OA\Get(
 *     path="/api/promoBanniere",
 *     summary="Get all promo",
 *     tags={"Promo"},
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="title", type="string", example="Promo Tabaski"),
 *                 @OA\Property(property="taux", type="integer", example="60"),
 *                 @OA\Property(property="available", type="boolean", example="true"),
 *                 @OA\Property(property="image", type="string", format="url", example="Promos/Tabaski.png")
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

        $data = PromoBanniere::query()
            ->where('title', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)->get();

        return response()->json($data, 200);
    }

        /**
 * @OA\Post(
 *     path="/api/promoBanniere/store",
 *     summary="Create a promo",
 *     tags={"Promo"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Promo object to be created",
 *         @OA\JsonContent(
 *             required={"title", "image", "taux"},
 *                 @OA\Property(property="title", type="string", example="Promo Tabaski"),
 *                 @OA\Property(property="taux", type="integer", example="60"),
 *                 @OA\Property(property="available", type="boolean", example="true"),
 *                 @OA\Property(property="image", type="string", format="url", example="Tabaski.png")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Promo created successfully",
 *         @OA\JsonContent(
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="title", type="string", example="Promo Tabaski"),
 *                 @OA\Property(property="taux", type="integer", example="60"),
 *                 @OA\Property(property="available", type="boolean", example="true"),
 *                 @OA\Property(property="image", type="string", format="url", example="Promos/Tabaski.png")
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


     /**
 * @OA\Get(
 *     path="/api/promoBanniere/{id}",
 *     summary="get a promo",
 *     tags={"Promo"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la promotion",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Promo récupérée avec succès",
 *         @OA\JsonContent(
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="title", type="string", example="Promo Tabaski"),
 *                 @OA\Property(property="taux", type="integer", example="60"),
 *                 @OA\Property(property="available", type="boolean", example="true"),
 *                 @OA\Property(property="image", type="string", format="url", example="Promos/Tabaski.png")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Promotion non trouvée"
 *     )
 * )
 */   
    public function show($id)
    {
        $promoBanniere = PromoBanniere::find($id);

        if (!$promoBanniere) {
            return response()->json(['message' => 'PromoBanniere non trouvé'], 404);
        }

        return response()->json($promoBanniere);
    }

        /**
 * @OA\Delete(
 *     path="/api/promoBanniere/{id}",
 *     summary="get a promo",
 *     tags={"Promo"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la promotion",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="PromoBanniere delete ",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Promotion supprimée avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="PromoBanniere  non trouvée"
 *     )
 * )
 */
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

        /**
 * @OA\Put(
 *     path="/api/promoBanniere/update/{id}",
 *     summary="Mettre à jour une Promotion",
 *     tags={"Promo"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la Promotion",
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Objet de la Promotion à mettre à jour",
 *         @OA\JsonContent(
 *             required={"title", "image", "taux"},
 *                 @OA\Property(property="title", type="string", example="Promo Tabaski"),
 *                 @OA\Property(property="taux", type="integer", example="60"),
 *                 @OA\Property(property="available", type="boolean", example="true"),
 *                 @OA\Property(property="image", type="string", format="url", example="Tabaski.png")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Promotion mise à jour avec succès",
 *                 @OA\Property(property="title", type="string", example="Promo Tabaski"),
 *                 @OA\Property(property="taux", type="integer", example="60"),
 *                 @OA\Property(property="available", type="boolean", example="true"),
 *                 @OA\Property(property="image", type="string", format="url", example="Promos/Tabaski.png")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Jeton d'authentification manquant ou invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Promotion non trouvée"
 *     )
 * )
 */
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
