<?php
namespace App\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use HasFactory;
    protected $table   = 'categories';
    protected $guarded = [];

    public function childs() {
        return $this->hasMany( Category::class, 'parent_id' );
    }

    public function products() {
        return $this->hasMany( Product::class );
    }
}