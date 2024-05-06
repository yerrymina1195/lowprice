<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Paydunya\Checkout\CheckoutInvoice;
use Illuminate\Support\Facades\Config;
use Paydunya\Checkout\Store;
use Paydunya\Setup;
use App\Models\Order;
use Exception;

class PaymentCallbackController extends Controller
{

    // public function handleCallback(Request $request)
    // {
    //     try {

    //         $token = $request->get('token');

    //         // Créer une instance de CheckoutInvoice
    //         $invoice = new CheckoutInvoice();

    //         if ($invoice->confirm($token)) {
    //             $status = $invoice->getStatus();

    //             $orderId = $invoice->getCustomData("order_id");

    //             if ($status === 'completed') {
    //                 $order = Order::find($orderId);
    //                 $order->status = true;
    //                 $order->save();
    //             }

    //             return response()->json(['success' => true, 'message' => 'Paiement confirmé avec succès'], 200);
    //         } else {
    //             // Le paiement n'a pas été confirmé
    //             $status = $invoice->getStatus();
    //             $responseText = $invoice->response_text;
    //             $responseCode = $invoice->response_code;

    //             // Faire le traitement en cas d'erreur

    //             // Retourner une réponse appropriée
    //             return response()->json(['success' => false, 'message' => 'Erreur lors de la confirmation du paiement', 'status' => $status, 'response_text' => $responseText, 'response_code' => $responseCode], 500);
    //         }
    //     } catch (Exception $e) {
    //         // Gestion des erreurs
    //     }
    // }


    public function handleReturn(Request $request)
{
    Setup::setMasterKey(Config::get('paydunya.master_key'));
    Setup::setPublicKey(Config::get('paydunya.public_key'));
    Setup::setPrivateKey(Config::get('paydunya.private_key'));
    Setup::setToken(Config::get('paydunya.token'));
    Setup::setMode(Config::get('paydunya.mode'));
    Store::setName("lowpriceclone");
    $token = $request->get('token');
    $invoice = new CheckoutInvoice();
    if ($invoice->confirm($token)) {
        $status = $invoice->getStatus();
        $totalAmount = $invoice->getTotalAmount();
        $orderId = $invoice->getCustomData("order_id");
        $livraison = $invoice->getCustomData("livraison");
        $pdf=$invoice->getReceiptUrl();
        $details=$invoice->getCustomData("details");

                        if ($status === 'completed') {
                    $order = Order::find($orderId);
                    $order->ispaid =true;
                    $order->save();
                }

        return view('confirmation')->with(['status' => $status, 'totalAmount' => $totalAmount,'pdf'=>$pdf, 'details'=>$details,'livraison'=>$livraison]);
    } else {

        $invoice->response_text;
        return response()->json($invoice->response_text);
    }
}

}


