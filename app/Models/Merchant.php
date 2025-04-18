<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $table = 'merchants';

    protected $primaryKey = 'id';   

    protected $fillable = [
        'owener_id',
        'name',
        'logo',
        'created_at',
        'updated_at'
    ];
}
