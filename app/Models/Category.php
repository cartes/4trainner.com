<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'parent_id', 'order'];

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('orden');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

}
