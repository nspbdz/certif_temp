<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'page',
        'username_dc',
        'activity',
        'action',
    ];

    const UPDATED_AT = null;

    public function getDataDashboard()
    {
        $query = DB::table('logs')
            ->select(
                'logs.id',
                DB::raw("TO_CHAR(
                    logs.created_at,
                    'DD/MM/YYYY HH24:MI:SS'
                ) AS created_at"),
                'logs.username_dc as username',
                'logs.page as page',
                'logs.activity as activity',
                'logs.action as action',
            );
        $query->orderBy('logs.id', 'desc');

        $query->limit(10);
        $results = $query->get();

        $data = $results->map(function ($item) {
            $item->activity = json_decode($item->activity, true);
            return $item;
        });
        return $data;
    }

    public function countLog($username = null, $start_date = null, $end_date = null, $search = null,)
    {
        $query = DB::table('logs');
        if ($search != null) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('lower(logs.page)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(logs.action)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(logs.username_dc)'), 'LIKE', '%' . $search . '%');
            });
        }
        if ($username != 'all' && $username != null) {
            $query->where('username_dc', $username);
        }

        if ($start_date != 'all' && $start_date != null && $end_date != 'all' && $end_date) {
            $query->whereBetween(DB::raw("DATE(created_at)"), array($start_date, $end_date));
        }


        return $query->count();
    }



    public function datatables($username = null, $start_date = null, $end_date = null, $limit = null, $start = null, $search = null, $orderCol = null, $orderDir = null)
    {
        $query = DB::table('logs')
            ->select(
                'logs.id',
                DB::raw("TO_CHAR(
                    created_at,
                    'DD/MM/YYYY HH24:MI:SS'
                ) AS created_at"),
                'logs.username_dc as username_dc',
                'logs.page as page',
                'logs.activity as activity',
                'logs.action as action',
            );
        $query->orderBy('logs.id', 'desc');

        if ($orderCol != null && $orderDir !=  null) {
            $query->orderBy('logs.' . $orderCol, $orderDir);
        } else {
            $query->orderBy('logs.id', 'desc');
        }


        if ($username != 'all' && $username != null) {
            $query->where('username_dc', $username);
        }

        if ($start_date != 'all' && $start_date != null && $end_date != 'all' && $end_date) {
            $query->whereBetween(DB::raw("DATE(created_at)"), array($start_date, $end_date));
        }

        if ($search != null) {
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('lower(logs.activity)'), 'LIKE', '%' . strtolower($search) . '%');
                $query->orWhere(DB::raw('lower(logs.page)'), 'LIKE', '%' . strtolower($search) . '%');
                $query->orWhere(DB::raw('lower(logs.action)'), 'LIKE', '%' . strtolower($search) . '%');

                if (str_contains($search, '/')) {
                    $searchParts = preg_split('/\/|\s+/', $search);
                    $query->orWhere(function ($query) use ($searchParts) {
                        $query->where(DB::raw('lower(logs.page)'), 'LIKE', '%' . strtolower($searchParts[0]) . '%');
                        $query->where(DB::raw('lower(logs.action)'), 'LIKE', '%' . strtolower($searchParts[1]) . '%');
                    });
                } else if (str_contains($search, ' ')) {
                    $searchParts = explode(' ', $search);
                    $query->orWhere(function ($query) use ($searchParts) {
                        $query->where(DB::raw('lower(logs.action)'), 'LIKE', '%' . strtolower($searchParts[0]) . '%');
                        $query->where(DB::raw('lower(logs.page)'), 'LIKE', '%' . strtolower($searchParts[1]) . '%');
                    });
                }
            });
        }

        $query->offset($start)->limit($limit);
        $results = $query->get();

        $data = $results->map(function ($item) {
            $item->activity = json_decode($item->activity, true);
            return $item;
        });
        return $data;
    }

    public function getDataExport($username = null, $start_date = null, $end_date = null,)

    {
        $query = DB::table('logs')
            ->select(
                'logs.id',
                DB::raw("TO_CHAR(
                created_at,
                'DD/MM/YYYY HH12:MI:SS'
            ) AS created_at"),
                'logs.username_dc as username',
                'logs.page as page',
                'logs.activity as activity',
                'logs.action as action',
            );
        if ($username != 'all' && $username != null) {
            $query->where('logs.username_dc', $username);
        }

        if ($start_date != 'all' && $start_date != null && $end_date != 'all' && $end_date) {
            $query->whereBetween(DB::raw("DATE(created_at)"), array($start_date, $end_date));
        }

        $results = $query->get();
        $data = $results->map(function ($item) {
            $item->activity = json_decode($item->activity, true);
            return $item;
        });


        return $data;
    }
}
