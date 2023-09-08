<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Recipient extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'template_id',
        'name',
        'email',
        'ticket_code',
        'status',
    ];

    public function countSearch($templateId, $search = null, $filter = null, $emailType = null)
    {
        $query = DB::table('recipients')
            ->where('template_id', '=', $templateId)
            ->whereNull('recipients.deleted_at');
        if ($search != null) {
            $query->where(function ($query) use ($search, $emailType) {
                $query->where(DB::raw('lower(recipients.name)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(recipients.email)'), 'LIKE', '%' . $search . '%');
                if ($emailType == 'ticket') {
                    $query->orwhere(DB::raw('lower(recipients.ticket_code)'), 'LIKE', '%' . $search . '%');
                }
            });
        }
        if ($filter != '') {
            $query->where('recipients.status', $filter);
        }
        return $query->count();
    }

    public function datatables($templateId, $limit = null, $start = null, $search = null, $orderCol = null, $orderDir = null, $filter = '', $emailType = null)
    {
        $query = DB::table($this->getTable())
            ->select(
                'id',
                'name',
                'email',
                'ticket_code',
                DB::raw("to_char(created_at,'YYYY-MM-DD') as created_at"),
                'status'
            )
            ->where('deleted_at')
            ->where('template_id', $templateId);
        if ($filter != '') {
            $query->where('recipients.status', $filter);
        }
        if ($orderCol != null && $orderDir !=  null) {
            $query->orderBy($orderCol, $orderDir);
        }
        if ($search != null) {
            $query->where(function ($query) use ($search, $emailType) {
                $query->where(DB::raw('lower(recipients.name)'), 'LIKE', '%' . $search . '%');
                $query->orwhere(DB::raw('lower(recipients.email)'), 'LIKE', '%' . $search . '%');
                if ($emailType == 'ticket') {
                    $query->orwhere(DB::raw('lower(recipients.ticket_code)'), 'LIKE', '%' . $search . '%');
                }
            });
        }
        $query->offset($start)->limit($limit);
        return  $query->get();
    }

    public function scopeUpdateSend($query, $templateId)
    {
        return $query->where('status', 'pending')
            ->where('template_id', $templateId);
    }

    public function scopeDeletePending($query, $templateId)
    {
        return $query->where('template_id', $templateId)->where('status', 'pending');
    }

    public function getRecipientCount($templateId)
    {
        $query = DB::table($this->getTable())
            ->select(
                'status',
                DB::RAW("count('id') as total")
            )
            ->where('template_id', '=', $templateId)
            ->whereNull('deleted_at')
            ->groupBy('status')
            ->get();
        return $query;
    }

    public function getUsersSendingStatus()
    {
        $limit  = config('config.recipient_email_limit');
        $query  = "SELECT recipients.id, email, recipients.name, template_id, ticket_code, templates.email_type, templates.font_type, templates.font_color, templates.text_position, templates.certificate_author, templates.pdf_title, templates.certificate_image, templates.certificate_name, templates.email_subject, templates.name as template_name, templates.email_body, templates.header_image, campaigns.sender_name, campaigns.sender_email
                       FROM recipients LEFT JOIN templates ON templates.id = recipients.template_id LEFT JOIN campaigns ON campaigns.id = templates.campaign_id
                       WHERE recipients.status = 'sending' AND recipients.deleted_at IS NULL AND templates.deleted_at IS NULL AND campaigns.deleted_at IS NULL
                       ORDER BY id ASC
                       LIMIT " . $limit;
        return DB::select($query);
    }

    public function updateStatus($id, $status)
    {
        return DB::table($this->getTable())->where('id', $id)->update(['status' => $status]);
    }

    public function scopeProcessSend($query, $templateId)
    {
        $query->whereIn('status', ['sending', 'success'])
            ->where('template_id', $templateId);
    }

    public function checkEmailByTemplateId($id){
        $query = DB::table($this->getTable())
            ->select(
               'email'
            )
            ->where('template_id', '=', $id)
            ->whereNull('deleted_at')
            ->pluck('email')->toArray();
        return $query;
    }

    public function checkDuplicateEmailByTemplateId($id, $data){
        $query = DB::table($this->getTable())
            ->select(
               'email'
            )
            ->where('template_id', '=', $id)
            ->whereIn('email', $data)
            ->whereNull('deleted_at')
            ->pluck('email')->unique()->toArray();
        return $query;
    }

    public function checkDuplicateTicketByTemplateId($id, $data){
        $query = DB::table($this->getTable())
            ->select(
               'ticket_code'
            )
            ->where('template_id', '=', $id)
            ->whereIn('ticket_code', $data)
            ->whereNull('deleted_at')
            ->pluck('ticket_code')->unique()->toArray();
        return $query;
    }
}
