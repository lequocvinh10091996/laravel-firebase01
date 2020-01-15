<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class TranslateMeanController extends BaseController
{
    public function index() {
        $json = array();
        $listTranslateMean = $this->database->getReference('mst_translate_mean')->getValue();
        $listConfigType = $this->database->getReference('mst_section')->getValue();
        
        if ($listTranslateMean) {
            foreach ($listTranslateMean as $key => $value) {
                if (isset($listConfigType[$value['sec_id']])) {
                    $listTranslateMean[$key]['sec_vietnamese'] = $listConfigType[$value['sec_id']]['sec_vietnamese'];
                }
            }
            $json = json_encode($listTranslateMean);   
        } 
        return response([
            'error' => false,
            'data' => compact('listTranslateMean', $json)
        ], 200);
    }

    public function store(Request $request) {
        $error = true;
        $json = null;
        $key = null;
        $data = array(
            'sec_id' => $request->sec_id,
            'tm_english_translate' => "$request->tm_english_translate",
            'tm_japanese_translate' => $request->tm_japanese_translate,
            'tm_japanese_higarana' => $request->tm_japanese_higarana,
            'tm_vietnamese_translate' => $request->tm_vietnamese_translate,
            'tm_flag' => 1,
        );
        //check duplicate username
        $reference = $this->database->getReference('mst_translate_mean')->getValue();
        $errorDuplicate = false;
        if ($reference) {
            foreach ($reference as $key => $value) {
                if (is_array($value)) {
                    if (($data['sec_id'] == $value['sec_id']) && ($data['tm_japanese_translate'] == $value['tm_japanese_translate'] ||
                        $data['tm_japanese_higarana'] == $value['tm_japanese_higarana'] ||
                        $data['tm_vietnamese_translate'] == $value['tm_vietnamese_translate'])) {
                        $errorDuplicate = true;
                    }
                }
            }
        }
        if ($errorDuplicate) {
            return response([
                'error' => true,
                'data' => 'Translate mean is duplicate !'
                    ], 200);
        }
        //insert
        $translateMeanId = $this->database->getReference('mst_translate_mean')->push($data)->getKey();
        //get data
        if ($translateMeanId) {
            $currentTranslateMean = $this->database->getReference('mst_translate_mean/' . $translateMeanId)->getValue();
            //get name of id in config type
            $listConfigType = $this->database->getReference('mst_section')->getValue();
            if (isset($listConfigType[$data['sec_id']])) {
                $currentTranslateMean['sec_vietnamese'] = $listConfigType[$data['sec_id']]['sec_vietnamese'];
            }
            
            $error = false;
            $json = json_encode($currentTranslateMean);
            $key = $translateMeanId;
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
        $keyTranslateMean = isset($request->keyTranslateMean) ? $request->keyTranslateMean : NULL;
        if (!$keyTranslateMean) {
            return response([
                'error' => true,
                'data' => 'Key translate mean not exist !'
            ], 200);
        }
        $data = array(
            'sec_id' => $request->sec_id,
            'tm_english_translate' => isset($request->tm_english_translate) && $request->tm_english_translate != "undefined" ? $request->tm_english_translate : "",
            'tm_japanese_translate' => $request->tm_japanese_translate,
            'tm_japanese_higarana' => $request->tm_japanese_higarana,
            'tm_vietnamese_translate' => $request->tm_vietnamese_translate,
            'tm_flag' => 1,
        );
        //check duplicate username
        $reference = $this->database->getReference('mst_translate_mean')->getValue();
        $errorDuplicate = false;
        $keyExist = false;
        foreach ($reference as $key => $value) {
            if (is_array($value) && $keyTranslateMean != $key) {
                if (($data['sec_id'] == $value['sec_id']) && ($data['tm_japanese_translate'] == $value['tm_japanese_translate'] ||
                        $data['tm_japanese_higarana'] == $value['tm_japanese_higarana'] ||
                        $data['tm_vietnamese_translate'] == $value['tm_vietnamese_translate'])) {
                        $errorDuplicate = true;
                    }
            }
            if ($keyTranslateMean == $key) {
                $keyExist = true;
            }
        }
        if ($errorDuplicate) {
            return response([
                'error' => true,
                'data' => 'Translate mean is duplicate !'
                    ], 200);
        }
        //check key exist
        if ($keyExist) {
            //update
            $this->database->getReference('mst_translate_mean/' . $keyTranslateMean)->update($data);
            //get data
            $currentTranslateMean = $this->database->getReference('mst_translate_mean/' . $keyTranslateMean)->getValue();
            //get name of id in config type
            $listConfigType = $this->database->getReference('mst_section')->getValue();
            if (isset($listConfigType[$data['sec_id']])) {
                $currentTranslateMean['sec_vietnamese'] = $listConfigType[$data['sec_id']]['sec_vietnamese'];
            }
            
            $error = false;
            $json = json_encode($currentTranslateMean);
        }
        return response([
            'error' => $error,
            'data' => $json
        ], 200);
    }

    public function delete(Request $request) {
        $keyTranslateMean = isset($request->keyTranslateMean) ? $request->keyTranslateMean : NULL;
        if (!$keyTranslateMean) {
            return response([
                'error' => true,
                'data' => 'Key translate mean not exist !'
            ], 200);
        }
        //delete
        $this->database->getReference('mst_translate_mean/' . $keyTranslateMean)->remove();
        return response([
            'error' => false,
        ], 200);
    }
}
