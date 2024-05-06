<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\Request;
use Paydunya\Checkout\CheckoutInvoice;
use Illuminate\Support\Facades\Config;
use Paydunya\Checkout\Store;
use Paydunya\Setup;


class CheckoutPaydunyaController extends Controller
{
    //

    public function createPayment($order, $itemsdetails)
    {
        try {
            // Configuration des clés d'API PayDunya
            Setup::setMasterKey(Config::get('paydunya.master_key'));
            Setup::setPublicKey(Config::get('paydunya.public_key'));
            Setup::setPrivateKey(Config::get('paydunya.private_key'));
            Setup::setToken(Config::get('paydunya.token'));
            Setup::setMode(Config::get('paydunya.mode'));
            Store::setName("lowpriceclone");
            Store::setLogoUrl("https://volkeno.com/images/logo.svg");



            $invoice = new CheckoutInvoice();

            foreach ($itemsdetails as $item) {
                $item['order_id'] = $order->id;
                $validator = OrderItem::validatedOrderItem($item);
                if ($validator->fails()) {
                    return response()->json(["errors"=> $validator->errors()], 422);
                }
                $invoice->addItem($item['name'], $item['quantity'], $item['prix'], $item['subTotal']);
                $invoice->addCustomData("order_id", $order->id);
                $invoice->addCustomData("orderidentifiant", $order->orderidentify);
                $invoice->addCustomData("livraison", $order->methodelivraisons->price);
            }
            $invoice->addCustomData("order_id", $order->id);
            $invoice->addCustomData("details", $itemsdetails);

            $invoice->setTotalAmount($order->prixTotal);

            // Définir les URLs de callback, d'annulation et de retour
            // Store::setCallbackUrl("http://127.0.0.1:8000/api/callback_url.php");
            // Store::setCancelUrl("http://127.0.0.1:8000/cancel_url.php");
            $invoice->setReturnUrl("http://127.0.0.1:8000/return_url");
            $invoice->setCancelUrl("http://127.0.0.1:8000/cancel");
            if($invoice->create()) {
                // Retourner l'URL de paiement
                return response()->json(["success"=>true, "paydunya_payment_url"=>$invoice->getInvoiceUrl()], 201);
            } else {
                dd($invoice->response_text);
                return response()->json(["error"=>"Erreur lors de la création de la facture PayDunya"], 500);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    


}
