<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethode;
use Exception;
use Illuminate\Http\Request;

class PaymentMethodeController extends Controller
{
    //

    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = PaymentMethode::query()
            ->where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }


    public function store(Request $request)
    {
        try {
            $folder = 'payments';

            $validator = PaymentMethode::validatedPaymentMethode($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cate = $validator->validated();
            $imagePath = uploadImage($request->file('image'), $folder);
            $cate['image']= $imagePath;
            $data = PaymentMethode::create($cate);

            return response()->json([
                'message' => 'paymentMethode added successfully',
                'success' => true,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }





    public function update(Request $request, $id)
    {
        try {
            $folder = 'payments';
            $PaymentMeth = PaymentMethode::find($id);
            if (!$PaymentMeth) {
                return response()->json(['message' => 'paymentMethode not found'], 404);
            }

            $validator = PaymentMethode::validatedPaymentMethode($request->all(), $PaymentMeth->id);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            if ($request->hasFile('image')) {
                deleteImage($PaymentMeth->image);
                $imagePath = uploadImage($request->file('image'), $folder);
                $data['image'] = $imagePath;
            }

            $PaymentMeth->update($data);

            return response()->json(['message' => 'PaymentMethode updated successfully',"data"=> $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }


    
    public function delete($id)
    {
        $PaymentMeth = PaymentMethode::find($id);
        if ($PaymentMeth) {
            deleteImage($PaymentMeth->image);
            $PaymentMeth->delete();
            return response()->json('PaymentMethode delete');
        }
        return response()->json(['message' => 'PaymentMethode not found'], 404);
    }
}
