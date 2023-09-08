<?php

namespace App\Http\Controllers;

use App\Models\CampaignSummarie;
use App\Models\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $campaignSummary = new CampaignSummarie;
        $dataCampaign = $campaignSummary->getDataDashboard();

        $log = new Log;
        $dataLog = $log->getDataDashboard();
        return view('dashboard', [
            'dataCampaign' => $dataCampaign,
            'dataLog' => $dataLog
        ]);
    }
}
