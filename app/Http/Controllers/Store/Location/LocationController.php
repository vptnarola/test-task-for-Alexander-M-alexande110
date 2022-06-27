<?php

namespace App\Http\Controllers\Store\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Location\LocationCreateRequest;
use App\Http\Requests\Store\Location\LocationQueueRequest;
use App\Http\Requests\Store\Location\LocationStoreRequest;
use App\Models\Store\Location\Location;
use App\Services\Store\Location\LocationService;
use App\Services\Shopper\ShopperService;
use Illuminate\Http\Request;
use Str;
use Auth;
use App\Models\Shopper\Status;
use App\Models\Shopper\Shopper;
use Carbon\Carbon;


/**
 * Class LocationController
 * @package App\Http\Controllers\Store
 */
class LocationController extends Controller
{
    /**
     * @var LocationService
     */
    protected $location;
    protected $shopper;

    /**
     * LocationController constructor.
     * @param LocationService $location
     */
    public function __construct(LocationService $location, ShopperService $shopper)
    {
        $this->location = $location;
        $this->shopper = $shopper;
    }

    /**
     * @param Location $location
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function public(Location $location)
    {
        if (Auth::guard('shoppers')->check()) {
            return redirect()->route('shopper.dashboard', ['shopper' => Auth::guard('shoppers')->user()->uuid]);
        }
        return view('stores.location.public')->with('location', $location);
    }

    /**
     * @param LocationCreateRequest $request
     * @param string $storeUuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(LocationCreateRequest $request, string $storeUuid)
    {
        return view('stores.location.create')
            ->with('store', $storeUuid);
    }

    /**
     * @param LocationStoreRequest $request
     * @param string $storeUuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LocationStoreRequest $request, string $storeUuid): \Illuminate\Http\RedirectResponse
    {
        $this->location->create([
            'location_name' => $request->location_name,
            'shopper_limit' => $request->shopper_limit,
            'store_id' => $storeUuid
        ]);

        return redirect()->route('store.store', ['store' => $storeUuid]);
    }

    /**
     * @param LocationQueueRequest $request
     * @param string $storeUuid
     * @param string $locationUuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function queue(LocationQueueRequest $request, string $storeUuid, string $locationUuid)
    {
        $location = $this->location->show(
            [
                'uuid' => $locationUuid
            ],
            [
                'Shoppers',
                'Shoppers.Status'
            ]
        );

        $shoppers = null;

        if( isset($location['shoppers']) && count($location['shoppers']) >= 1 ){
            $shoppers = $this->location->getShoppers($location['shoppers']);
        }

        return view('stores.location.queue')
            ->with('location', $location)
            ->with('shoppers', $shoppers);
    }


    /**
     * @param Location $location
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function checkIn(Request $request, Location $location)
    {
        // Find the status for the new shopper
        $status_id = $this->shopper->checkShopperStatus($location);

        // Check already checkin then redirect to the dasbhoard
        $check_shopper_exist = $this->shopper->show([
            'email' => $request->email,
            'check_out' => NULL
        ]);
        if ($check_shopper_exist) {
            Auth::guard('shoppers')->login($check_shopper_exist);
            return redirect()->route('shopper.dashboard', ['shopper' => Auth::guard('shoppers')->user()->uuid])->withSuccess('You have checked in successfully.');
        }

        // Store the checkIn details
        $shopper = $this->shopper->checkInShopper($request, $location->id, $status_id);
        if ($shopper) {
            Auth::guard('shoppers')->login($shopper);

            return redirect()->route('shopper.dashboard', ['shopper' => $shopper->uuid])->withSuccess('You have checked in successfully.');
        }
        return redirect()->back()->withErrors('Something went wrong, try after some times!');    
    }
}
