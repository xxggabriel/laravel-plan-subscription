<?php

namespace Xxggabriel\LaravelPlanSubscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    public static function bySlug($slug)
    {
        return self::query()->where("slug", $slug)->first();
    }

    public function features()
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function subscriptions() {
        return $this->belongsTo(PlanSubscription::class);
    }
}
