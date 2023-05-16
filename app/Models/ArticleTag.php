<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArticleTag
 * @package App\Models
 */
class ArticleTag extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $touches = ['article'];

    public $timestamps = false;

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
