<?php

namespace App\Services\Shopper;

use App\Repositories\Shopper\ShopperRepository;
use App\Services\BaseService;
use App\Models\Shopper\Shopper;
use App\Models\Store\Location\Location;
use App\Models\Shopper\Status;
use App\Services\Shopper\StatusService;
use App\Repositories\Shopper\StatusRepository;
use Carbon\Carbon;
use Str;

/**
 * Class ShopperService
 * @package App\Services\Shopper
 */
class ShopperService extends BaseService
{
    /**
     * @var ShopperRepository
     */
    protected $shopper;
    protected $statuses;

    /**
     * ShopperService constructor.
     * @param ShopperRepository $shopper
     */
    public function __construct(ShopperRepository $shopper)
    {
        $this->statuses = Status::pluck('id', 'name');
        $this->shopper = $shopper;
        parent::__construct($this->shopper);
    }

    public function checkShopperStatus(Location $location) {
        $status_id = $this->statuses->get('Active');
        $active_shoppers_count = $this->activeQueueCount($location->id, $status_id);
        if ($active_shoppers_count >= $location->shopper_limit) {
            $status_id = $this->statuses->get('Pending');
        }
        return $status_id;
    }

    // Find the list of pending shoppers by location
    public function pendingShoppersByLocation($location_id) : array
    {
        // Pending status id
        $pending_status_id = Status::pending()->value('id');
        
        return $shoppers = $this->shopper->all( [], [
            'status_id' => $pending_status_id, 
            'location_id' => $location_id
        ]);
    }

    // Find the waiting number of the specific shopper
    public function countWaitingNumber(string $location_id, Shopper $shopper) : string
    {
        $shoppers = $this->pendingShoppersByLocation($location_id);

        // Count the waiting number of the 
        $shopper_waiting_number = 0;
        if ($shoppers) {
            $shopper_waiting_number  = collect($shoppers)->search(function($user) use ($shopper) {
                return $user['email'] == $shopper->email;
            });    

            $shopper_waiting_number++;
        }

        return $shopper_waiting_number;
    }

    // Find the waiting number of the specific shopper
    public function findNextShopperOfLocation(Location $location) 
    {
        $pending_status_id = $this->statuses->get('Pending');

        return $this->shopper->show([
            'status_id' => $pending_status_id,
            'location_id' => $location->id
        ]);
    }

    // Find the waiting number of the specific shopper
    public function checkOutShopper(Shopper $shopper) 
    {
        $completed_status_id = $this->statuses->get('Completed');

        if ($shopper->check_out == NULL) {
            $shopper->check_out = Carbon::now();
            $shopper->status_id = $completed_status_id;
            if ( ! $shopper->save()) {
                return null;
            }
        }

        return $shopper;
    }

    // Find the waiting number of the specific shopper
    public function activeteNextShopper(Shopper $shopper)
    {
        $activeQueueCount = $this->activeQueueCount($shopper->location->id, 1);

        $queue =  $this->calculateQueue($shopper->location->shopper_limit, $activeQueueCount);

        if ($queue > 0) {
            $next_shopper = $this->findNextShopperOfLocation($shopper->location);
            if ($next_shopper) {
                $next_shopper_status =  Shopper::findOrFail($next_shopper['id']);
                $next_shopper_status->status_id = $this->statuses->get('Active');
                $next_shopper_status->save();
                return $next_shopper_status;
                // return 
                // $this->shopper->update(
                // return $next_shopper_status =  $this->shopper->update(
                //     $next_shopper['id'],
                //     [
                //         'status_id' => $this->statuses->get('Active')
                //     ]
                // );    
            }
        }
        
        return false;
    }

    private function activeQueueCount(int $location_id, $status_id)
    {
        return $this->shopper->count(
            [
                'location_id' => $location_id,
                'status_id' => $status_id
            ]
        );
    }

    private function calculateQueue($limit, $queue): int
    {
        if( is_int($limit) && is_int($queue) && $queue < $limit ) {
            return $limit - $queue;
        }

        return 0;
    }

    // Find the waiting number of the specific shopper
    public function checkInShopper($request, $location_id, $status_id) 
    {
        $shopper = new Shopper;
        $shopper->uuid = Str::uuid();
        $shopper->location_id = $location_id;
        $shopper->status_id = $status_id;
        $shopper->first_name = $request->first_name;
        $shopper->last_name = $request->last_name;
        $shopper->email = $request->email;
        $shopper->check_in = Carbon::now();
        if ($shopper->save()) {
            return $shopper;
        }
        return false;
    }
}
