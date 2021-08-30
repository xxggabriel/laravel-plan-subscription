<?php 

namespace Xxggabriel\LaravelPlanSubscription\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Xxggabriel\LaravelPlanSubscription\Models\Plan;
use Xxggabriel\LaravelPlanSubscription\Models\PlanSubscription;
use Xxggabriel\LaravelPlanSubscription\Services\Period;

trait HasSubscription {

    public function subscriptions(): MorphMany
    {
        return $this->morphMany(config('models.plan_subscription'), 'subscriber');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function newSubscription($subscriptionName, Plan $plan, $startDate): PlanSubscription
    {
        $trial = new Period($this->plan->trial_interval, $this->plan->trial_period, $startDate ?? now());
        $period = new Period($this->plan->period_interval, $this->plan->period_period, $startDate ?? now());

        return $this->subscriptions()->create([
            'name' => $subscriptionName,
            'plan_id' => $plan->getKey(),
            'trial_ends_at' => $trial->getStartDate(),
            'starts_at' => $period->getStartDate(),
            'ends_at' => $period->getEndDate(),
        ]);
    }
}