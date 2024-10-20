<?php

namespace App\Services\Store;

use App\Models\Store;
use Illuminate\Support\Facades\Log;

class StoreService
{

    public function getAllStores()
    {
        return Store::all();
    }

    public function getStoreById($id)
    {
        return Store::find($id);
    }


    public function createStore(array $data)
    {
        return Store::create($data);
    }

 
    public function updateStore($id, array $data)
    {
        $store = Store::find($id);
        if ($store) {
            $store->update($data);
        }
        return $store;
    }

  
    public function deleteStore($id)
    {
        $store = Store::find($id);
        if ($store) {
            return $store->delete();
        }
        return false;
    }

    public function updateStatus($id, $status)
    {
        $store = Store::find($id);
        if ($store) {
            $store->status = $status;
            $store->save();
        }
        return $store;
    }
}
