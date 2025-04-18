<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'merchant_id',
        'location_id',
        'total_price',
        'order_type',
        'payment_method',
        'status',
        'created_at',
        'updated_at'
    ];
}
