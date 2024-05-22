<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Produit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/product/store",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product object to be created",
     *         @OA\JsonContent(
     *             required={"name", "categorie_id", "image"},
     *             @OA\Property(property="name", type="string", example="velo mini"),
     *             @OA\Property(property="categorie_id", type="integer", example="5"),
     *             @OA\Property(property="sub_categorie_id", type="integer", nullable=true),
     *             @OA\Property(property="image", type="string", example="produits/velo.jpeg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="enregistré avec success"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="velo mini"),
     *                 @OA\Property(property="categorie_id", type="integer", example="5"),
     *                 @OA\Property(property="sub_categorie_id", type="integer", nullable=true),
     *                 @OA\Property(property="created_at", type="string", example="2024-05-16T14:36:34.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-05-16T14:36:34.000000Z")
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
            $validator = Produit::validatedProduit($request->all());

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            $data = $validator->validate();

            $produits = Produit::create($data);

            addImagesToProduct($produits->id, $request);

            return response()->json(["success" => true, "data" => $produits, "message" => "enregistré avec success"], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/product/",
     *     summary="Search products and display ",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32",
     *             example=2
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"created_at", "name", "prix"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="categorie_id",
     *         in="query",
     *         description="Category ID",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="subcategorie_id",
     *         in="query",
     *         description="Subcategory ID",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search in product name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum price",
     *         required=false,
     *         @OA\Schema(
     *             type="number",
     *             format="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maximum price",
     *         required=false,
     *         @OA\Schema(
     *             type="number",
     *             format="float"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
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


    public function searchProducts(Request $request)
    {
        $perPage = request('per_page', 2);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Produit::query()->where('name', 'like', "%{$search}%")
            ->with(['reviews', 'images'])->withAvg('reviews', 'rating')
            ->orderBy($sortField, $sortDirection);


        if ($request->has('category_id')) {
            $category = Categorie::findOrFail($request->category_id);
            $query->where(function ($query) use ($category) {
                $query->whereHas('subcategories', function ($subquery) use ($category) {
                    $subquery->where('category_id', $category->id);
                })
                    ->orWhereDoesntHave('subcategories');
            });
        }


        if ($request->has('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where('name', 'like', "%$keyword%");
        }

        if ($request->has('min_price')) {
            $minPrice = $request->min_price;
            $query->where('prix', '>=', $minPrice);
        }

        if ($request->has('max_price')) {
            $maxPrice = $request->max_price;
            $query->where('prix', '<=', $maxPrice);
        }

        $products = $query->get();

        // $products = $query->paginate($perPage);

        return response()->json($products);
    }

    /**
     * @OA\Put(
     *     path="/api/product/update/{id}",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated product object",
     *         @OA\JsonContent(
     *             required={"name", "categorie_id", "image"},
     *             @OA\Property(property="name", type="string", example="Updated Product Name"),
     *             @OA\Property(property="categorie_id", type="integer", example="5"),
     *             @OA\Property(property="sub_categorie_id", type="integer", nullable=true),
     *             @OA\Property(property="image", type="string", example="product.jpeg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="produit  updated successfully"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="Updated Product Name"),
     *                 @OA\Property(property="categorie_id", type="integer", example="5"),
     *                 @OA\Property(property="sub_categorie_id", type="integer", nullable=true),
     *                 @OA\Property(property="created_at", type="string", example="2024-05-16T14:36:34.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-05-16T14:36:34.000000Z")
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
            $produit = Produit::find($id);
            if (!$produit) {
                return response()->json(['message' => 'produit  not found'], 404);
            }

            $validator = Produit::validatedProduit($request->all(), $produit->id);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            addImagesToProduct($produit->id, $request);

            $produit->update($data);

            return response()->json(['message' => 'produit  updated successfully', $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/product/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="produit  delete successfully"),
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
        try {
            $produit = Produit::find($id);
            if (!$produit) {
                return response()->json(['message' => 'produit  not found'], 404);
            }
            foreach ($produit->images as $image) {
                Storage::disk('public')->delete($image->image);
            }

            $produit->delete();

            return response()->json(['message' => 'produit  delete successfully'], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/product/show/{id}",
     *     summary="Get a product by ID",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to retrieve",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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

    public function show($id)
    {
        try {
            $produit = Produit::with('images')->find($id);
            if (!$produit) {
                return response()->json(['message' => 'produit  not found'], 404);
            }

            return response()->json($produit);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/product/topProducts",
     *     summary="Get top rated products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Top rated products retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product"),
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

    public function topProducts()
    {
        $topProduits = Produit::withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(10)
            ->get();

        return response()->json([$topProduits]);
    }
}
