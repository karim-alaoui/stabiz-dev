<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EmailTemplate
 * @package App\Models
 * @method static name(string $name)
 */
class EmailTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Mail template names saved as constants
     * So that they are easy to access and there's no mistake of spelling mistake
     * and case sensitive issue. At the same time, you can document each template name
     * on what it does
     */

    /**
     * when subscription starts, use this template
     * @const  string
     */
    public const SUB_START = 'subscription_start';

    /**
     * When subscription is cancelled, use this template
     * @const string
     */
    public const SUB_CANCEL = 'subscription_cancel';

    /**
     * When email otp is sent
     * @const  string
     */
    public const SEND_OTP = 'send_otp';

    /**
     * When an entrepreneur/founder apply to get accepted by a founder/entrepreneur
     * @const string
     */
    public const APPL_ACCEPTED = 'application_accepted';

    /**
     * when the above application rejected
     * @const string
     */
    public const APPL_REJECTED = 'application_rejected';

    /**
     * When a document is rejected
     * @const string
     */
    public const DOC_REJECTED = 'document_rejected';

    /**
     * When an user applies to another user from the recommendation list
     * We notify the staff who made that recommendation
     * @const string
     */
    public const USER_APPLIED_NOTIFY_STAFF = 'user_applied_notify_staff';

    protected $fillable = [
        'name',
        'subject',
        'body',
        // any comment on the template For example, when it's sent, what condition there has to be. This
        // is added by the people who use the app, not us
        'comment',
    ];

    /**
     * Search by name
     * @param Builder $query
     * @param string $name
     * @return Builder
     */
    public function scopeName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'ilike', $name);
    }
}
