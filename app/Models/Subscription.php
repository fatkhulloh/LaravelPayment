<?php

namespace App\Models;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'active_until',
        'user_id',
        'plan_id'
    ];
    protected $casts = [
        'active_until' => 'datetime',
    ];

    // protected $dates = [
    //     'active_until'
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function isActive()
    {
        return $this->active_until->gt(now());
    }
}
