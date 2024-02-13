<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable= [
        'name'
    ];

   /**
     * The post that belong to the categories.
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'category_post');
    }
}
