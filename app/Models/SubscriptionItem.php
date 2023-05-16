<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * We use this model as the default SubscriptionItem model for cashier stripe
 * Class SubscriptionItem
 * @package App\Models
 */
class SubscriptionItem extends \Laravel\Cashier\SubscriptionItem
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'stripe_product', 'stripe_product_id');
    }

    /**
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'stripe_price', 'stripe_plan_id');
    }
}
