<?php

namespace App\Models\Shopper;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Status
 * @package App\Models\Shopper
 */
class Status extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shoppers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Shopper::class);
    }

    public function scopeActive($query)
    {
        return $query->where('name', 'Active');
    }

    public function scopePending($query)
    {
        return $query->where('name', 'Pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('name', 'Completed');
    }
}
