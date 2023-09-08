<?php

/**
 * This is DetikCDN Class for Upload Image Or File to CDN
 */

namespace App\Library;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class DetikCDN
{
    private $akcdn;
    private $cdn;
    private $env;

    public function __construct($akcdn = null, $cdn = null, $env = null)
    {
        $this->akcdn = $akcdn ?? Config::get('config.ip.akcdn');
        $this->cdn = $cdn ?? Config::get('config.ip.cdn');
        $this->env = $env ?? Config::get('config.env');
        
        
    }
    public function sendImage($moduleName, $fileName, $data): void
    {
        $url = "{$this->akcdn}images/microsite/{$this->env}/{$moduleName}/$fileName";
        $this->request('PUT', $url, $data);
    }

    public function sendFile($moduleName, $fileName, $data): void
    {
        $url = "{$this->cdn}microsite/{$this->env}/{$moduleName}/$fileName";
        $this->request('PUT', $url, $data);
    }

    public function request($method, $url, $parameters)
    {
        $client = new Client();
        $res = $client->request($method, $url, [
            'body' => $parameters
        ]);

        $result = $res->getBody()->getContents();
        return $result;
    }
}
