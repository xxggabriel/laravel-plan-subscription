<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
