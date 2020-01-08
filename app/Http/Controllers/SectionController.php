<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class SectionController extends BaseController
{
    public function index() {
        $json = array();
        $listSection = $this->database->getReference('mst_section')->getValue();
        if($listSection){
          $json = json_encode($listSection);   
        } 
        return response([
            'error' => false,
            'data' => compact('listSection', $json)
        ], 200);
    }

    public function store(Request $request) {
        $error = true;
        $json = null;
        $key = null;
        $data = array(
            'sec_vietnamese' => $request->sec_vietnamese,
            'sec_japanese' => $request->sec_japanese,
            'sec_description' => $request->sec_description,
            'sec_flag ' => 1,
        );
        //check duplicate username
        $reference = $this->database->getReference('mst_section')->getValue();
        $errorDuplicate = false;
        if ($reference) {
            foreach ($reference as $key => $value) {
                if (is_array($value)) {
                    if ($data['sec_vietnamese'] == $value['sec_vietnamese']) {
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
        $configTypeId = $this->database->getReference('mst_section')->push($data)->getKey();
        //get data
        if ($configTypeId) {
            $currentConfigType = $this->database->getReference('mst_section/' . $configTypeId)->getValue();
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
        $keySection = isset($request->keySection) ? $request->keySection : NULL;
        if (!$keySection) {
            return response([
                'error' => true,
                'data' => 'Key section not exist !'
            ], 200);
        }
        $data = array(
            'sec_vietnamese' => $request->sec_vietnamese,
            'sec_japanese' => $request->sec_japanese,
            'sec_description' => $request->sec_description,
            'acc_flag' => 1,
        );
        //check duplicate username
        $reference = $this->database->getReference('mst_section')->getValue();
        $errorDuplicate = false;
        $keyExist = false;
        foreach ($reference as $key => $value) {
            if (is_array($value) && $keySection != $key) {
                if ($data['sec_vietnamese'] == $value['sec_vietnamese']) {
                    $errorDuplicate = true;
                }
            }
            if ($keySection == $key) {
                $keyExist = true;
            }
        }
        if ($errorDuplicate) {
            return response([
                'error' => true,
                'data' => 'Section is duplicate !'
                    ], 200);
        }
        //check key exist
        if ($keyExist) {
            //update
            $this->database->getReference('mst_section/' . $keySection)->update($data);
            //get data
            $currentConfigType = $this->database->getReference('mst_section/' . $keySection)->getValue();
            $error = false;
            $json = json_encode($currentConfigType);
        }
        return response([
            'error' => $error,
            'data' => $json
        ], 200);
    }

    public function delete(Request $request) {
        $keySection = isset($request->keySection) ? $request->keySection : NULL;
        if (!$keySection) {
            return response([
                'error' => true,
                'data' => 'Key section not exist !'
            ], 200);
        }
        //delete
        $this->database->getReference('mst_section/' . $keySection)->remove();
        return response([
            'error' => false,
        ], 200);
    }
}
