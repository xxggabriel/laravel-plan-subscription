<?php

namespace Xxggabriel\LaravelPlanSubscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Xxggabriel\LaravelPlanSubscription\Services\Period;

class PlanSubscription extends Model
{

    protected $dates = [
        "trial_ends_at",
        "starts_at",
        "ends_at",
        "cancels_at",
        "canceled_at",
    ];

    protected $fileble = [
        'name',
        'plan_id',
        'trial_ends_at',
        'starts_at',
        'ends_at'
    ];

    public function daysToEndOfSubscription()
    {
        return now()->diffInDays($this->ends_at, false);
    }

    public function cancel($now = false)
    {
        $this->canceled_at = now();
        if($now){
            $this->ends_at = $this->canceled_at;            
        } 

        $this->save();
        return $this;
    }

    public function activate()
    {
        $this->cancels_at = null;
        $this->canceled_at = null;
        $this->save();

        return $this;
    }

    public function active()
    {
        return $this->onTrial() || $this->ended();
    }

    public function inactive()
    {
        return !$this->active();
    }

    public function onTrial()
    {
        return $this->trial_ends_at ? Carbon::now()->gnt($this->trial_ends_at) : false;
    }

    public function ended()
    {
        return $this->ends_at ? Carbon::now()->gnt($this->ends_at) : false;
    }

    public function canceled()
    {
        return $this->canceled_at ? Carbon::now()->gnt($this->canceled_at) : false;
    }

    public function renew()
    {
        if ($this->ended() && $this->canceled()) {
            throw new \Exception('Unable to renew canceled ended subscription.');
        }

        $subscription = $this;

        DB::transaction(function () use ($subscription) {
            $subscription->usage()->delete();
            $subscription->canceled_at = null;
            $subscription->setNewPeriod();
            $subscription->save();
        });

        return $this;
    }

    public function setNewPeriod($invoice_interval = '', $invoice_period = '', $start = '')
    {
        if(empty($invoice_interval)){
            $invoice_interval = $this->plan->invoice_interval;
        }

        if(empty($invoice_period)){
            $invoice_period = $this->plan->invoice_period;
        }

        $period = new Period($invoice_interval, $invoice_period, $start);

        $this->starts_at = $period->getStartDate();
        $this->ends_at = $period->getEndDate();

        return $this;
    }

    public function recordFeatureUsage($featureSlug, $uses = 1, $incremental = true): PlanSubscriptionUsage
    {
        $feature = $this->plan->features()->where('slug', $featureSlug)->first();

        if(!$feature){
            throw new \Exception("Feature not found");
        }

        $usage = $this->usage()->firstOrNew([
            'subscription_id' => $this->getKey(),
            'feature_id' => $feature->id,
        ]);

        if($feature->resettable_period){
            if(is_null($usage->valid_until)){
                $usage->valid_until = $feature->getResetDate($this->created_at);
            } elseif($usage->expired()){
                $usage->valid_until = $feature->getResetDate($usage->valid_until);
                $usage->used = 0;
            }
        }

        $usage->used = ($incremental ? $usage->used + $uses : $uses);
        $usage->save();

        return $usage;
    }

    public function reduceFeatureUsage(string $featureSlug, int $uses = 1): ?PlanSubscriptionUsage
    {
        $feature = $this->getFeatureBySlug($featureSlug);
        $usage = $this->usage()->where('feature_id', $feature->id)->first();
        
        if(!$feature || !$usage){
            return null;
        }

        if($usage->used != "*"){
            $usage->used = max($usage->used - $uses, 0);   
        }

        $usage->save();

        return $usage;
    }

    public function resetUsage(): void
    {
        $usages = $this->usage();

        $usages->each(function($usage){
            $feature = PlanFeature::find($usage->feature_id)
                            ->select('value');
            
            $usage->used = $feature->value;
            $usage->save();
        });
    }

    public function getFeatureBySlug($featureSlug)
    {
        return $this->plan->features()->where("slug", $featureSlug)->first();
    }

    public function canUseFeature($featureSlug): bool
    {
        $feature = $this->getFeatureBySlug($featureSlug);
        $usage = $this->usage()->where('feature_id', $feature->id)->first();
        
        if(!$feature || !$usage){
            return false;
        }

        if($usage->used == "*"){
            return true;
        }

        return $feature->value > intval($usage->used);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('laravel-plan-subscription.models.plan'));
    }

    public function usage(): HasMany
    {
        return $this->hasMany(config('laravel-plan-subscription.models.plan_subscription_usage'), 'subscription_id', 'id');
    }
}
