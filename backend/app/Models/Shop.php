<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = ['shop_domain', 'access_token'];

    public function getShopDomainAttribute($value)
    {
        return strtolower($value);
    }

    public function setShopDomainAttribute($value)
    {
        $this->attributes['shop_domain'] = strtolower($value);
    }


}


