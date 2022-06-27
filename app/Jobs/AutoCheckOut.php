<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class AutoCheckOut implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shopper;
    protected $location;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shopper, $location)
    {
        $this->shopper = $shopper;
        $this->location = $location;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $checkout_status = app()->call(
            [app(\App\Http\Controllers\Shopper\ShopperQueueController::class), 'check_out'], 
            [
                'shopper' => $this->shopper,
                'location' => $this->location
            ] 
        );

        if ($checkout_status) {
            Log::info('Shopper '. $this->shopper->email .' auto checkout successfully.');
        }
        else {
            Log::info('Shopper '. $this->shopper->email .' auto checkout failed.');    
        }
    }
}
