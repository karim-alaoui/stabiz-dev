<?php

namespace App\Models;

use Database\Seeders\Category4ArticleSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Categories for article
 */
class Category4Article extends Model
{
    use HasFactory;

    protected $table = 'categories_for_article';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * @return mixed
     */
    protected static function newFactory(): mixed
    {
        return Category4ArticleSeeder::new();
    }
}
