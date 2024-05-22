<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoProduit;
use Exception;
use Illuminate\Http\Request;

class PromoProductController extends Controller
{
    //

    // public function index()
    // {
    //     $perPage = request('per_page', 10);
    // $sortField = request('sort_field', 'created_at');
    // $sortDirection = request('sort_direction', 'desc');

    // $data = PromoProduit::with(['produits', 'promos'])
    //     ->orderBy($sortField, $sortDirection)
    //     ->paginate($perPage);

    // return response()->json($data, 200);
    // }


    //

    /**
     * @OA\Get(
     *     path="/api/promoProduct/",
     *     summary="Get paginated list of promotional products",
     *     tags={"Promotional Products"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (default: 10)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Field to sort by (default: created_at)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (default: desc)",
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of promotional products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="prixpromo", type="number"),
     *                 @OA\Property(property="produitname", type="string"),
     *                 @OA\Property(property="produitnameprix", type="number"),
     *                 @OA\Property(property="produitImage", type="string", nullable=true),
     *                 @OA\Property(property="promotitle", type="string"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string")
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

        $data = PromoProduit::query()
            ->with(['produits', 'promos'])
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        $transformedData = $data->map(function ($promoProduit) {
            // dd($promoProduit->produits->images);
            return [
                'id' => $promoProduit->id,
                'prixpromo' => $promoProduit->prixpromo,
                'produitname' => $promoProduit->produits->name,
                'produitnameprix' => $promoProduit->produits->prix,
                'produitImage' => $promoProduit->produits->images->isEmpty() ? null : $promoProduit->produits->images[0]->image,
                'promotitle' => $promoProduit->promos->title,
                'created_at' => $promoProduit->created_at,
                'updated_at' => $promoProduit->updated_at,
            ];
        });

        return response()->json($transformedData, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/promoProduct/",
     *     summary="Add a product to promotion",
     *     tags={"Promotional Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Promotional product object to be created",
     *         @OA\JsonContent(
     *             required={"produit_id", "promobanniere_id", "prixpromo"},
     *             @OA\Property(property="produit_id", type="integer", example="1"),
     *             @OA\Property(property="promobanniere_id", type="integer", example="1"),
     *             @OA\Property(property="prixpromo", type="number", example="50.00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produit added with promo successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Promotional product added successfully"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="produit_id", type="integer"),
     *                 @OA\Property(property="promobanniere_id", type="integer"),
     *                 @OA\Property(property="prixpromo", type="number"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string")
     *             )
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



    public function store(Request $request)
    {
        try {

            $validator = PromoProduit::validatedPromoProduit($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            $promoproduct = PromoProduit::create($data);

            return response()->json([
                'message' => 'Produit added with promo successfully',
                'success' => true,
                'data' =>  $promoproduct
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/promoProduct/update/{id}",
     *     summary="Update an existing product",
     *     tags={"Promotional Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the promo product to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated product object",
     *         @OA\JsonContent(
     *             required={"produit_id", "promobanniere_id", "prixpromo"},
     *             @OA\Property(property="produit_id", type="integer", example="1"),
     *             @OA\Property(property="promobanniere_id", type="integer", example="1"),
     *             @OA\Property(property="prixpromo", type="number", example="50.00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="'PromoProduit updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="PromoProduit updated successfully"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="produit_id", type="integer"),
     *                 @OA\Property(property="promobanniere_id", type="integer"),
     *                 @OA\Property(property="prixpromo", type="number"),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product not found")
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

    public function update(Request $request, $id)
    {
        try {
            $promoProduits = PromoProduit::find($id);
            if (!$promoProduits) {
                return response()->json(['message' => 'PromoProduit not found'], 404);
            }

            $validator = PromoProduit::validatedPromoProduit($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            $promoProduits->update($data);

            return response()->json(['message' => 'PromoProduit updated successfully', $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/promoProduct/delete/{id}",
     *     summary="Delete an existing product",
     *     tags={"Promotional Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the promo product to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="deleted successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produit not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product not found"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string"),
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        $promoProduits = PromoProduit::find($id);
        if (!$promoProduits) {
            return response()->json(['message' => 'PromoProduit not found'], 404);
        }

        $promoProduits->delete();

        return response()->json(["message" => 'deleted successfully']);
    }
}
