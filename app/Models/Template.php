<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    public function countSearch($search = null, $filter = null)
    {
        $query = DB::table('templates')
            ->leftJoin('campaigns', 'campaigns.id', '=', "templates.campaign_id")
            ->whereNull('campaigns.deleted_at')
            ->whereNull('templates.deleted_at');
        if ($search != null) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('lower(templates.name)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(templates.email_type)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(campaigns.sender_name)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(campaigns.sender_email)'), 'LIKE', '%' . $search . '%');
            });
        }
        if ($filter != '') {
            $query->where('templates.campaign_id', $filter);
        }
        return $query->count();
    }

    public function datatables($limit = null, $start = null, $search = null, $orderCol = null, $orderDir = null, $filter = '')
    {
        $query = DB::table($this->getTable())
            ->select(
                'templates.id',
                'templates.name',
                'email_type',
                'campaigns.sender_email',
                'campaigns.sender_name',
                'templates.created_at',
                'templates.header_image',
                'templates.certificate_image'
            )
            ->leftJoin('campaigns', 'campaigns.id', '=', "templates.campaign_id")
            ->whereNull('campaigns.deleted_at')
            ->whereNull('templates.deleted_at');
        if ($filter != '') {
            $query->where('templates.campaign_id', $filter);
        }
        if ($orderCol != null && $orderDir !=  null) {
            $query->orderBy($orderCol, $orderDir);
        }
        if ($search != null) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('lower(templates.name)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(templates.email_type)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(campaigns.sender_name)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(campaigns.sender_email)'), 'LIKE', '%' . $search . '%');
            });
        }
        $query->offset($start)->limit($limit);
        return  $query->get();
    }

    public function findById($id)
    {
        $query = DB::table($this->getTable())
            ->select(
                'templates.id',
                'templates.campaign_id',
                'templates.name',
                'email_type',
                'templates.email_subject',
                'templates.email_body',
                DB::raw('campaigns.name as campaign_name'),
                'campaigns.sender_email',
                'campaigns.sender_name',
                'templates.header_image',
                'templates.font_type',
                'templates.font_color',
                'templates.text_position',
                'templates.certificate_name',
                'templates.certificate_author',
                'templates.pdf_title',
                'templates.certificate_image',
                'templates.start_date',
                'templates.end_date',
                'templates.created_at',
                DB::raw('CASE WHEN templates.start_date <= NOW() AND templates.end_date >= NOW() THEN 1 ELSE 0 END as active')
            )
            ->leftJoin('campaigns', 'campaigns.id', '=', "templates.campaign_id")
            ->whereNull('templates.deleted_at')
            ->where('templates.id', $id);

        $data = $query->first();
        if (empty($data)) {
            return abort(404);
        }
        return $data;
    }

}
