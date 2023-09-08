<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\CampaignSummarie;

class SummaryCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:summary-campaigns'; // menambahkan command artisan untuk menjalankan bagian handle() yang dibawha

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaign = new Campaign;
        $data = $campaign->summary(); // menjala query dari model campaign di function summary
        $campaign_summaries = new CampaignSummarie;
        $campaign_summaries->deleteOldData(); // menjalankan query dari model campaign di function summary
        $campaign_summaries->storeSummary($data); // melakukan insert data ke database 
    }
}
