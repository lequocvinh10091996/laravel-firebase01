<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class TerminologyController extends BaseController
{
    public function index() {
        $json = array();
        $listTerminology = $this->database->getReference('mst_translate_mean')->getValue();
        $listSection = $this->database->getReference('mst_section')->getValue();
        
        if ($listTerminology) {
            foreach ($listTerminology as $key => $value) {
                if (isset($listSection[$value['sec_id']])) {
                    $listTerminology[$key]['sec_vietnamese'] = $listSection[$value['sec_id']]['sec_vietnamese'];
                }
            }
            $json = json_encode($listTerminology);   
        } 
        return response([
            'error' => false,
            'data' => compact('listTerminology', $json)
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
            'tm_example' => $request->tm_example,
            'tm_insert_user' => session('acc_username'),
            'tm_flag' => 1,
        );
        //insert
        $terminologyId = $this->database->getReference('mst_translate_mean')->push($data)->getKey();
        //get data
        if ($terminologyId) {
            $currentTerminology = $this->database->getReference('mst_translate_mean/' . $terminologyId)->getValue();
            //get name of id in config type
            $listSection = $this->database->getReference('mst_section')->getValue();
            if (isset($listSection[$data['sec_id']])) {
                $currentTerminology['sec_vietnamese'] = $listSection[$data['sec_id']]['sec_vietnamese'];
            }
            
            $error = false;
            $json = json_encode($currentTerminology);
            $key = $terminologyId;
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
        $keyTerminology = isset($request->keyTerminology) ? $request->keyTerminology : NULL;
        if (!$keyTerminology) {
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
            'tm_example' => $request->tm_example,
            'tm_insert_user' => session('acc_username'),
            'tm_flag' => 1,
        );
        //check key exist
        $reference = $this->database->getReference('mst_translate_mean')->getValue();
        $keyExist = false;
        if (isset($reference[$keyTerminology])) {
            $keyExist = true;
        }
        if ($keyExist) {
            //update
            $this->database->getReference('mst_translate_mean/' . $keyTerminology)->update($data);
            //get data
            $currentTerminology = $this->database->getReference('mst_translate_mean/' . $keyTerminology)->getValue();
            //get name of id in config type
            $listSection = $this->database->getReference('mst_section')->getValue();
            if (isset($listSection[$data['sec_id']])) {
                $currentTerminology['sec_vietnamese'] = $listSection[$data['sec_id']]['sec_vietnamese'];
            }
            
            $error = false;
            $json = json_encode($currentTerminology);
        }
        return response([
            'error' => $error,
            'data' => $json
        ], 200);
    }

    public function delete(Request $request) {
        $keyTerminology = isset($request->keyTerminology) ? $request->keyTerminology : NULL;
        if (!$keyTerminology) {
            return response([
                'error' => true,
                'data' => 'Key terminology mean not exist !'
            ], 200);
        }
        //delete
        $this->database->getReference('mst_translate_mean/' . $keyTerminology)->remove();
        return response([
            'error' => false,
        ], 200);
    }
}
