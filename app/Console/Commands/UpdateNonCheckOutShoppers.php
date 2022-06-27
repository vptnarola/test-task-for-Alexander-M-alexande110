<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shopper\Shopper;
use App\Models\Shopper\Status;
use Carbon\Carbon;
use DB;

class UpdateNonCheckOutShoppers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:auto-checkout-shoppers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron Job will check that any shopper will miss the checkout due to the system issue it will automatically checkout that shopper after 2 Hours';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        $completed_status_id = Status::where('name', 'like', '%Completed%')->value('id');
        // Find that records which are not check out due to system issue or anything else
        $shopper = Shopper::where('check_in', '<=', Carbon::now()->subHour(2)->toDateTimeString())
            ->update([
                'check_out' => DB::raw('DATE_ADD(check_in, INTERVAL 2 HOUR)'),
                'status_id' => $completed_status_id
            ]);
    }
}
