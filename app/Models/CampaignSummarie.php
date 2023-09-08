<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CampaignSummarie extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'template_id',
        'total_success',
        'total_data',
        'created_at',
    ];


    public function deleteOldData()
    {
        DB::table('campaign_summaries')->truncate();
    }

    public function storeSummary($data = null)
    {
        foreach ($data as $value) {
            self::create([
                'template_id' => $value->id_templates,
                'total_success' => $value->total_success,
                'total_data' => $value->total_data,
            ]);
        }
    }

    public function getDataDashboard()
    {

        $data = DB::table('campaign_summaries')
            ->select('templates.id', 'campaigns.name as campaign_name', 'templates.name as template_name', 'templates.created_at as templates_created_at', 'campaign_summaries.total_success', 'campaign_summaries.total_data')
            ->join('templates', 'templates.id', '=', 'campaign_summaries.template_id')
            ->join('campaigns', 'campaigns.id', '=', 'templates.campaign_id')
            ->whereNull('campaigns.deleted_at')
            ->whereNull('templates.deleted_at')
            ->limit(10)
            ->offset(0)
            ->orderBy('templates_created_at', 'DESC')
            ->get();

        return $data;
    }
}
