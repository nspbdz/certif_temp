<?php

namespace App\Library;

use App\Models\Log;
use Illuminate\Support\Arr;

class LoggingBuilder
{
    private $page;
    private $activity;
    private $dataAfter;
    private $dataBefore = null;
    private $extra = null;
    private $diffCondition = false;

    public function setpage($page): LoggingBuilder
    {
        $this->page = $page;
        return $this;
    }

    public function setActivity($activity): LoggingBuilder
    {
        $this->activity = $activity;
        return $this;
    }
    public function setDataBefore($dataBefore): LoggingBuilder
    {
        $this->dataBefore = $dataBefore;
        return $this;
    }

    public function setDataAfter($dataAfter): LoggingBuilder
    {
        $this->dataAfter = $dataAfter;
        return $this;
    }

    public function setExtra($extra): LoggingBuilder
    {
        $this->extra = $extra;
        return $this;
    }

    public function retry()
    {
        $this->activity = json_encode($this->handleExtra()); // memanggil fungsi savedata handleExtra
        $this->saveData($this->activity, "Retry"); // memanggil fungsi savedata
    }

    public function send()
    {
        $this->activity = json_encode($this->handleExtra()); // memanggil fungsi savedata handleExtra
        $this->saveData($this->activity, "Send"); // memanggil fungsi savedata
    }

    public function create()
    {
        $this->activity = json_encode($this->activity);
        $this->saveData($this->activity, "Add"); // memanggil fungsi savedata

    }

    public function edit()
    {
        $this->dataBefore = $this->arrExcept($this->dataBefore); // memanggil fungsi arrExcept
        $this->dataAfter = $this->arrExcept($this->dataAfter); // memanggil fungsi arrExcept
        $diff = array_diff($this->dataAfter, $this->dataBefore);
        if (count($diff) > 0) {
            $this->diffCondition = true;
            $result = [];
            $result['id'] = $this->dataBefore['id'];
            foreach ($diff as $key => $value) {
                if (strip_tags($this->dataAfter[$key]) !=  $this->dataAfter[$key]) {
                    $result[$key] = 'updated';
                } else {
                    $result[$key] = $this->dataBefore[$key] . ' -> ' . $value;
                }
            }
            $this->dataAfter = json_encode($result); // //digunakan untuk menjadikan data json 
        }

        return $this;
    }

    public function build()
    {

        if ($this->diffCondition) {

            if ($this->activity != null) {
                $this->dataAfter = json_decode($this->dataAfter, true);
                $this->dataAfter = json_encode(array_merge($this->activity, $this->dataAfter));
            }
            $this->saveData($this->dataAfter, "edit"); // memanggil fungsi savedata
        }
    }


    public function delete()
    {
        $this->activity = json_encode($this->activity);
        $this->saveData($this->activity, "Delete"); // memanggil fungsi savedata
    }

    private function saveData($data, $action)
    {
        // print_r($data);
        // die();
        $username = request('identity')['username']; // mengambil username yang sedang login

        $log = new Log;
        $log->page = $this->page;
        $log->action = $action;
        $log->activity = $data;
        $log->username_dc = $username;
        $log->save();
    }

    private function arrExcept($data)
    {
        $data = Arr::except($data->toArray(), ['created_at', 'updated_at', 'deleted_at']); //mengecualikan created_at updated_at deleted_at
        return $data;
    }

    private function handleExtra()
    {
        $this->extra = ['extra' => $this->extra];
        $this->activity = json_decode(json_encode($this->activity), true);
        $this->activity = array_merge($this->activity, $this->extra);

        return $this->activity;
    }
}
