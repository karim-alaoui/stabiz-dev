<?php /** @noinspection PhpUnused */

/** @noinspection PhpHierarchyChecksInspection */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * Class Article
 * @property mixed audiences
 * @property mixed tags
 * @package App\Models
 */
class Article extends Model
{
    use HasFactory;
    use RevisionableTrait;
    use Searchable;
    use SoftDeletes;

    /**
     * Remove old revisions (works only when used with $historyLimit)
     * @var bool
     */
    protected bool $revisionCleanup = true;
    protected int $historyLimit = 20;
    protected bool $revisionCreationsEnabled = true;
    protected array $dontKeepRevisionOf = ['updated_at', 'created_at'];


    protected $fillable = [
        'title',
        'description',
        'content',
        // to be published after this date. Basically after this date, it will be visible to readers
        // default to current time. If not defined, will be visible to readers after it's posted
        'publish_after',
        'hide_after', // to be hidden from the readers after this date
        'is_draft'
    ];

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs(): string
    {
        return model_index('Article');
    }

    /**
     * Get indexable data for this model
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $audiences = $this?->audiences->pluck('name')->toArray() ?? [];
        $tags = $this?->tags->pluck('name')->toArray() ?? [];
        $id = Arr::get($array, 'id');
        return [
            'id' => $id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            /**
             * for the timestamp related field, keep the timestamp to only date and not time
             * that way it would be easy to search. So, if the show after value is 2021-06-09 10:10:10
             * then providing 2021-06-09 would not match, since the time different would result in
             * different unix timestamp values
             */
            'hide_after' => $this->hide_after ? strtotime(Carbon::parse($this->hide_after)->format('Y-m-d')) : null,
            'publish_after' => $this->publish_after ? strtotime(Carbon::parse($this->publish_after)->format('Y-m-d')) : null,
            'is_draft' => $this->is_draft,
            'audiences' => implode(', ', $audiences),
            'tags' => implode(', ', $tags)
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query
            ->select(['id', 'title', 'description', 'content', 'publish_after', 'hide_after', 'is_draft']);
    }

    /**
     * @return HasMany
     */
    public function audiences(): HasMany
    {
        return $this->hasMany(ArticleAudience::class);
    }

    protected $casts = [
        'is_draft' => 'bool'
    ];

    /**
     * Key name which would be used when storing an
     * individual article in cache
     * @param $id
     * @return string
     */
    public static function cacheKey($id): string
    {
        return sprintf("article:%s:$id", app()->getLocale());
    }

    /**
     * @return HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(ArticleTag::class);
    }

    /**
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(ArticleCategory::class);
    }

    public function industries(): HasMany
    {
        return $this->hasMany(ArticleIndustry::class);
    }
}
