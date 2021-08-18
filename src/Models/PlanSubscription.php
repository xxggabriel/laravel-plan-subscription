<?php

namespace Xxggabriel\LaravelPlanSubscription\Models;

use Illuminate\Database\Eloquent\Model;

class PlanSubscription extends Model
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 3;
    const STATUS_CANCELED = 4;

    protected $dates = [
        "trial_ends_at",
        "starts_at",
        "ends_at",
        "cancels_at",
        "canceled_at",
    ];

    protected $appends = [
        // "active"
    ];

    public function endsAt()
    {
        return now()->diffInDays($this->ends_at, false);
    }

    public function renew($status = self::STATUS_ACTIVE)
    {
        $this->ends_at = now()->addMonths($this->plan->invoice_period);
        $this->status = $status;
        $this->save();
        return $this;
    }

    public function cancel($now = false)
    {
        $this->status = self::STATUS_CANCELED;
        if($now){
            $this->canceled_at = now();
            $this->save();
            return $this->canceled_at;
        } else {
            $this->cancels_at = now();
            $this->save();
            return $this->cancels_at;
        }
    }

    public function activate()
    {
        $subscription = self::query()
            ->where("account_id", $this->account_id)
            ->firstOrFail();

        $subscription->cancels_at = null;
        $subscription->canceled_at = null;
        $subscription->payment_failed_at = null;
        $subscription->status = self::STATUS_ACTIVE;

        $subscription->save();
    }

    public function active($interval = 0)
    {
        if($this->canceled_at != null) {
            return false;
        }

        if(!$this->ends_at){
            return false;
        }

        if(now()->diffInDays($this->ends_at->addDays($interval), false) <= 0){
            return false;
        }

        if($this->cancels_at){
            if(now()->diffInDays($this->ends_at->addDays($this->cancels_at), false) <= 0){
                return false;
            }
        }

        return true;

    }

    public function getActiveAttribute()
    {
        return $this->active();
    }

    public function canceled($interval = 0)
    {
        if($this->status == self::STATUS_CANCELED){
            return true;
        }

        if($this->cancels_at != null){
            return true;
        }

        if($this->cancels_at == null){
            return false;
        }

        if(now()->diffInDays($this->ends_at->addDays($interval), false) <= 0){
            return true;
        }

        return false;
    }
}
