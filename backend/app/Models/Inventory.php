<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public static function demo(){
        return [
            'data' => [[
                'id' => 1,
                'name' => 'pop',
                'quantity' => 50,

            ],[
                'id' => 1,
                'name' => 'pop',
                'quantity' => 50,

            ]]
            ];

    }
}
