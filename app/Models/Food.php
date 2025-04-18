<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'name',
        'price',
        'image',
        'description',
        'type',
        'nutrition_facts',
        'created_at',
        'updated_at'
    ];
}
