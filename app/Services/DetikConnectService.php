<?php

namespace App\Services;

use App\Http\Middleware\DetikConnect;
use DetikConnect\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class DetikConnectService
{

    public $config;
    public $detik;
    public $isLogin = false;
    private $userIdentity;
    private $app_name;
    private $service;
    private $sessionId;

    public function __construct($service = "backend")
    {
        $this->service = $service;
        $this->app_name = config('app.name');
        $this->config = config('detikconnect');
        $this->detik  = new Client($this->config[$service]);
    }

    /**
     * get `DetikConnect\Client` Class Instance
     * @return \DetikConnect\Client $detik class Client detik
     */
    public function getDetik()
    {
        return $this->detik;
    }

    /**
     *  logout detikconnect with session flush
     *  @param string $path URL of target redirect after logout
     *  @return void
     */
    public function logout($path)
    {
        Session::flush();
        $this->detik->logout($path);
    }

    /**
     * Get Session Id of DetikConnect
     * @return string|null string of SessionId or null if no session id
     */
    public function getSessionId()
    {
        return $this->detik->getSessionId();
    }

    /**
     * Get User Identity (User Detail) by Session Id with `View::Share`
     * @param boolean $isFull Get Full Data or Minimal Data
     * @return false|array Get `User Identity (User Detail)` Data, and `false` if no `sessionId` or `is_login is false` or `status is error`
     */
    public function getUserIdentity($isFull = false)
    {
        $this->sessionId = $this->detik->getSessionId();

        // jika session id tidak ada
        if (empty($this->sessionId)) {
            return false;
        }

        $this->userIdentity = (array) $this->detik->getUserDetailBySession($this->sessionId, $isFull);

        if ($isFull) {
            //check status jika getUserDetailBySession nya "Full Data"
            if ($this->getIdentityStatus('error')) {
                return false;
            }
        } else {
            //check is_login jika getUserDetailBySession nya "Minimal Data"
            $this->isLogin = !isset($this->userIdentity['is_login']) ? false : $this->userIdentity['is_login'];
            if (!$this->isLogin) {
                return false;
            }
        }

        // set profilePicture jika "Full Data" & avatar jika "minimal data"
        $this->setViewShare($this->userIdentity, $this->userIdentity[$isFull ? 'profilePicture' : 'avatar']);

        return $this->userIdentity;
    }

    /**
     * Get User Identity (User Detail) by Session Id
     * @return mixed Get `User Identity (User Detail)` Data, and null if no sessionId
     */
    public function getUserIdentityBySession()
    {
        $this->sessionId          = $this->detik->getSessionId();
        $this->userIdentity = $this->detik->getUserDetailBySession($this->sessionId, true);

        return $this->userIdentity;
    }

    /**
     * Get User Identity Status (default: status `true` if error)
     * @return boolean Get User Identity Status (default: status `true` if error)
     */
    public function getIdentityStatus($status = 'error')
    {
        return isset($this->userIdentity['status']) && $this->userIdentity['status'] === $status;
    }

    /**
     * Get User Role in Specific Project / Application
     * @return string|null
     */
    public function getRole(?string $projectName = null)
    {
        $projectName = $projectName ?? $this->app_name;
        return $this->userIdentity['staff'][$projectName] ?? null;
    }

    /**
     * Private: Set View Share Data
     * @param $dataUser data of user identity (user detail)
     * @param $avatar URL of image `avatar` or `profilePicture` 
     */
    private function setViewShare($dataUser, $avatar)
    {
        if($this->service == 'frontend'){
            View::share([
                'login_url'  => $this->detik->getLoginUrl() .'?u=',
                'onLogin'    => $this->detik->helperAutologin("onLoginClient")
            ]);
        }
        
        View::share([
            'id'       => $dataUser['id'],
            'is_login' => true,
            'username' => $this->realname($dataUser),
            'email'    => $dataUser['email'],
            'avatar'   => $avatar,
            'phone'    => $dataUser['mobilePhone'] ?? '',
        ]);
    }

    /**
     * Get Real Name Data
     * @param $dataUser data
     */
    private function realname($dataUser)
    {
        if (!empty($dataUser['first_name'])) {
            return $dataUser['first_name'];
        }

        if (!empty($dataUser['name'])) {
            return $dataUser['name'];
        }

        return $dataUser['username'];
    }
}
