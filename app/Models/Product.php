<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;
    protected $table   = 'products';
    protected $guarded = [];

    public function category() {
        return $this->belongsTo( Category::class );
    }
}