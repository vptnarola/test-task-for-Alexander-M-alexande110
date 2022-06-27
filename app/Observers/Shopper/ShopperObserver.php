<?php

namespace App\Observers\Shopper;

use App\Models\Shopper\Shopper;
use App\Models\Shopper\Status;
use App\Observers\BaseObserver;
use App\Services\Shopper\ShopperService;

/**
 * Class ShopperObserver
 * @package App\Observers\Shopper
 */
class ShopperObserver extends BaseObserver
{

    protected $shopper;
    protected $statuses;

    public function __construct(ShopperService $shopper)
    {
        $this->statuses = Status::pluck('id', 'name');
        $this->shopper = $shopper;
    }

    /**
     * Handle the Shopper "created" event.
     *
     * @param  \App\Models\Shopper\Shopper  $shopper
     * @return void
     */
    public function created(Shopper $shopper): void
    {
        $active_status_id = $this->statuses->get('Active');
        if ($shopper->status_id == $active_status_id) {
            // set the Queue job for auto checkout after 2 Hours of checkIn
            dispatch(new \App\Jobs\AutoCheckOut($shopper, $shopper->location))->delay(now()->addHours(2)); 
        }
    }

    /**
     * Handle the Shopper "updated" event.
     *
     * @param  \App\Models\Shopper\Shopper  $shopper
     * @return void
     */
    public function updated(Shopper $shopper): void
    {
        $active_status_id = $this->statuses->get('Active');
        if ($shopper->status_id == $active_status_id) {
            // set the Queue job for auto checkout after 2 Hours of checkIn
            dispatch(new \App\Jobs\AutoCheckOut($shopper, $shopper->location))->delay(now()->addHours(2)); 
        }

        $this->shopper->activeteNextShopper($shopper);
    }

    /**
     * Handle the Shopper "deleted" event.
     *
     * @param  \App\Models\Shopper\Shopper  $shopper
     * @return void
     */
    public function deleted(Shopper $shopper): void
    {
        //
    }

    /**
     * Handle the Shopper "restored" event.
     *
     * @param  \App\Models\Shopper\Shopper  $shopper
     * @return void
     */
    public function restored(Shopper $shopper): void
    {
        //
    }

    /**
     * Handle the Shopper "force deleted" event.
     *
     * @param  \App\Models\Shopper\Shopper  $shopper
     * @return void
     */
    public function forceDeleted(Shopper $shopper): void
    {
        //
    }
}
