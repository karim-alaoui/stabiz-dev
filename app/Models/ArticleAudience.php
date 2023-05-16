<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArticleAudience
 * @package App\Models
 */
class ArticleAudience extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['article_id', 'audience'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public $timestamps = false;
}
