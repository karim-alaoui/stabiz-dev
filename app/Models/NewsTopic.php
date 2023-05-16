<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\NewsTopicFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

/**
 * Class NewsTopic
 * @package App\Models
 * @method static userSide()
 */
class NewsTopic extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Searchable;

    protected $fillable = ['title', 'body', 'visible_to', 'show_after', 'hide_after', 'added_by_staff_id'];

    protected $hidden = ['deleted_at'];

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs(): string
    {
        return model_index('NewsTopic');
    }

    /**
     * Get indexable data for this model
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            /**
             * for the timestamp related field, keep the timestamp to only date and not time
             * that way it would be easy to search. So, if the show after value is 2021-06-09 10:10:10
             * then providing 2021-06-09 would not match, since the time different would result in
             * different unix timestamp values
             */
            'show_after' => $this->show_after ? strtotime(Carbon::parse($this->show_after)->format('Y-m-d')) : null,
            'hide_after' => $this->hide_after ? strtotime(Carbon::parse($this->hide_after)->format('Y-m-d')) : null,
            'added_by_staff_id' => $this->added_by_staff_id
        ];
    }

    /**
     * News and topic which is usually shown to the users
     * @param Builder $query
     * @return Builder
     */
    public function scopeUserSide(Builder $query): Builder
    {
        return $query->where('show_after', '<=', DB::raw('now()'))
            ->where(function ($q) {
                $q->whereNull('hide_after')
                    ->orWhere('hide_after', '>=', DB::raw('now()'));
            });
    }

    /**
     * Factory attached to this model
     * @return NewsTopicFactory
     */
    protected static function newFactory(): NewsTopicFactory
    {
        return NewsTopicFactory::new();
    }
    /**
     * Set the visible_to attribute as JSON
     *
     * @param array $value
     */
    public function setVisibleToAttribute($value)
    {
        $this->attributes['visible_to'] = json_encode($value);
    }

}
