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

    public function updateStatut(Request $request, $id)
    {
        try {

            $commande = Order::find($id);
            if (!$commande) {
                return response()->json(['message' => 'Addresses non trouvé'], 404);
            }
            $validator = Validator::make($request->all(), [
                'statut' => 'required|in:en cours,terminé,annulé,livré',
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
