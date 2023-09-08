<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateRequest;
use App\Library\LoggingBuilder;
use App\Library\Uploader;
use App\Models\Campaign;
use App\Models\Recipient;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public $timestamps = false; // agar data timestamp tidak ikut masuk ke log 

    protected $page = 'Template';

    private $except = ['created_at', 'updated_at', 'deleted_at'];

    public function index()
    {
        $template = Template::get();
        $campaigns = Campaign::get();

        return view('template.index', compact('template', 'campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $email_type = config('config.email_type');
        $font_type = config('config.font_type');
        $text_position = config('config.text_position');
        $campaigns = Campaign::get();
        return view(
            'template.create',
            [
                'campaigns' => $campaigns,
                'email_type' => $email_type,
                'font_type' => $font_type,
                'text_position' => $text_position
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TemplateRequest $request)
    {
        //
        $templates = new Template;
        $templates->name = $request->name;
        $templates->campaign_id = $request->campaign;
        $templates->email_type = $request->email_type;
        $templates->email_subject = $request->email_subject;
        $templates->email_body = $request->email_body;
        if ($request->email_type == 'certificate') {
            $templates->font_type = $request->font_type;
            $templates->font_color = $request->font_color;
            $templates->text_position = $request->text_position;
            $templates->certificate_name = $request->certificate_name;
            $templates->certificate_author = $request->certificate_author;
            $templates->pdf_title = $request->pdf_title;
            if ($request->file('certificate_image')) {
                $certificate_image = Uploader::make()->put($request->certificate_image, 'cdn');
                $templates->certificate_image = $certificate_image;
            }
        }
        if ($request->file('header_image')) {
            $header_image = Uploader::make()->put($request->header_image, 'cdn');
            $templates->header_image = $header_image;
        }
        $templates->start_date = $request->start_date;
        $templates->end_date = $request->end_date;

        $templates->save();

        $activity = Arr::except($templates->getAttributes(), $this->except);

        $loggingBuilder = new LoggingBuilder();
        $loggingBuilder
            ->setPage($this->page)
            ->setActivity($activity)
            ->create();

        return redirect('/template')->with('success', 'Template created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $id = (int) $id;
        $data = (new Template)->findById($id);
        $email_type = config('config.email_type');
        $font_type = config('config.font_type');
        $text_position = config('config.text_position');
        $campaigns = Campaign::get();
        return view('template.edit', [
            'email_type' => $email_type,
            'data' => $data,
            'campaigns' => $campaigns,
            'font_type' => $font_type,
            'text_position' => $text_position
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TemplateRequest $request, Template $template)
    {
        //
        $templates = Template::findOrFail($template->id);
        $templates->name = $request->name;
        $templates->email_subject = $request->email_subject;
        $templates->start_date = $request->start_date;
        $templates->end_date = $request->end_date;
        $templates->email_body = $request->email_body;
        if ($request->email_type == 'certificate') {
            $templates->font_type = $request->font_type;
            $templates->font_color = $request->font_color;
            $templates->text_position = $request->text_position;
            $templates->certificate_name = $request->certificate_name;
            $templates->certificate_author = $request->certificate_author;
            $templates->pdf_title = $request->pdf_title;
            if ($request->file('certificate_image')) {
                $certificate_image = Uploader::make()->put($request->certificate_image, 'cdn');
                $templates->certificate_image = $certificate_image;
            }
        }

        if ($request->file('header_image')) {
            $header_image = Uploader::make()->put($request->header_image, 'cdn');
            $templates->header_image = $header_image;
        }
        if ($templates->isDirty()) {
            $templates->save();

            $campaign = Campaign::findOrFail($templates->campaign_id);

            $activity = [
                'Campaign ID' => $templates->campaign_id,
                'Campaign Name' => $campaign->name
            ];

            $loggingBuilder = new LoggingBuilder();
            $loggingBuilder
                ->setPage($this->page)
                ->setDataAfter($templates)
                ->setDataBefore($template)
                ->setActivity($activity)
                ->edit()
                ->build();

            return redirect('/template')->with('success', 'Template updated successfully');
        }

        return redirect('/template');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $templates = Template::findOrFail($id);

        $countProcess = Recipient::processSend($id)->count();

        if ($countProcess == 0) {
            $templates->delete();
            $campaign = Campaign::findOrFail($templates->campaign_id);
            $dataDelete = [
                'Campaign ID' => $templates->campaign_id,
                'Campaign Name' => $campaign->name,
                "Delete Template ID" => $id,
                'Template name' => $templates->name
            ];
            $loggingBuilder = new LoggingBuilder();
            $loggingBuilder
                ->setPage($this->page)
                ->setActivity($dataDelete)
                ->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function datatable(Request $request)
    {
        if (request()->ajax()) {
            $orderColumn = $request['order'][0]['column'] ?? null;
            $orderCol = $request['columns'][(int)$orderColumn]['name'] ?? null;
            $order = $request['order'][0]['dir'] ?? null;
            $search = $request['search']['value'] ?? null;
            $limit = request('length');
            $start = request('start');
            $filter = request('campaign');
            $templates = new Template();
            $recordsTotal = $templates->countSearch(null, null);
            $data = $templates->datatables($limit, $start, $search, $orderCol, $order, $filter);
            $recordsFiltered = $templates->countSearch($search, $filter);

            return response()->json([
                'draw' => intval(request('draw')),
                'recordsTotal' => intval($recordsTotal),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'order' => $order,
            ]);
        }
    }

    public function image()
    {
        $data = Template::select('header_image', 'certificate_image')->find(request('id'));
        return response()->json([
            'data' => $data,
            'success' => true
        ]);
    }
}
