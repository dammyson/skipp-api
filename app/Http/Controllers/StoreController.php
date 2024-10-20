<?php

namespace App\Http\Controllers;

use App\Services\Store\StoreService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController as BaseController;

class StoreController extends BaseController
{
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function index()
    {
        $stores = $this->storeService->getAllStores();
        return $this->sendResponse($stores, 'stores fetched successfully.', 200);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'company_rc' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'website' => 'nullable|url',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'logo' => 'nullable|string',
        ]);

        $store = $this->storeService->createStore($validatedData);
        return $this->sendResponse($store, 'store created successfully.', 201);
    }


    public function show($id)
    {
        $store = $this->storeService->getStoreById($id);

        if ($store) {
            return $this->sendResponse($store, 'store fetched successfully.', 200);
        }

        return response()->json(['message' => 'Store not found'], 404);
    }


 
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'company_rc' => 'nullable|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone_number' => 'sometimes|string|max:20',
            'website' => 'nullable|url',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'logo' => 'nullable|string',
        ]);

        $store = $this->storeService->updateStore($id, $validatedData);

        if ($store) {
            return response()->json($store);
        }

        return response()->json(['message' => 'Store not found or update failed'], 404);
    }


    public function destroy($id)
    {
        $deleted = $this->storeService->deleteStore($id);

        if ($deleted) {
            return response()->json(['message' => 'Store deleted successfully']);
        }

        return response()->json(['message' => 'Store not found or deletion failed'], 404);
    }
}
