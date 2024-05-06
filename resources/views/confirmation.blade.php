<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
</head>
<body>
    <h1>Payment Successful</h1>
    

    <p>Your payment has been processed successfully. <mark>total payé = {{ $totalAmount }}</mark></p>
    <a href={{ $pdf }} download> Telecharger le recu de paiement</a>

<h1> Details </h1>
    @foreach ( $details as $item )
    <p class="">name: {{ $item['name'] }}</p>
    <p class="">quantity: {{ $item['quantity']}}</p>
    <p class="">prixUnitaire: {{ $item['prix']}}</p>
    <p class="">prixsubTotal: {{ $item['subTotal'] }}</p>
    
    <hr>
    @endforeach

    <p class="">livraison: {{ $livraison}}</p>
</body>
</html>