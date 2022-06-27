<?php

namespace App\Http\Controllers\Shopper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store\Location\Location;
use App\Services\Store\Location\LocationService;
use App\Models\Shopper\Status;
use App\Services\Shopper\StatusService;
use App\Services\Shopper\ShopperService;
use Carbon\Carbon;
use Auth;
use DB;
use App\Models\Shopper\Shopper;

class ShopperQueueController extends Controller
{

    /**
     * @var ShopperService
     */
    protected $shopper;

    /**
     * ShopperQueueController constructor.
     * @param ShopperService $location
     */
    public function __construct(ShopperService $shopper)
    {
        $this->shopper = $shopper;
    }

    /**
     * @param Location $location
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function dashboard(Shopper $shopper)
    {
        // Find the waiting number
        $shopper_waiting_number = $this->shopper->countWaitingNumber($shopper->location_id, $shopper);
        
        return view('shopper.dashboard', compact('shopper_waiting_number', 'shopper'));
    }

    /**
     * @param Location $location
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function check_out(Shopper $shopper, Location $location)
    {   
        $checkout = $this->shopper->checkOutShopper($shopper);
        if ($checkout) {
            Auth::guard('shoppers')->logout();
            return redirect()->route('public.location', ['location' => $location->uuid])->withSuccess('Thank you for visiting our store.');
        }
        return redirect()->back()->withError('Something went wrong.try after some time.');
    }
}
