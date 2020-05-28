<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use League\Csv\Writer;
use SplTempFileObject;
use League\Csv\CharsetConverter;

class ExportController extends BaseController
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
        $data = array(
            'mst_account' => $request->mst_account,
            'mst_translate_mean' => $request->mst_translate_mean,
            'mst_section' => $request->mst_section,
            'mst_topic' => $request->mst_topic,
        );
        
        if ($data['mst_account']) {
            $csvname = "mst_account-" . date("Y-m-d") . ".csv";
            $csv = Writer::createFromFileObject(new SplTempFileObject());

            $listAccount = $this->database->getReference('mst_account')->getValue();
            
            $csv->insertOne(['Username', 'Email']);

            if ($listAccount) {
                foreach ($listAccount as $key => $value) {
                    $listAccountExport[$key] = array(
                        'acc_username' => $listAccount[$key]['acc_username'],
                        'acc_email' => $listAccount[$key]['acc_email'],
                    );
                }
            }

            $csv->insertAll($listAccountExport);
            $csv->output($csvname);
            $error = false;
        }
        
//        if ($data['mst_translate_mean']) {
//            $csvname = "mst_translate_mean-" . date("Y-m-d") . ".csv";
//            $csv = Writer::createFromFileObject(new SplTempFileObject());
//
//            $listAccount = $this->database->getReference('mst_translate_mean')->getValue();
//            
//            $csv->insertOne(['Username', 'Email']);
//
//            if ($listAccount) {
//                foreach ($listAccount as $key => $value) {
//                    $listAccountExport[$key] = array(
//                        'acc_username' => $listAccount[$key]['acc_username'],
//                        'acc_email' => $listAccount[$key]['acc_email'],
//                    );
//                }
//            }
//
//            $csv->insertAll($listAccountExport);
//            $csv->output($csvname);
//            $error = false;
//        }
//        
//        if ($data['mst_section']) {
//            $csvname = "mst_section-" . date("Y-m-d") . ".csv";
//            $csv = Writer::createFromFileObject(new SplTempFileObject());
//
//            $listAccount = $this->database->getReference('mst_section')->getValue();
//            
//            $csv->insertOne(['Username', 'Email']);
//
//            if ($listAccount) {
//                foreach ($listAccount as $key => $value) {
//                    $listAccountExport[$key] = array(
//                        'acc_username' => $listAccount[$key]['acc_username'],
//                        'acc_email' => $listAccount[$key]['acc_email'],
//                    );
//                }
//            }
//
//            $csv->insertAll($listAccountExport);
//            $csv->output($csvname);
//            $error = false;
//        }
//        
//        if ($data['mst_topic']) {
//            $csvname = "mst_topic-" . date("Y-m-d") . ".csv";
//            $csv = Writer::createFromFileObject(new SplTempFileObject());
//
//            $listAccount = $this->database->getReference('mst_topic')->getValue();
//            
//            $csv->insertOne(['Username', 'Email']);
//
//            if ($listAccount) {
//                foreach ($listAccount as $key => $value) {
//                    $listAccountExport[$key] = array(
//                        'acc_username' => $listAccount[$key]['acc_username'],
//                        'acc_email' => $listAccount[$key]['acc_email'],
//                    );
//                }
//            }
//
//            $csv->insertAll($listAccountExport);
//            $csv->output($csvname);
//            $error = false;
//        }
        
        return response([
            'error' => $error,
//            'data' => '$json',
//            'key' => $key
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
