<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use App\Models\Produit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{



/**
 * @OA\Post(
 *     path="/api/order/store",
 *     summary="Create a new order",
 *     tags={"Orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Order object to be created",
 *         @OA\JsonContent(
 *             required={"paymentmethode_id", "methodelivraison_id", "addresse_id", "prixTotal", "items"},
 *             @OA\Property(property="paymentmethode_id", type="integer", example="3"),
 *             @OA\Property(property="methodelivraison_id", type="integer", example="2"),
 *             @OA\Property(property="addresse_id", type="integer", example="1"),
 *             @OA\Property(property="prixTotal", type="number", example="250000"),
 *             @OA\Property(
 *                 property="items",
 *                 type="array",
 *                 @OA\Items(
 *                     required={"produit_id", "quantity", "subTotal"},
 *                     @OA\Property(property="produit_id", type="integer", example="28"),
 *                     @OA\Property(property="quantity", type="integer", example="1"),
 *                     @OA\Property(property="subTotal", type="number", example="252000")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Order created successfully"),
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="paymentmethode_id", type="integer"),
 *                 @OA\Property(property="methodelivraison_id", type="integer"),
 *                 @OA\Property(property="addresse_id", type="integer"),
 *                 @OA\Property(property="prixTotal", type="number"),
 *                 @OA\Property(
 *                     property="items",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="produit_id", type="integer"),
 *                         @OA\Property(property="quantity", type="integer"),
 *                         @OA\Property(property="subTotal", type="number")
 *                     )
 *                 ),
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
            DB::beginTransaction();
            $validator = Order::validatedOrder($request->all());

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = auth()->id();
            $data['orderidentify'] = generateID();

            $order = Order::create($data);

            $itemsdetails = [];

            foreach ($request->items as $item) {
                $item['order_id'] = $order->id;
                $validator = OrderItem::validatedOrderItem($item);

                if ($validator->fails()) {
                    return response()->json(["errors" => $validator->errors()], 422);
                }


                $orditems = $order->items()->create($item);

                $items[] = $orditems;
            }

            $itemsdetails = $this->recupdetail($items);

            $type = $order->paymentmethodes->type;
            if ($type === 'paydunya') {
                $checkoutController = new CheckoutPaydunyaController();
                $response =  $checkoutController->createPayment($order, $itemsdetails);

                DB::commit();
                return $response;
            } else

                return response()->json(["success" => true, "data" => $order, "message" => "enregistré avec succès", "items" => $itemsdetails], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    private function recupdetail($items)
    {
        $detailedItems = [];

        foreach ($items as $item) {
            $orderItem = OrderItem::findOrFail($item->id);
            $product = $orderItem->produits;

            $detailedItem = [
                'produit_id' => $item->produit_id,
                'quantity' => $item->quantity,
                'subTotal' => $item->subTotal,
                'name' => $product->name,
                'prix' => $product->prix
            ];

            $detailedItems[] = $detailedItem;
        }

        return $detailedItems;
    }


    /**
     * @OA\Get(
     *     path="/api/order/",
     *     summary="Search order and display ",
     *     tags={"Orders"},
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
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search in product name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search_statut",
     *         in="query",
     *         description="statut order",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search_orderidentify",
     *         in="query",
     *         description="oreder identify",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
   *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Order created successfully"),
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="paymentmethode_id", type="integer"),
 *                 @OA\Property(property="methodelivraison_id", type="integer"),
 *                 @OA\Property(property="addresse_id", type="integer"),
 *                 @OA\Property(property="prixTotal", type="number"),
 *                 @OA\Property(
 *                     property="items",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="produit_id", type="integer"),
 *                         @OA\Property(property="quantity", type="integer"),
 *                         @OA\Property(property="subTotal", type="number")
 *                     )
 *                 ),
 *             )
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


    public function index()
    {
        $perPage = request('per_page', 10);
        $searchOrderidentify = request('search_orderidentify', '');
        $searchStatut = request('search_statut', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');
        $startDate = request('start_date');

        $data = Order::query()->when($searchOrderidentify, function ($query) use ($searchOrderidentify) {
            $query->where('orderidentify', 'like', "%{$searchOrderidentify}%");
        })
            ->when($searchStatut, function ($query) use ($searchStatut) {
                $query->where('statut', 'like', "%{$searchStatut}%");
            })->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('created_at', '=', Carbon::parse($startDate)->startOfDay());
            })
            ->with(['users:id,first_name,last_name', 'methodelivraisons:id,name'])
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        $totalOrders = Order::query()->count();

        return response()->json([
            'total_orders' => $totalOrders,
            'data' => $data,
        ], 200);
    }


    /**
 * @OA\Get(
 *     path="/api/order/mescommandes",
 *     summary="Get orders of the authenticated user",
 *     tags={"Orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of items per page",
 *         required=false,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="sort_field",
 *         in="query",
 *         description="Field to sort by",
 *         required=false,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="sort_direction",
 *         in="query",
 *         description="Sort direction (asc or desc)",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             enum={"asc", "desc"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of orders of the user",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="prixTotal", type="string"),
 *                 @OA\Property(property="orderidentify", type="string"),
 *                 @OA\Property(property="first_product_image", type="string"),
 *                 @OA\Property(property="statut", type="string"),
 *                 @OA\Property(property="created_at", type="string"),
 *                 @OA\Property(property="updated_at", type="string"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Unauthorized")
 *         )
 *     )
 * )
 */


    public function getUserOrders()
    {
        $perPage = request('per_page', 10);
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = Order::where('user_id', auth()->id())
            ->with(['users:id,first_name,last_name', 'methodelivraisons:id,name', 'items'])
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        $transformedData = $data->map(function ($order) {
            return [
                'id' => $order->id,
                'prixTotal' => $order->prixTotal,
                'orderidentify' => $order->orderidentify,
                'first_product_image' => $order->items->first()->produits->images->isEmpty() ? null : $order->items->first()->produits->images->first()->image,
                'statut' => $order->statut,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ];
        });


        return response()->json($transformedData, 200);
    }



    public function show($id)
    {
        try {
            $details = Order::with(['items', 'methodelivraisons', 'addresses', 'paymentmethodes'])->find($id);
            if (!$details) {
                return response()->json(['message' => 'details order not found'], 404);
            }
            $transformedData = [
                'id' => $details->id,
                'items' => $details->items->map(function ($item) {
                    return [
                        'product_image' => $item->produits->images->isEmpty() ? null : $item->produits->images[0]->image,
                        'product_name' => $item->produits->name,
                        'quantity' => $item->quantity,
                        'subTotal' => $item->subTotal
                    ];
                }),
                'methodelivraisons' => $details->methodelivraisons,
                'addresses' => $details->addresses,
                'paymentmethodes' => $details->paymentmethodes,
                'prixTotal' => $details->prixTotal,
                'statut' => $details->statut
            ];

            return response()->json($transformedData);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
 * @OA\Put(
 *     path="/api/order/updatestatut/{id}",
 *     summary="Update the status of an order",
 *     tags={"Orders"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the order",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="New status of the order",
 *         @OA\JsonContent(
 *             required={"statut"},
 *             @OA\Property(property="statut", type="string", example="en cours", enum={"en cours", "terminé", "annulé", "livré"}),
 *             @OA\Property(property="ispaid", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Modification réussie"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="statut", type="string"),
 *                 @OA\Property(property="ispaid", type="boolean"),
 *                 @OA\Property(property="created_at", type="string"),
 *                 @OA\Property(property="updated_at", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Order not found")
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

    public function updateStatut(Request $request, $id)
    {
        try {

            $commande = Order::find($id);
            if (!$commande) {
                return response()->json(['message' => 'Addresses non trouvé'], 404);
            }
            $validator = Validator::make($request->all(), [
                'statut' => 'required|in:en cours,terminé,annulé,livré',
                'ispaid'=>'boolean'
            ], [
                'required' => 'Le champ :attribute est requis.',
                'in' => "La valeur du champ :attribute doit être l'une des suivantes : :values.",
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }


            $commande->update([
                'statut' => $request->statut,
            ]);

            return response()->json(["message" => 'modification faite', "data" => $commande], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }
}
