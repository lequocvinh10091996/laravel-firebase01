<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class SectionController extends BaseController
{
    public function index() {
        $json = array();
        $listSection = $this->database->getReference('mst_section')->getValue();
        $listTopic = $this->database->getReference('mst_topic')->getValue();
        if ($listSection) {
            foreach ($listSection as $key => $value) {
                if (isset($listTopic[$value['tp_id']])) {
                    $listSection[$key]['tp_vietnamese'] = $listTopic[$value['tp_id']]['tp_vietnamese'];
                }
            }
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
            'tp_id' => $request->tp_id,
            'sec_vietnamese' => $request->sec_vietnamese,
            'sec_japanese' => $request->sec_japanese,
            'sec_description' => $request->sec_description,
            'sec_flag' => 1,
        );
        //insert
        $sectionId = $this->database->getReference('mst_section')->push($data)->getKey();
        //get data
        if ($sectionId) {
            $currentSection = $this->database->getReference('mst_section/' . $sectionId)->getValue();
            //get name of id in topic
            $listTopic = $this->database->getReference('mst_topic')->getValue();
            if (isset($listTopic[$data['tp_id']])) {
                $currentSection['tp_vietnamese'] = $listTopic[$data['tp_id']]['tp_vietnamese'];
            }
            $error = false;
            $json = json_encode($currentSection);
            $key = $sectionId;
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
            'tp_id' => $request->tp_id,
            'sec_vietnamese' => $request->sec_vietnamese,
            'sec_japanese' => $request->sec_japanese,
            'sec_description' => $request->sec_description,
            'acc_flag' => 1,
        );
        //check key exist
        $reference = $this->database->getReference('mst_section')->getValue();
        $keyExist = false;
        if (isset($reference[$keySection])) {
            $keyExist = true;
        }
        if ($keyExist) {
            //update
            $this->database->getReference('mst_section/' . $keySection)->update($data);
            //get data
            $currentSection = $this->database->getReference('mst_section/' . $keySection)->getValue();
            //get name of id in topic
            $listTopic = $this->database->getReference('mst_topic')->getValue();
            if (isset($listTopic[$data['tp_id']])) {
                $currentSection['tp_vietnamese'] = $listTopic[$data['tp_id']]['tp_vietnamese'];
            }
            $error = false;
            $json = json_encode($currentSection);
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
