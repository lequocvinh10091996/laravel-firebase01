<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use League\Csv\Writer;
use SplTempFileObject;
use League\Csv\CharsetConverter;
use ZipArchive;
use Illuminate\View\View;

class SearchController extends BaseController
{
    public function index() {
        $jsonTopic = array();
        $listTopic = $this->database->getReference('mst_topic')->getValue();

        if ($listTopic) {
            foreach($listTopic as $key => $value){
                $listTopic[$key]['tp_key'] = $key;
            }
            $jsonTopic = json_encode($listTopic);
        }
        
        $jsonTerminology = array();
        $listTerminology = $this->database->getReference('mst_translate_mean')->getValue();

        if ($listTerminology) {
            $jsonTerminology = json_encode($listTerminology);
        }
        return response([
            'error' => false,
            'data' => compact('listTopic', $jsonTopic, 'listTerminology', $jsonTerminology)
        ], 200);
    }

    public function store(Request $request) {
        $listSectionQuery = array();
        $listTerminologyQuery = array();
        $error = true;
        $json = "";
        if (!empty($request->topicData)) {
            $listSection = $this->database->getReference('mst_section')->getValue();
            foreach ($listSection as $key => $value) {
                if (in_array($value['tp_id'], $request->topicData)) {
                    $listSectionQuery[] = $key;
                }
            }
            if (!empty($listSectionQuery)) {
                $listTerminology = $this->database->getReference('mst_translate_mean')->getValue();
                foreach ($listTerminology as $key => $value) {
                    if (in_array($value['sec_id'], $listSectionQuery)) {
                        $listTerminologyQuery[] = $value;
                    }
                }
            }//print_r($listTerminologyQuery);die;
        }

//        foreach($listSection as $key){
//            $listSection[] = $this->database->getReference('mst_translate_mean/' . $key)->getValue();
//        }
//        $error = true;
//        $json = null;
//        $key = null;
//        $data = array(
//            'sec_id' => $request->sec_id,
//            'tm_english_translate' => "$request->tm_english_translate",
//            'tm_japanese_translate' => $request->tm_japanese_translate,
//            'tm_japanese_higarana' => $request->tm_japanese_higarana,
//            'tm_vietnamese_translate' => $request->tm_vietnamese_translate,
//            'tm_example' => $request->tm_example,
//            'tm_insert_user' => session('acc_username'),
//            'tm_flag' => 1,
//        );
//        //insert
//        $terminologyId = $this->database->getReference('mst_translate_mean')->push($data)->getKey();
//        //get data
//        if ($terminologyId) {
//            $currentTerminology = $this->database->getReference('mst_translate_mean/' . $terminologyId)->getValue();
//            //get name of id in config type
//            $listSection = $this->database->getReference('mst_section')->getValue();
//            if (isset($listSection[$data['sec_id']])) {
//                $currentTerminology['sec_vietnamese'] = $listSection[$data['sec_id']]['sec_vietnamese'];
//            }
//            
//            $error = false;
//            $json = json_encode($currentTerminology);
//            $key = $terminologyId;
//        }
        if(!empty($listTerminologyQuery)){
            $error = false;
            $json = json_encode($listTerminologyQuery);
//            print_r($json);die;
        }
        return response([
            'error' => $error,
            'data' => $json,
        ], 200);
    }
}
