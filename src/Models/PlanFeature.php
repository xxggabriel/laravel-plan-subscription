<?php

namespace Xxggabriel\LaravelPlanSubscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Xxggabriel\LaravelPlanSubscription\Services\Period;

class PlanFeature extends Model
{
    use SoftDeletes;

    protected $fillable  = [
        "plan_id",
        "name",
        "slug",
        "value",
        "sort_order",
        "resettable_period",
        "resettable_interval"
    ];

    public static function bySlug($slug)
    {
        return self::query()->where('slug', $slug)
            ->first();
    }

    public function getResetDate(Carbon $date)
    {
        $period = new Period($this->resettable_interval, $this->resettable_period, $date ?? now());
        return $period->getEndDate();
    }
}
