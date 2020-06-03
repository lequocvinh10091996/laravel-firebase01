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

    public function query (Request $request) {
        $listSectionQuery = array();
        $listTerminologyQuery = array();
        $error = true;
        $json = "";
        if (!empty($request->topicData)) {
            $error = false;
//            $listSection = $this->database->getReference('mst_section')->getValue();
//            foreach ($listSection as $key => $value) {
//                if (in_array($value['tp_id'], $request->topicData)) {
//                    $listSectionQuery[] = $key;
//                }
//            }
            
            foreach ($request->topicData as $topic){
                $listSectionQuery[] = $this->database->getReference('mst_section')
                // order the reference's children by the values in the field 'height'
                ->orderByChild('tp_id')
                // returns all persons being exactly 
                ->equalTo($topic)
                ->getValue();
            }
            
            foreach( $listSectionQuery as $keys => $values){
                foreach(array_keys($values) as $key => $value){
                    $listSectionKey[] = $value;
                }
            }
            
            if (!empty($listSectionKey)) {
                $listTerminology = $this->database->getReference('mst_translate_mean')->getValue();
                foreach ($listTerminology as $key => $value) {
                    if (in_array($value['sec_id'], $listSectionQuery)) {
                        $listTerminologyQuery[] = $value;
                    }
                }
            }
        }
        if(!empty($listTerminologyQuery)){
            $json = json_encode($listTerminologyQuery);
        }
        return response([
            'error' => $error,
            'data' => $json,
        ], 200);
    }
}
