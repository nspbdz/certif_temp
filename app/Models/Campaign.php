<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'sender_email',
        'sender_name',
        'total_data',
    ];



    public function template()
    {
        return $this->belongsTo(Template::class, 'id', 'campaign_id');
    }

    public function summary()
    {
        $data = DB::table('campaigns')
            ->select('recipients.template_id as id_templates',  DB::raw("SUM(CASE WHEN recipients.status = 'success' THEN 1 ELSE 0 END) AS total_success "), DB::RAW("count('recipients.status') as total_data "))
            ->join('templates', 'templates.campaign_id', '=', 'campaigns.id')
            ->join('recipients', 'recipients.template_id', '=', 'templates.id')
            ->whereNull('campaigns.deleted_at')
            ->whereNull('templates.deleted_at')
            ->whereNull('recipients.deleted_at')
            ->groupBy('recipients.template_id')
            ->limit(10)
            ->offset(0)
            ->get();

        return $data;
    }




    public function queryFind($id = null)
    {

        $query = DB::table('campaigns')
            ->select('campaigns.id as campaigns_id')
            ->join('templates', 'templates.campaign_id', '=', 'campaigns.id')
            ->join('recipients', 'recipients.template_id', '=', 'templates.id')
            ->whereNull('campaigns.deleted_at')
            ->whereNull('templates.deleted_at')
            ->whereNull('recipients.deleted_at')
            ->where(function ($query) {
                $query->where('recipients.status', config('config.status.success'))
                    ->orWhere('recipients.status', config('config.status.sending'));
            })
            ->where('campaigns.id', $id);

        return $query;
    }

    public function countCampaign($search = null)
    {
        $query = DB::table('campaigns')->whereNull('campaigns.deleted_at');
        if ($search != null) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('lower(campaigns.name)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(campaigns.sender_email)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(campaigns.sender_name)'), 'LIKE', '%' . $search . '%');
            });
        }
        return $query->count();
    }

    public function datatables($limit = null, $start = null, $search = null, $orderCol = null, $orderDir = null)
    {
        $query = DB::table('campaigns')
            ->select(
                'campaigns.id as campaigns_id',
                'campaigns.name as name',
                'campaigns.sender_email as sender_email',
                'campaigns.sender_name as sender_name',
                DB::raw("TO_CHAR(
                    created_at,
                    'DD/MM/YYYY HH24:MI:SS'
                ) AS created_at"),
                'campaigns.total_data',
            )
            ->whereNull('campaigns.deleted_at');
        if ($orderCol != null && $orderDir !=  null) {
            $query->orderBy('campaigns.' . $orderCol, $orderDir);
        } else {
            $query->orderBy('campaigns.created_at', 'desc');
        }


        if ($search != null) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('lower(campaigns.name)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(campaigns.sender_email)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(campaigns.sender_name)'), 'LIKE', '%' . $search . '%');
            });
        }
        $query->offset($start)->limit($limit);
        return  $query->get();
    }

    public function totalRecipient()
    {
        $data = DB::table('campaigns')
            ->select('campaigns.id',  DB::raw("SUM(CASE WHEN recipients.status = 'success' THEN 1 ELSE 0 END) AS total_data "))
            ->join('templates', 'templates.campaign_id', '=', 'campaigns.id')
            ->join('recipients', 'recipients.template_id', '=', 'templates.id')
            ->whereNull('campaigns.deleted_at')
            ->whereNull('templates.deleted_at')
            ->whereNull('recipients.deleted_at')
            ->whereRaw("templates.start_date <= NOW() AND templates.end_date >= NOW()")
            ->groupBy('campaigns.id')
            ->get();
        return $data;
    }

    public function storeSummary($data = null)
    {

        foreach ($data as $value) {
            self::create([
                'template_id' => $value->id_templates,
                'total_data' => $value->total_data,
            ]);
        }
    }
}
