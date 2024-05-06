<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    //
    
    public function store (Request $request)
    {try {
        $validator = Address::validatedAddresse($request->all());
        if ($validator->fails()){
           return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id']=Auth::id();
        $addresses= Address::create($data);
        return response()->json(['data'=>$addresses,'success'=> true],201);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'message' => 'Address registration failed'], 500);
    }

    }
    public function getUserAddress ()
    {
        $userId= Auth::id();
        $addressesUser= Address::where('user_id',$userId )->get();

        if(!$addressesUser){
            return response()->json(['message' => 'Addresses non trouvé'], 404);
        }
        return response()->json(['addresses' => $addressesUser,'success'=>true], 200);
    }

    public function update(Request $request, $id)
    {
        try {
       
            $addresses= Address::find($id);
        if (!$addresses) {
            return response()->json(['message' => 'Addresses non trouvé'], 404);
        }

        $validator= Address::validatedAddresse($request->all(),$addresses->id);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $addresses->update($data);
        return response()->json(["message"=>'modification faite',"data"=>$data],200);

        } catch (Exception $e) {
            return response()->json($e, 500);
        }

    }
}
