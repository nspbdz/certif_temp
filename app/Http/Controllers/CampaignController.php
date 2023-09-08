<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Http\Requests\CampaignRequest;
use App\Library\LoggingBuilder;

class CampaignController extends Controller
{


    public function index()
    {
        return view('campaign.index');
    }

    public function create()
    {

        $data_email = config('config.data_email');

        return view('campaign.create', ['data_email' => $data_email]);
    }

    public function datatable(Request $request)
    {

        if (request()->ajax()) {

            $orderColumn = $request['order'][0]['column'] ?? null;
            $orderCol = $request['columns'][(int)$orderColumn]['name'] ?? null;
            $orderDir = $request['order'][0]['dir'] ?? null;
            $search = $request['search']['value'] ?? null;
            $limit = request('length');
            $start = request('start');
            $campaigns = new Campaign();
            $recordsFiltered = $campaigns->countCampaign($search);
            $data = $campaigns->datatables($limit, $start, $search, $orderCol, $orderDir);
            $recordsTotal = $campaigns->countCampaign();

            return response()->json([
                'draw' => intval(request('draw')),
                'recordsTotal' => intval($recordsTotal),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }
    }


    public function store(CampaignRequest $request)
    {
        $campaigns = new Campaign;
        $campaigns->name = $request->name;
        $campaigns->sender_email = $request->sender_email;
        $campaigns->sender_name = $request->sender_name;
        $campaigns->total_data = 0;
        $campaigns->save();
        $loggingBuilder = new LoggingBuilder();
        $loggingBuilder
            ->setPage("Campaign")
            ->setActivity($request->only('name', 'sender_email', 'sender_name'))
            ->create();

        return redirect('/campaign')->with('success', 'Campaign created successfully');
    }

    public function edit($id)
    {
        $id = (int) $id;
        $data = Campaign::findOrFail($id);
        $data_email = config('config.data_email');

        return view('campaign.edit', ['data_email' => $data_email, 'data' => $data]);
    }

    public function update(CampaignRequest $request, Campaign $campaign)
    {

        $campaigns = Campaign::findOrFail($campaign->id);
        $campaigns->name  = $request->name;
        $campaigns->sender_email = $request->sender_email;
        $campaigns->sender_name = $request->sender_name;

        if ($campaigns->isDirty()) { //isDirty() untuk mengetahui apakah query update dijalankan atau tidak.
            $campaigns->save();
            $loggingBuilder = new LoggingBuilder();
            $loggingBuilder
                ->setPage("Campaign")
                ->setDataAfter($campaigns)
                ->setDataBefore($campaign)
                ->edit()
                ->build();
            return redirect('/campaign')->with('success', 'Campaign updated successfully');
        } else {
            return redirect('/campaign');
        }
    }

    public function destroy($id)
    {
        $campaigns = new Campaign;
        $campaigns = $campaigns->queryFind($id);

        $campaigns = $campaigns->first();
        if (empty($campaigns)) {
            $campaign = Campaign::find($id);
            $campaign->deleted_at = dateTimeNow();
            $campaign->save();
            $dataDelete = ["Delete Campaign ID" => $id, 'Campaign name' => $campaign->name];
            $loggingBuilder = new LoggingBuilder();
            $loggingBuilder
                ->setPage("Campaign")
                ->setActivity($dataDelete)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Campaign Deleted Successfully']);
        }
    }



    public function data()
    {
        $data = Campaign::select('sender_name', 'sender_email')->find(request('id'));
        return response()->json([
            'data' => $data,
            'success' => true
        ]);
    }
}
