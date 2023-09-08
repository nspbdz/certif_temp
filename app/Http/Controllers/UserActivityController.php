<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserActivityExport;
use App\Models\Users;

class UserActivityController extends Controller
{


    public function index()
    {
        $data_username = Users::get();
        return view('user_activity.index', ['data_username' => $data_username]);
    }


    public function datatable(Request $request)
    {

        if (request()->ajax()) {
            $username = $request->get('username') ?? null;
            $end_date = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->format('Y/m/d') : null;
            $start_date = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->format('Y/m/d') : null;

            $orderColumn = $request['order'][0]['column'] ?? null;
            $orderCol = $request['columns'][(int)$orderColumn]['name'] ?? null;
            $orderDir = $request['order'][0]['dir'] ?? null;

            $search = $request['search']['value'] ?? null;
            $limit = request('length');
            $start = request('start');
            $log = new Log();
            $recordsFiltered = $log->countLog($username, $start_date, $end_date, $search);
            $data = $log->datatables($username, $start_date, $end_date, $limit, $start, $search, $orderCol, $orderDir);
            $recordsTotal = $log->countLog();



            return response()->json([
                'draw' => intval(request('draw')),
                'recordsTotal' => intval($recordsTotal),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'order' => $orderDir,
            ]);
        }
    }

    public function export_excel(Request $request)
    {
        $username = $request->input('username') ?? null;
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;
        $log = new Log();
        $data = $log->getDataExport($username, $start_date, $end_date);
        return Excel::download(new UserActivityExport($data), 'User Activity.xlsx');
    }
}
