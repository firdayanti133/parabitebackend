<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantMenu extends Model
{
    use HasFactory;

    protected $table = 'merchant_menu_list';

    protected $primaryKey = 'id';

    protected $fillable = [
        'merchant_id',
        'food_id',
        'stocks',
        'price',
        'status',
        'created_at',
        'updated_at'
    ];
}
