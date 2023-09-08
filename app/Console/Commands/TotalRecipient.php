<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use Illuminate\Console\Command;

class TotalRecipient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:total-recipient';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Total Recipient with Success Status and Active Campaign Period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $totalRecipient = (new Campaign())->totalRecipient();
        if ($totalRecipient) {
            foreach ($totalRecipient as $value) {
                $campaign = Campaign::find($value->id);
                $campaign->total_datas = $value->total_data;
                if (!$campaign->save()) {
                    echo "Failed";
                    return false;
                }
            }
        }
        echo 'Success';
    }
}
