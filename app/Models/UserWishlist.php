<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWishlist extends Model
{
    use HasFactory;

    protected $table = 'user_wishlist';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'menu_id',
        'created_at',
        'updated_at'
    ];
}
