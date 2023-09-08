<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Library\LoggingBuilder;
use App\Models\Recipient;
use App\Models\Template;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $page = 'Recipient';

    public function index($templateId)
    {
        //
        $template = (new Template())->findById($templateId);
        $status = config('config.status');
        $recipients = $this->get_status_total($templateId);

        return view('recipient.index', ['template' => $template, 'status' => $status, 'recipients' => $recipients]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function datatable(Request $request)
    {
        if (request()->ajax()) {
            $templateId = $request->get('templateId') ?? null;
            $orderColumn = $request['order'][0]['column'] ?? null;
            $orderCol = $request['columns'][(int)$orderColumn]['name'] ?? null;
            $order = $request['order'][0]['dir'] ?? null;
            $search = strtolower($request['search']['value']) ?? null;
            $limit = request('length');
            $start = request('start');
            $filter = request('status');
            $emailType = $request->get('emailType') ?? null;
            $recipient = new Recipient();
            $recordsTotal = $recipient->countSearch($templateId, null, null, $emailType);
            $data = $recipient->datatables($templateId, $limit, $start, $search, $orderCol, $order, $filter, $emailType);
            $recordsFiltered = $recipient->countSearch($templateId, $search, $filter, $emailType);

            return response()->json([
                'draw' => intval(request('draw')),
                'recordsTotal' => intval($recordsTotal),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'order' => $order,
            ]);
        }
    }

    public function import(ImportRequest $request, int $id)
    {
        $result = false;
        $template = (new Template())->findById($id);
        $the_file = $request->file('import');
        if ($template->email_type == 'certificate') {
            $data_formatted = $this->getFormattedDataImport($the_file, $id);
        } else {
            $data_formatted = $this->getFormattedDataImportTicket($the_file, $id);
        }
        $dataNeed = array_map('array_filter', $data_formatted['data']);
        $dataNeed = array_filter($dataNeed);

        if ($data_formatted['err'] > 0) {
            return back()->with('error', ['Format file yang diupload tidak sesuai, pastikan kolom sesuai sample!']);
        }

        if ($template->email_type == 'certificate') {
            $result = $this->queryImport($dataNeed, $id);
        } else if ($template->email_type == 'ticket') {
            $result = $this->queryImportTicket($dataNeed, $id);
        }

        if ($result) {
            $data = [
                'Campaign ID' => $template->campaign_id,
                'Campaign Name' => $template->campaign_name,
                'Template Email ID' => $template->id,
                'Template Email Name' => $template->name,
                'Added Recipients' => count($dataNeed)
            ];

            $loggingBuilder = new LoggingBuilder();
            $loggingBuilder
                ->setPage($this->page)
                ->setActivity($data)
                ->create();
        }
        return back();
    }

    public function download()
    {
        $type = request('type');
        $tmp_file = public_path('download_' . $type . '.xlsx');
        return response()->download($tmp_file, 'import.xlsx', ['Content-Type: application/octet-stream']);
    }

    public function send()
    {
        $templateId = request('id');
        $resultUpdate = (new Recipient())->updateSend($templateId)->update(['status' => 'sending']);

        if ($resultUpdate) {
            $template = (new Template())->findById($templateId);

            $data = [
                'Campaign ID' => $template->campaign_id,
                'Campaign Name' => $template->campaign_name,
                'Template Email ID' => $template->id,
                'Template Email Name' => $template->name,
            ];

            $loggingBuilder = new LoggingBuilder();
            $loggingBuilder
                ->setPage($this->page)
                ->setActivity($data)
                ->setExtra('Sending Email')
                ->send();


            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function retry()
    {
        $id = request('id');
        $templateId = request('template_id');
        $update = Recipient::where('id', $id)->update(['status' => 'sending']);

        if ($update) {
            $recipient = Recipient::find($id);
            $template = (new Template())->findById($templateId);

            $data = [
                'Campaign ID' => $template->campaign_id,
                'Campaign Name' => $template->campaign_name,
                'Template Email ID' => $template->id,
                'Template Email Name' => $template->name,
            ];

            $loggingBuilder = new LoggingBuilder();
            $loggingBuilder
                ->setPage($this->page)
                ->setActivity($data)
                ->setExtra('Retry send email to ' . $recipient->email)
                ->retry();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function delete()
    {
        $templateId = request('id');
        $recipient = Recipient::deletePending($templateId)->delete();
        $template = (new Template())->findById($templateId);
        $data = [
            'Campaign ID' => $template->campaign_id,
            'Campaign Name' => $template->campaign_name,
            'Template Email ID' => $template->id,
            'Template Name' => $template->name,
            'Pending Data Deleted' => $recipient
        ];
        $loggingBuilder = new LoggingBuilder();
        $loggingBuilder
            ->setPage($this->page)
            ->setActivity($data)
            ->delete();

        return response()->json(['success' => true]);
    }

    private function getFormattedDataImport($file, $idTemplate)
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $row_limit = $sheet->getHighestDataRow();
            $dataNeed = [];
            $errFormat = 0;

            for ($row = 2; $row <= $row_limit; $row++) {
                $email = $sheet->getCell('A' . $row)->getValue();
                $name = $sheet->getCell('B' . $row)->getValue();
                $template_id = ($email || $name) ? $idTemplate : '';

                $dataNeed[] = [
                    'email' => $email,
                    'name' => $name,
                    'template_id' => $template_id
                ];

                if ($sheet->getCell('C' . $row)->getValue()) {
                    $errFormat++;
                }
            }
        } catch (Exception $e) {
            Log::channel('common')->info($e->getMessage());
        }

        return [
            'data' => $dataNeed,
            'err'  => $errFormat
        ];
    }



    private function getFormattedDataImportTicket($file, $idTemplate)
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $row_limit = $sheet->getHighestDataRow();
            $dataNeed = [];
            $errFormat = 0;

            for ($row = 2; $row <= $row_limit; $row++) {
                $email = $sheet->getCell('A' . $row)->getValue();
                $name = $sheet->getCell('B' . $row)->getValue();
                $ticket_code = $sheet->getCell('C' . $row)->getValue();
                $template_id = ($email || $name || $ticket_code) ? $idTemplate : '';

                $dataNeed[] = [
                    'email' => $email,
                    'name' => $name,
                    'ticket_code' => $ticket_code,
                    'template_id' => $template_id
                ];

                if ($sheet->getCell('D' . $row)->getValue()) {
                    $errFormat++;
                }
            }
        } catch (Exception $e) {
            Log::channel('common')->info($e->getMessage());
            return [
                'data' => [],
                'err' => 0
            ];
        }

        return [
            'data' => $dataNeed,
            'err' => $errFormat
        ];
    }


    private function queryImport($dataNeed, $templateId)
    {
        if (!empty($dataNeed)) {
            //validation
            $validatedData = $this->validateImportData($dataNeed, $templateId);

            if (count($validatedData['error']) > 0) {
                foreach ($validatedData['error'] as $message) {
                    $errors[] = $message;
                }
                redirect()->back()->with('error', $errors);
                return false;
            }

            $placeholders = implode(',', array_map(function ($item) {
                return '(' . implode(',', array_fill(0, count($item), '?')) . ')';
            }, $validatedData['data']));
            $bindings = array_reduce($validatedData['data'], function ($carry, $item) {
                $carry[] = $item['email'];
                $carry[] = $item['name'];
                $carry[] = $item['template_id'];
                return $carry;
            }, []);

            $query = "with data(email, name, template_id) as (
                   values " . $placeholders . "
                )
                insert into recipients (template_id, name, email, status, created_at)
                select CAST(template_id AS INT), name, email, 'pending', now()
                from data d";

            try {
                DB::statement($query, $bindings);
            } catch (\Exception $e) {
                Log::channel('common')->info($e->getMessage());
            }
            return true;
        }
    }

    private function queryImportTicket($dataNeed, $templateId)
    {
        if (!empty($dataNeed)) {
            //validation
            $validatedData = $this->validateImportDataTicket($dataNeed, $templateId);
            if (count($validatedData['error']) > 0) {
                foreach ($validatedData['error'] as $message) {
                    $errors[] = $message;
                }
                redirect()->back()->with('error', $errors);
                return false;
            }

            $placeholders = implode(',', array_map(function ($item) {
                return '(' . implode(',', array_fill(0, count($item), '?')) . ')';
            }, $validatedData['data']));
            $bindings = array_reduce($validatedData['data'], function ($carry, $item) {
                $carry[] = $item['email'];
                $carry[] = $item['name'];
                $carry[] = $item['ticket_code'];
                $carry[] = $item['template_id'];
                return $carry;
            }, []);
            $query = "with data(email, name, ticket_code, template_id) as (
                   values " . $placeholders . "
                )
                insert into recipients (template_id, name, email, ticket_code, status, created_at)
                select CAST(template_id AS INT), name, email, ticket_code, 'pending', now()
                from data d";

            try {
                DB::statement($query, $bindings);
            } catch (\Exception $e) {
                Log::channel('common')->info($e->getMessage());
            }
            return true;
        }
    }

    private function validateImportData($import_data, $templateId)
    {
        $validatedData = array(
            'data'  => array(),
            'error' => array()
        );

        $uniqueEmail = array();

        foreach ($import_data as $index => $data) {
            $line = $index + 1;
            $name  = isset($data['name']) ? $data['name'] : '';
            $email = isset($data['email']) ? $data['email'] : '';

            $name  = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
            $email = iconv('UTF-8', 'ASCII//TRANSLIT', $email);

            //Replace semua jenis white-space tab/enter/spasi atau spasi berlebih menjadi 1x spasi
            $name = preg_replace('/\s+/', ' ', $name);

            $data['name']  = ucwords(trim($name));
            $data['email'] = str_replace(' ', '', $email);

            array_push($validatedData['data'], $data);

            $validator = Validator::make(
                $data,
                [
                    'name' => 'required|regex:/^[a-z_.\.\'\-.\s]+$/i',
                    'email' => ['required', 'email', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i']
                ]
            );

            if (in_array($data['email'], $uniqueEmail)) {
                // Nilai duplikat ditemukan, lakukan tindakan yang sesuai
                $msgText = 'Kolom Email Duplikat ' . " on line " . ($line) . ' >> name : ' . $data['name'] . ' | email : ' . $data['email'];
                array_push($validatedData['error'], $msgText);
                // Misalnya, hentikan proses upload atau tampilkan pesan kesalahan
            } else {
                // Tambahkan nilai ke dalam array $uniqueValues
                $uniqueEmail[] = $data['email'];
            }


            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $msgText = $error . " on line " . ($line) . ' >> name : ' . $data['name'] . ' | email : ' . $data['email'];
                    array_push($validatedData['error'], $msgText);
                }
            }
            if ($validatedData['error']) {
                return $validatedData;
            }
        }

        $dataEmail = array_unique(array_column($import_data, 'email'));

        $check = (new Recipient())->checkDuplicateEmailByTemplateId($templateId, $dataEmail);

        if (!empty($check)) {
            $msgText = 'Duplicate Email [' . implode(', ', $check) . ']';
            array_push($validatedData['error'], $msgText);
        }

        return $validatedData;
    }

    private function validateImportDataTicket($import_data, $templateId)
    {
        $validatedData = array(
            'data'  => array(),
            'error' => array()
        );

        $uniqueTicket = array();

        foreach ($import_data as $index => $data) {
            $line = $index + 1;
            $name  = isset($data['name']) ? $data['name'] : '';
            $email = isset($data['email']) ? $data['email'] : '';

            $name  = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
            $email = iconv('UTF-8', 'ASCII//TRANSLIT', $email);

            //Replace semua jenis white-space tab/enter/spasi atau spasi berlebih menjadi 1x spasi
            $name = preg_replace('/\s+/', ' ', $name);

            $data['name']  = ucwords(trim($name));
            $data['email'] = str_replace(' ', '', $email);
            $data['ticket_code'] = $data['ticket_code'] ?? '';

            array_push($validatedData['data'], $data);

            $validator = Validator::make(
                $data,
                [
                    'name' => 'required|regex:/^[a-z_\.\'\-.\s]+$/i',
                    'email' => 'required|email|regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i',
                    'ticket_code' => [
                        'required'
                    ]
                ]
            );

            if (in_array($data['ticket_code'], $uniqueTicket)) {
                // Nilai duplikat ditemukan, lakukan tindakan yang sesuai
                $msgText = 'Kolom Ticket Code Duplikat ' . " on line " . ($line) . ' >> name : ' . $data['name'] . ' | email : ' . $data['email'] . ' | ticket : ' . $data['ticket_code'];
                array_push($validatedData['error'], $msgText);
                // Misalnya, hentikan proses upload atau tampilkan pesan kesalahan
            } else {
                // Tambahkan nilai ke dalam array $uniqueValues
                $uniqueTicket[] = $data['ticket_code'];
            }

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $msgText = $error . " on line " . ($line) . ' >> name : ' . $data['name'] . ' | email : ' . $data['email'];
                    array_push($validatedData['error'], $msgText);
                }
            }
        }
        if ($validatedData['error']) {
            return $validatedData;
        }

        $dataEmail = array_unique(array_column($import_data, 'ticket_code'));

        $check = (new Recipient())->checkDuplicateTicketByTemplateId($templateId, $dataEmail);

        if (!empty($check)) {
            $msgText = 'Duplicate Ticket [' . implode(',', $check) . ']';
            array_push($validatedData['error'], $msgText);
        }

        return $validatedData;
    }

    public function get_status_total($templateId, $json = false)
    {
        $recipientCount = (new Recipient())->getRecipientCount($templateId);

        $recipients = [
            'pending' => 0,
            'sending' => 0,
            'failed' => 0,
            'success' => 0
        ];

        if (!empty($recipientCount)) {
            foreach ($recipientCount as $value) {
                if ($value->status == 'pending') {
                    $recipients['pending'] = $value->total;
                } elseif ($value->status == 'sending') {
                    $recipients['sending'] = $value->total;
                } elseif ($value->status == 'success') {
                    $recipients['success'] = $value->total;
                } elseif ($value->status == 'failed') {
                    $recipients['failed'] = $value->total;
                }
            }
            $recipients['all'] = $recipients['pending'] + $recipients['sending'] + $recipients['success'] + $recipients['failed'];
        }

        if ($json) {
            return response()->json($recipients);
        } else {
            return $recipients;
        }
    }
}
