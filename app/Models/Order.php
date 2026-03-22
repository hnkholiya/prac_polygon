<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
   protected $fillable = [
        'tx_hash', 
        'wallet_address', 
        'amount', 
        'status'
    ];
}

