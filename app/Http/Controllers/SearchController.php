<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

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
        $listTerminologyValue = array();
        $error = true;
        $json = "";
        if (!empty($request->topicData)) {
            $error = false;
            foreach ($request->topicData as $topic){
                $listSectionQuery[] = $this->database->getReference('mst_section')
                // order the reference's children by the values in the field 
                ->orderByChild('tp_id')
                // returns all persons being exactly 
                ->equalTo($topic)
                ->getValue();
            }
            //get key of section
            foreach( $listSectionQuery as $keys => $values){
                foreach($values as $key => $value){
                    $listSectionKey[] = $value['sec_id'];
                }
            }
            
            if (!empty($listSectionKey)) {
                foreach ($listSectionKey as $section) {
                    $listTerminologyQuery[] = $this->database->getReference('mst_translate_mean')
                    // order the reference's children by the values in the field 
                    ->orderByChild('sec_id')
                    // returns all persons being exactly 
                    ->equalTo((string)$section)
                    ->getValue();
                }
                //get value of terminology
                foreach ($listTerminologyQuery as $keys => $values) {
                    foreach ($values as $key => $value) {
                        $listTerminologyValue[] = $value;
                    }
                }
            }
        }
        if(!empty($listTerminologyValue)){
            $json = json_encode($listTerminologyValue);
        }
        return response([
            'error' => $error,
            'data' => $json,
        ], 200);
    }
}