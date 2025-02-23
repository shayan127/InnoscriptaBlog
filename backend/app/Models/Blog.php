<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'snippet',
        'content',
        'url',
        'published_at',
        'source', // todo
        'origin',
        'authors',
        'image',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'authors' => 'array', // todo
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'blog_category');
    }
}
