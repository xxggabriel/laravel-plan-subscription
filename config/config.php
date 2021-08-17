<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'tables' => [
        'plans' => 'plans',
        'plan_features' => 'plan_features',
        'plan_subscriptions' => 'plan_subscriptions',
        'plan_subscription_usage' => 'plan_subscription_usage',

    ],

    // Subscriptions Models
    'models' => [
        'plan' => Xxggabriel\LaravelPlanSubscription\Models\Plan::class,
        'plan_feature' => Xxggabriel\LaravelPlanSubscription\Models\PlanFeature::class,
        'plan_subscription' => Xxggabriel\LaravelPlanSubscription\Models\PlanSubscription::class,
        'plan_subscription_usage' => Xxggabriel\LaravelPlanSubscription\Models\PlanSubscriptionUsage::class,

    ],
];