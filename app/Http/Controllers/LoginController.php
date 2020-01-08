<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Crypt;

class LoginController extends BaseController {

    function index() {
        return view('login');
    }

    function checklogin(Request $request) {
        $this->validate($request, [
            'acc_email' => 'required',
            'acc_password' => 'required',
        ]);
        
        $user_data = array(
            'acc_email' => $request->get('acc_email'),
            'acc_password' => $request->get('acc_password'),
        );
        $reference = $this->database->getReference('mst_account')->getValue();
        $successlogin = false;
        foreach($reference as $key => $value){
            if(is_array($value)) {
                if ($user_data['acc_email'] == $value['acc_email'] && $user_data['acc_password'] == Crypt::decryptString($value['acc_password'])) {
                    session(['acc_username' =>  $value['acc_username']]);
                    $successlogin = true;
                }
            }
        }
        if ($successlogin) {
            return redirect('/account/index');
        } else {
            return back()->with('error', 'Wrong Login Details');
        }
    }

    function logout() {
        session()->flush();
        return redirect('/');
    }

}
