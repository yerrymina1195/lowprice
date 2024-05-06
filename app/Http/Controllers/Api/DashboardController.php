<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{



    public function index()
    {
        $userCount = User::count();

        $produitCount = Produit::count();

        $orderCount = Order::count();

        return response()->json([
            'nbrUsers' => $userCount,
            'nbrProduct' => $produitCount,
            'nbrOrder' => $orderCount,
        ],200);
    }
}
