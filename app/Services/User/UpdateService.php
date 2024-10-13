<?php

namespace App\Services\User;

use Illuminate\Support\Arr;
use DB;
use App\Services\BaseServiceInterface;

/**
 * This service is to update a user.
 */
class UpdateService implements BaseServiceInterface
{
    const USER_UPDATE_FIELDS = ['first_name', 'last_name', 'phone_number', 'avatar', 'username', 'address', 'city', 'state' ];

    protected $user;
    protected $data;

    public function __construct($user, $data)
    {
       
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Update the user 
     */
    public function run()
    {
     
        return $this->update();
    }

    protected function update()
    {
        return DB::transaction(function () {
            $this->updateModel($this->user, static::USER_UPDATE_FIELDS, $this->data);
            return $this->user;
        });
    }

    /**
     * Setting attributes like this, so that events are fired
     * if we just do a model update directly from array, events will not be fired
     */
    private function updateModel($model, $update_fields, $data)
    {
        foreach ($update_fields as $key) {
            if (Arr::has($data, $key)) {
                $model->setAttribute($key, $data[$key]);
            }
        }
        //save will only actually save if the model has changed
        $model->save();
    }
}