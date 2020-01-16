<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Crypt;

class AccountController extends BaseController
{
    public function index() {
        $json = array();
        $listAccount = $this->database->getReference('mst_account')->getValue();
        foreach($listAccount as $key => $value){
            $listAccount[$key]['acc_password'] = Crypt::decryptString($value['acc_password']);
        }
        if($listAccount){
          $json = json_encode($listAccount);   
        } 
        return response([
            'error' => false,
            'data' => compact('listAccount', $json)
        ], 200);
    }

    public function store(Request $request) {
        $error = true;
        $json = null;
        $key = null;
        $data = array(
            'acc_username' => $request->acc_username,
            'acc_password' => Crypt::encryptString($request->acc_password),
            'acc_email' => $request->acc_email,
            'acc_flag' => 1,
        );
//        $encrypted = Crypt::encryptString('Hello world.');
//
//$decrypted = Crypt::decryptString($encrypted);
//print_r($encrypted);die;
        //check duplicate username
        $reference = $this->database->getReference('mst_account')->getValue();
        $errorDuplicate = false;
        if ($reference) {
            foreach ($reference as $key => $value) {
                if (is_array($value)) {
                    if ($data['acc_username'] == $value['acc_username'] || $data['acc_email'] == $value['acc_email']) {
                        $errorDuplicate = true;
                    }
                }
            }
        }
        if ($errorDuplicate) {
            return response([
                'error' => true,
                'data' => 'Username is duplicate !'
                    ], 200);
        }
        //insert
        $accountId = $this->database->getReference('mst_account')->push($data)->getKey();
        //get data
        if ($accountId) {
            $currentAccount = $this->database->getReference('mst_account/' . $accountId)->getValue();
            $currentAccount['acc_password'] = Crypt::decryptString($currentAccount['acc_password']);
            $error = false;
            $json = json_encode($currentAccount);
            $key = $accountId;
        }
        return response([
            'error' => $error,
            'data' => $json,
            'key' => $key
        ], 200);
    }

    public function update(Request $request) {
        $error = true;
        $json = 'Not update, something wrong !';
        $keyAccount = isset($request->keyAccount) ? $request->keyAccount : NULL;
        if (!$keyAccount) {
            return response([
                'error' => true,
                'data' => 'Key account not exist !'
            ], 200);
        }
        $data = array(
            'acc_username' => $request->acc_username,
            'acc_password' => Crypt::encryptString($request->acc_password),
            'acc_email' => $request->acc_email,
            'acc_flag' => 1,
        );
        //check duplicate username
        $reference = $this->database->getReference('mst_account')->getValue();
        $errorDuplicate = false;
        $keyExist = false;
        foreach ($reference as $key => $value) {
            if (is_array($value) && $keyAccount != $key) {
                if ($data['acc_username'] == $value['acc_username'] || $data['acc_email'] == $value['acc_email']) {
                    $errorDuplicate = true;
                }
            }
            if ($keyAccount == $key) {
                $keyExist = true;
            }
        }
        if ($errorDuplicate) {
            return response([
                'error' => true,
                'data' => 'Username is duplicate !'
                    ], 200);
        }
        //check key exist
        if ($keyExist) {
            //update
            $this->database->getReference('mst_account/' . $keyAccount)->update($data);
            //get data
            $currentAccount = $this->database->getReference('mst_account/' . $keyAccount)->getValue();
            $error = false;
            $json = json_encode($currentAccount);
        }
        return response([
            'error' => $error,
            'data' => $json
        ], 200);
    }

    public function delete(Request $request) {
        $keyAccount = isset($request->keyAccount) ? $request->keyAccount : NULL;
        if (!$keyAccount) {
            return response([
                'error' => true,
                'data' => 'Key account not exist !'
            ], 200);
        }
        //delete
        $this->database->getReference('mst_account/' . $keyAccount)->remove();
        return response([
            'error' => false,
        ], 200);
    }
}
