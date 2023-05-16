<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class FdrPfdArea
 * @package App\Models
 */
class FdrPfdPrefecture extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $table = 'fdr_pfd_prefectures';

    protected $fillable = ['founder_profile_id', 'prefecture_id'];

    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'prefecture_id');
    }

    public $timestamps = false;
}
