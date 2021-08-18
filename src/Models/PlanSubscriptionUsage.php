<?php

namespace Xxggabriel\LaravelPlanSubscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanSubscriptionUsage extends Model
{
    use SoftDeletes;
    
    protected $table="plan_subscription_usages";
    //

    public function byFeatureSlug($slug){
        return $this->where('slug', $slug);
    }

    public function subscription()
    {
        return $this->belongsTo(config('laravel-plan-subscription.models.plan_subscription'));
    }
}
