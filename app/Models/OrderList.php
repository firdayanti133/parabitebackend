<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderList extends Model
{
    use HasFactory;

    protected $table = 'order_list';

    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'created_at',
        'updated_at'
    ];
}
