<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class TopicController extends BaseController
{
    public function index() {
        $json = array();
        $listTopic = $this->database->getReference('mst_topic')->getValue();
        if($listTopic){
          $json = json_encode($listTopic);   
        } 
        return response([
            'error' => false,
            'data' => compact('listTopic', $json)
        ], 200);
    }

    public function store(Request $request) {
        $error = true;
        $json = null;
        $key = null;
        $data = array(
            'tp_vietnamese' => $request->tp_vietnamese,
            'tp_japanese' => $request->tp_japanese,
            'tp_description' => $request->tp_description,
            'tp_flag' => 1,
        );
        //insert
        $topicId = $this->database->getReference('mst_topic')->push($data)->getKey();
        //get data
        if ($topicId) {
            $currentConfigType = $this->database->getReference('mst_topic/' . $topicId)->getValue();
            $error = false;
            $json = json_encode($currentConfigType);
            $key = $topicId;
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
        $keyTopic = isset($request->keyTopic) ? $request->keyTopic : NULL;
        if (!$keyTopic) {
            return response([
                'error' => true,
                'data' => 'Key topic not exist !'
            ], 200);
        }
        $data = array(
            'tp_vietnamese' => $request->tp_vietnamese,
            'tp_japanese' => $request->tp_japanese,
            'tp_description' => $request->tp_description,
            'acc_flag' => 1,
        );
        //check key exist
        $reference = $this->database->getReference('mst_topic')->getValue();
        $keyExist = false;
        if (isset($reference[$keyTopic])) {
            $keyExist = true;
        }
        if ($keyExist) {
            //update
            $this->database->getReference('mst_topic/' . $keyTopic)->update($data);
            //get data
            $currentConfigType = $this->database->getReference('mst_topic/' . $keyTopic)->getValue();
            $error = false;
            $json = json_encode($currentConfigType);
        }
        return response([
            'error' => $error,
            'data' => $json
        ], 200);
    }

    public function delete(Request $request) {
        $keyTopic = isset($request->keyTopic) ? $request->keyTopic : NULL;
        if (!$keyTopic) {
            return response([
                'error' => true,
                'data' => 'Key topic not exist !'
            ], 200);
        }
        //delete
        $this->database->getReference('mst_topic/' . $keyTopic)->remove();
        return response([
            'error' => false,
        ], 200);
    }
}
