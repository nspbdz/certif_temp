<?php

namespace App\Console\Commands;

use App\Mail\CertifBlastMail;
use App\Models\Recipient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Certificate and Ticket Email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $start_time = date('Y-m-d H:i:s');
        try {
            $users = (new Recipient)->getUsersSendingStatus();

            $certificate_image = array_filter(array_unique(array_column($users, 'certificate_image')));

            if (!empty($certificate_image)) {
                foreach ($certificate_image as $image) {
                    $img_file = config('config.akcdn.full_url') . $image . '?m=1';
                    Storage::put(config('config.certif_temp_path') . $image, file_get_contents($img_file));
                }
            }

            if (empty($users)) {
                echo 'pending user is empty';
                return false;
            }

            foreach ($users as $value) {
                $email = trim($value->email);
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($value->name)) {
                    $fullname = $this->getFormattedName($value->name);

                    ($value->email_type == 'certificate') ?  $this->createPdf($fullname, $value, $email) : $this->sendMailModerated($email, $value);

                    $records = (new Recipient())->updateStatus($value->id, 'success');
                    if ($records) {
                        $info = "[Certificate] | Email : {$value->email} | Firstname : {$value->name} | themeId : {$value->template_id} || message : Email Sent";
                        Log::channel('common')->info($info);
                    }
                } else {
                    $error = "[Certificate] | Email : {$value->email} | Firstname : {$value->name} | themeId : {$value->template_id} || message : Firstname or email is empty or email format is not valid";
                    (new Recipient())->updateStatus($value->id, 'failed');
                    Log::channel('common')->info($error);
                }
            }
        } catch (\Exception $e) {
            $msgText = $e->getMessage();

            $error = "[Certificate] | Email : {$value->email} | Firstname : {$value->name} | themeId : {$value->template_id} || message : {$msgText}";
            (new Recipient())->updateStatus($value->id, 'failed');
            Log::channel('common')->info($e->getMessage());
        }

        $this->deleteStorageFile($certificate_image);
        $end_time = date('Y-m-d H:i:s');
        $report = 'done : from ' . $start_time . ' to ' . $end_time;
        echo $report;
        Log::channel('common')->info($report);
    }

    private function createPdf($name, $certificate, $email = null)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set default monospaced font
        $pdf->SetCreator($certificate->certificate_author ?? '');

        $pdf->SetAuthor($certificate->certificate_author ?? '');
        $pdf->SetTitle($certificate->pdf_title ?? '');
        $pdf->SetSubject('Certificate');

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);

        // remove default footer
        $pdf->setPrintFooter(false);

        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // convert TTF font to TCPDF format and store it on the fonts folder
        $font_path = public_path('fonts/') . ($certificate->font_type ?? 'montserrat-bold') . '.ttf';

        $fontname = \TCPDF_FONTS::addTTFfont($font_path, 'TrueTypeUnicode', '', 96);

        // set font
        $pdf->SetFont($fontname, '', 48, '', false);

        // remove default header
        $pdf->setPrintHeader(false);

        // add a page
        $pdf->AddPage('L', 'A4');

        // -- set new background ---
        // get the current page break margin
        $bMargin = $pdf->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $pdf->getAutoPageBreak();
        // disable auto-page-break
        $pdf->SetAutoPageBreak(false, 0);
        // set bacground image

        $fileName = $certificate->certificate_image;
        $img_file = storage_path('app/' . config('config.certif_temp_path') . $fileName);
        $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);

        // restore auto-page-break status
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $pdf->setPageMark();

        // Print a text
        $html = '<span style="color:' . $certificate->font_color . ';text-align:center;font-weight:bold;font-size:34pt;line-height:' . config('config.line_height.' . $certificate->text_position) . '; margin:top 10px !important;"><br><br>'
            . $name .
            '</span>';
        $pdf->writeHTML($html, true, false, true, false, '');

        //Close and output PDF document
        //email exists = send email, else show sertifikat in browser
        if ($email) {
            $filename = ($certificate->certificate_name ?? 'certificate') . '.pdf';
            $attachment['name'] = $filename;
            $attachment['fullname'] = $name;
            $attachment['tcpdf'] = $pdf->Output($filename, 'S');

            $this->sendMailModerated($email, $certificate, $attachment);
        } else {
            $pdf->Output(($certificate->certificate_name ?? 'certificate') . '.pdf', 'I');
            exit;
        }
    }

    private function getFormattedName($name)
    {
        $fullname = $name;
        $nameArr = explode(' ', $name);

        if (count($nameArr) >= 3) {
            $initial = substr(str_replace("'", '', $nameArr[2]), 0, 1);
            $fullname = $nameArr[0] . ' ' . $nameArr[1] . ' ' . $initial;
        }

        return ucwords(strtolower($fullname)); // name in title case
    }

    public function sendMailModerated($email, $certificate, $attachment = null)
    {
        $to        = $email;

        $data = [
            'certificate_name'   => $certificate->certificate_name ?? '',
            'sender_email'   => $certificate->sender_email ?? '',
            'sender_name'   => $certificate->sender_name ?? '',
            'header_image' => config('config.akcdn.full_url') . ($certificate->header_image ?? '') . '?m=1'
        ];

        $data['subject'] = $certificate->email_subject ?? '';
        $data['body'] = preg_replace('/\$\$name/', $certificate->name, $certificate->email_body ?? '');

        if ($certificate->email_type == 'ticket') {
            $data['body'] = preg_replace('/\$\$ticketcode/', $certificate->ticket_code, $data['body']);
        }

        Mail::to($to)->send(new CertifBlastMail($data, $attachment['tcpdf'] ?? null));
    }

    private function deleteStorageFile($files)
    {
        // Menghapus setiap file dalam direktori
        foreach ($files as $file) {
            Storage::delete(config('config.certif_temp_path') . $file);
        }
    }
}
