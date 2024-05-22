<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    //

        /**
     * @OA\Get(
     *     path="/api/favorite",
     *     summary="Get products favorite",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Product"),
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

    public function index()
    {
        $favoriteProducts = auth()->user()->favoriteProducts()->with('images')->get()->makeHidden(['quantity','pivot','images']);

        $transformedProducts = $favoriteProducts->map(function ($product) {
            $firstImage = $product->images->first();
            $productData = $product->toArray();
            $productData['image'] = $firstImage ? $firstImage->image  : null;
            return $productData;
        });
        return response()->json(["data"=> $transformedProducts]);
    }

    /**
 * @OA\Post(
 *     path="/api/favorite/{productid}/favorite",
 *     summary="Add product to favorites",
 *     tags={"Products"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="produit_id",
 *         in="path",
 *         required=true,
 *         description="ID of the product to add to favorites",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product added to favorites successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="favoris ajouté")
 *         )
 *     )
 * )
 */

    public function addFavorite($productid)
    {
        auth()->user()->favoriteProducts()->attach($productid);

        return response()->json(["message"=>'favoris ajouté']);
    }

    /**
 * @OA\Delete(
 *     path="/api/favorite/{productid}",
 *     summary="Remove product from favorites",
 *     tags={"User Favorites"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="produit_id",
 *         in="path",
 *         required=true,
 *         description="ID of the product to remove from favorites",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product removed from favorites successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="favoris supprimé")
 *         )
 *     )
 * )
 */

    public function removeFavorite($productid)
    {
        auth()->user()->favoriteProducts()->detach($productid);

        return response()->json(["message"=>'favoris supprimé']);
    }



}
