<?php

namespace Xxggabriel\LaravelPlanSubscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanSubscriptionUsage extends Model
{
    use SoftDeletes;
    
    protected $table="plan_subscription_usages";
    
    protected $fillable = [
        "subscription_id",
        "feature_id"
    ];

    protected $dates = [
        "valid_until"
    ];

    public function byFeatureSlug($slug){
        
        $feature = $this->subscription->plan->features()->where('slug', $slug)->first();
        return $this->where('feature_id', $feature->id);
    }

    public function subscription()
    {
        return $this->belongsTo(config('laravel-plan-subscription.models.plan_subscription'));
    }

    public function expired()
    {
        return $this->valid_until ? Carbon::now()->gte($this->valid_until) : false;
    }
}
