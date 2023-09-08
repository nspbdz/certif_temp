<?php

namespace App\Http\Controllers;

use App\Services\DetikConnectService;
use App\Models\Users;

class AuthController extends Controller
{
    protected $detikService;

    public function __construct()
    {
        $this->detikService = new DetikConnectService('backend');
    }

    public function auth()
    {
        return view('login', [
            'loginUrl' => $this->detikService->detik->getLoginUrl(),
        ]);
    }

    public function callback()
    {
        $this->detikService->detik->authCallback();
        $userDetail = $this->detikService->getUserIdentityBySession();
        $data = Users::where('dc_id', '=', $userDetail['_id'])->first();
        if (empty($data)) {
            $users = new Users;
            $users->dc_id = $userDetail['_id'];
            $users->username = $userDetail['username'];
            $users->save();
        }
        return redirect()->route('backend.dashboard');
    }

    public function logout()
    {
        $this->detikService->logout(route('backend.login'));
    }
}
