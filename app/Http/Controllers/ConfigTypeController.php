<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class ConfigTypeController extends BaseController
{
    public function index() {
        $json = array();
        $listConfigType = $this->database->getReference('mst_config_type')->getValue();
        if($listConfigType){
          $json = json_encode($listConfigType);   
        } 
        return response([
            'error' => false,
            'data' => compact('listConfigType', $json)
        ], 200);
    }

    public function store(Request $request) {
        $error = true;
        $json = null;
        $key = null;
        $data = array(
            'cty_config_name' => $request->cty_config_name,
            'cty_config_descrip' => $request->cty_config_descrip,
            'cty_flag' => 1,
        );
        //check duplicate username
        $reference = $this->database->getReference('mst_config_type')->getValue();
        $errorDuplicate = false;
        if ($reference) {
            foreach ($reference as $key => $value) {
                if (is_array($value)) {
                    if ($data['cty_config_name'] == $value['cty_config_name']) {
                        $errorDuplicate = true;
                    }
                }
            }
        }
        if ($errorDuplicate) {
            return response([
                'error' => true,
                'data' => 'Config type is duplicate !'
                    ], 200);
        }
        //insert
        $configTypeId = $this->database->getReference('mst_config_type')->push($data)->getKey();
        //get data
        if ($configTypeId) {
            $currentConfigType = $this->database->getReference('mst_config_type/' . $configTypeId)->getValue();
            $error = false;
            $json = json_encode($currentConfigType);
            $key = $configTypeId;
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
        $keyConfigType = isset($request->keyConfigType) ? $request->keyConfigType : NULL;
        if (!$keyConfigType) {
            return response([
                'error' => true,
                'data' => 'Key config type not exist !'
            ], 200);
        }
        $data = array(
            'cty_config_name' => $request->cty_config_name,
            'cty_config_descrip' => $request->cty_config_descrip,
            'acc_flag' => 1,
        );
        //check duplicate username
        $reference = $this->database->getReference('mst_config_type')->getValue();
        $errorDuplicate = false;
        $keyExist = false;
        foreach ($reference as $key => $value) {
            if (is_array($value) && $keyConfigType != $key) {
                if ($data['cty_config_name'] == $value['cty_config_name']) {
                    $errorDuplicate = true;
                }
            }
            if ($keyConfigType == $key) {
                $keyExist = true;
            }
        }
        if ($errorDuplicate) {
            return response([
                'error' => true,
                'data' => 'Config type is duplicate !'
                    ], 200);
        }
        //check key exist
        if ($keyExist) {
            //update
            $this->database->getReference('mst_config_type/' . $keyConfigType)->update($data);
            //get data
            $currentConfigType = $this->database->getReference('mst_config_type/' . $keyConfigType)->getValue();
            $error = false;
            $json = json_encode($currentConfigType);
        }
        return response([
            'error' => $error,
            'data' => $json
        ], 200);
    }

    public function delete(Request $request) {
        $keyConfigType = isset($request->keyConfigType) ? $request->keyConfigType : NULL;
        if (!$keyConfigType) {
            return response([
                'error' => true,
                'data' => 'Key account not exist !'
            ], 200);
        }
        //delete
        $this->database->getReference('mst_config_type/' . $keyConfigType)->remove();
        return response([
            'error' => false,
        ], 200);
    }
}
