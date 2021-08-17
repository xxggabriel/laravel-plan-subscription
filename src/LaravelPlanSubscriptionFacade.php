<?php

namespace Xxggabriel\LaravelPlanSubscription;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xxggabriel\LaravelPlanSubscription\Skeleton\SkeletonClass
 */
class LaravelPlanSubscriptionFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-plan-subscription';
    }
}
