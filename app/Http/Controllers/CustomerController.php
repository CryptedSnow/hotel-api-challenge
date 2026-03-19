<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('id')->get();
        if ($customers->isEmpty()) {
            return response()->json(['message' => 'No customers found.'], 404);
        }
        return CustomerResource::collection($customers);
    }

    public function store(Request $request)
    {
        try {
            $validations = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
            ]);
            $customer = Customer::create($validations);
            return (new CustomerResource($customer))->response()->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => "Customer Nº $id was not found."], 404);
        }
        return new CustomerResource($customer);
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::find($id);
            if (!$customer) {
                return response()->json(['message' => "Customer ID Nº $id was not found."], 404);
            }
            $validations = $request->validate([
                'first_name' => 'sometimes|required|string',
                'last_name' => 'sometimes|required|string',
            ]);
            $customer->update($validations);
            return (new CustomerResource($customer))->response()->setStatusCode(202);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => "Customer ID Nº $id was not found."], 404);
        }
        $customer_name = $customer->first_name . ' ' . $customer->last_name;
        $customer->delete();
        return response()->json(['message' => "$customer_name was deleted."], 200);
    }

}
