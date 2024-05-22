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



/**
 * @OA\Post(
 *     path="/api/addresse/store",
 *     summary="Create a new address",
 *     tags={"Addresses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Address object to be created",
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "addresse", "telephone1", "zone"},
 *             @OA\Property(property="first_name", type="string", example="test"),
 *             @OA\Property(property="last_name", type="string", example="test"),
 *             @OA\Property(property="addresse", type="string", example="Hlm"),
 *             @OA\Property(property="zone", type="string", example="Dakar"),
 *             @OA\Property(property="quartier", type="string", example="Hlm"),
 *             @OA\Property(property="complement_addresse", type="string", example="centre ville"),
 *             @OA\Property(property="telephone1", type="string", example="123456789"),
 *             @OA\Property(property="telephone2", type="string", example="123456789")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="address created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="enregistré avec success"),
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="first_name", type="string", example="test"),
 *                 @OA\Property(property="last_name", type="string", example="test"),
 *                 @OA\Property(property="zone", type="string", example="Dakar"),
 *                 @OA\Property(property="addresse", type="string", example="Hlm"),
 *                 @OA\Property(property="quartier", type="string", example="Hlm"),
 *                 @OA\Property(property="complement_addresse", type="string", example="centre ville"),
 *                 @OA\Property(property="telephone1", type="string", example="123456789"),
 *                 @OA\Property(property="telephone2", type="string", example="123456790"),
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
            $validator = Address::validatedAddresse($request->all());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();
            $addresses = Address::create($data);
            return response()->json(['data' => $addresses, 'success' => true], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Address registration failed'], 500);
        }
    }

    /**
 * @OA\Get(
 *     path="/api/addresse/",
 *     summary="Get user addresses",
 *     tags={"Addresses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User addresses retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="addresses", type="array",
 *                 @OA\Items(ref="#/components/schemas/Address")
 *             ),
 *             @OA\Property(property="success", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Addresses not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Addresses non trouvé")
 *         )
 *     )
 * )
 */

    public function getUserAddress()
    {
        $userId = Auth::id();
        $addressesUser = Address::where('user_id', $userId)->get();

        if (!$addressesUser) {
            return response()->json(['message' => 'Addresses non trouvé'], 404);
        }
        return response()->json(['addresses' => $addressesUser, 'success' => true], 200);
    }


    /**
 * @OA\Put(
 *     path="/api/addresse/update/{id}'",
 *     summary="Update user address",
 *     tags={"Addresses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the address to update",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Address data to update",
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "addresse", "telephone1", "zone"},
 *             @OA\Property(property="first_name", type="string", example="test"),
 *             @OA\Property(property="last_name", type="string", example="test"),
 *             @OA\Property(property="addresse", type="string", example="Hlm"),
 *             @OA\Property(property="zone", type="string", example="Dakar"),
 *             @OA\Property(property="quartier", type="string", example="Hlm"),
 *             @OA\Property(property="complement_addresse", type="string", example="centre ville"),
 *             @OA\Property(property="telephone1", type="string", example="123456789"),
 *             @OA\Property(property="telephone2", type="string", example="123456789")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Address updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="modification faite"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="first_name", type="string", example="test"),
 *                 @OA\Property(property="last_name", type="string", example="test"),
 *                 @OA\Property(property="addresse", type="string", example="Hlm"),
 *                 @OA\Property(property="zone", type="string", example="Dakar"),
 *                 @OA\Property(property="quartier", type="string", example="Hlm"),
 *                 @OA\Property(property="complement_addresse", type="string", example="centre ville"),
 *                 @OA\Property(property="telephone1", type="string", example="123456789"),
 *                 @OA\Property(property="telephone2", type="string", example="123456789")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Address not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Addresses non trouvé")
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

            $addresses = Address::find($id);
            if (!$addresses) {
                return response()->json(['message' => 'Addresses non trouvé'], 404);
            }

            $validator = Address::validatedAddresse($request->all(), $addresses->id);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $addresses->update($data);
            return response()->json(["message" => 'modification faite', "data" => $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }
}
