<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use League\Csv\Writer;
use SplTempFileObject;
use League\Csv\CharsetConverter;
use ZipArchive;
use Illuminate\View\View;
use League\Csv\Reader;
use League\Csv\Statement;
use DateTime;

class ImportController extends BaseController
{
    const DELETED = 1;
    const NOT_DELETED = 0;

    protected $mst_section = [
        'tp_id' => 'Topic id',
        'sec_vietnamese' => 'Section vietnamese',
        'sec_japanese'   => 'Section japanese',
        'sec_description' => 'Section description',
    ];
    
    protected $mst_translate_mean = [
        'sec_id' => 'Section id',
        'tm_english_translate' => 'English',
        'tm_example'   => 'Example',
        'tm_insert_user' => 'User',
        'tm_japanese_higarana' => 'Higarana',
        'tm_japanese_translate' => 'Japanese',
        'tm_vietnamese_translate' => 'Vietnamese',
    ];
    
    protected $mst_topic = [
        'tp_description' => 'Topic description',
        'tp_japanese' => 'Topic japanese',
        'tp_vietnamese'   => 'Topic vietnamese',
    ];


    public function index() {
        
    }

    public function import(Request $request) {
        $error = true;
        $jsonListImport = array();
        
        if($request->hasFile('mst_translate_mean') || $request->hasFile('mst_section') || $request->hasFile('mst_topic')){
            $this->validate($request, [
                'mst_translate_mean' => 'file',
                'mst_section' => 'file',
                'mst_topic' => 'file',
            ]);
            $data = array(
                'mst_topic' => $request->mst_topic,
                'mst_section' => $request->mst_section,
                'mst_translate_mean' => $request->mst_translate_mean,
            );
            foreach ($data as $key => $name) {
                if (!empty($name)) {
                    $name->move('upload/temp', $name->getClientOriginalName());
                    if (file_exists('upload/temp/' . $name->getClientOriginalName())) {
                        rename('upload/temp/' . $name->getClientOriginalName(), 'upload/temp/' . md5($name->getClientOriginalName()));                  
                        $data[$key]->nameFile = md5($name->getClientOriginalName());
                    }
                }
            }

            foreach ($data as $key => $name) {
                $importSuccess[$key] = 0;
                $importFailed[$key] = 0;
                if (isset($data[$key]->nameFile)) {
                    $csv = Reader::createFromPath('upload/temp/' . $data[$key]->nameFile)
                            ->setHeaderOffset(0);
                    $checkColumn = false;
                    if ($key == 'mst_topic') {
                        foreach ($csv as $record) {
                            if (isset($record['Topic vietnamese']) && isset($record['Topic japanese']) && isset($record['Topic description'])) {
                                if (!empty($record['Topic vietnamese']) && !empty($record['Topic japanese'])) {
                                    foreach ($record as $keyRecord => $valueRecord) {
                                        $keyRecordSearch = array_search($keyRecord, $this->$key);
                                        if ($keyRecordSearch) {
                                            $record[$keyRecordSearch] = $valueRecord;
                                            unset($record[$keyRecord]);
                                            $checkColumn = true;
                                        } else {
                                            $checkColumn = false;
                                        }
                                    }
                                    if ($checkColumn == true) {
                                        $firstId = 1;
                                        $record['tp_flag'] = 1;
                                        $lastId = $this->database->getReference($key)
                                        ->orderByChild('tp_id')
                                        // limits the result to the last 10 children (in this case: the 10 tallest persons)
                                        ->limitToLast(1)
                                        ->getValue();
                                        if ($lastId) {
                                            $record['tp_id'] = $lastId[key($lastId)]['tp_id'] + 1;
                                        } else {
                                            $record['tp_id'] = $firstId;
                                        }
                                        $this->database->getReference($key)->push($record);
                                        $importSuccess[$key] ++;
                                    } else {
                                        $importFailed[$key] ++;
                                    }
                                } else {
                                    $importFailed[$key] ++;
                                }
                            }
                        }
                    } elseif ($key == 'mst_section') {
                        foreach ($csv as $record) {
                            if (isset($record['Topic id']) && isset($record['Section vietnamese']) && isset($record['Section japanese']) && isset($record['Section description'])) {
//                                $topic = $this->database->getReference('mst_topic/'.$record['Topic id'])->getValue();
                                $topic = $this->database->getReference('mst_topic')
                                        // order the reference's children by the values in the field 
                                        ->orderByChild('tp_id')
                                        // returns all persons being exactly 
                                        ->equalTo((int)$record['Topic id'])
                                        ->getValue();
                                if (!empty($record['Section vietnamese']) && !empty($record['Section japanese']) && !empty($topic)) {
                                    foreach ($record as $keyRecord => $valueRecord) {
                                        $keyRecordSearch = array_search($keyRecord, $this->$key);
                                        if ($keyRecordSearch) {
                                            $record[$keyRecordSearch] = $valueRecord;
                                            unset($record[$keyRecord]);
                                            $checkColumn = true;
                                        } else {
                                            $checkColumn = false;
                                        }
                                    }
                                    if ($checkColumn == true) {
                                        $firstId = 1;
                                        $record['sec_flag'] = 1;
                                        $lastId = $this->database->getReference($key)
                                        ->orderByChild('sec_id')
                                        // limits the result to the last 10 children (in this case: the 10 tallest persons)
                                        ->limitToLast(1)
                                        ->getValue();
                                        if ($lastId) {
                                            $record['sec_id'] = $lastId[key($lastId)]['sec_id'] + 1;
                                        } else {
                                            $record['sec_id'] = $firstId;
                                        }
                                        $this->database->getReference($key)->push($record);
                                        $importSuccess[$key] ++;
                                    } else {
                                        $importFailed[$key] ++;
                                    }
                                } else {
                                    $importFailed[$key] ++;
                                }
                            }
                        }
                    } elseif ($key == 'mst_translate_mean') {
                        foreach ($csv as $record) {
                            if (isset($record['Section id']) && isset($record['Japanese']) && isset($record['Higarana']) && isset($record['Vietnamese']) && isset($record['English']) && isset($record['User'])) {
//                                $section = $this->database->getReference('mst_section/'.$record['Section id'])->getValue();
                                $section = $this->database->getReference('mst_section')
                                        // order the reference's children by the values in the field 
                                        ->orderByChild('sec_id')
                                        // returns all persons being exactly 
                                        ->equalTo((int)$record['Section id'])
                                        ->getValue();
                                if (!empty($record['Japanese']) && !empty($record['Higarana']) && !empty($record['Vietnamese']) && !empty($section)) {
                                    foreach ($record as $keyRecord => $valueRecord) {
                                        $keyRecordSearch = array_search($keyRecord, $this->$key);
                                        if ($keyRecordSearch) {
                                            $record[$keyRecordSearch] = $valueRecord;
                                            unset($record[$keyRecord]);
                                            $checkColumn = true;
                                        } else {
                                            $checkColumn = false;
                                        }
                                    }
                                    if ($checkColumn == true) {
                                        $firstId = 1;
                                        $record['tm_flag'] = 1;
                                        $lastId = $this->database->getReference($key)
                                        ->orderByChild('tm_id')
                                        // limits the result to the last 10 children (in this case: the 10 tallest persons)
                                        ->limitToLast(1)
                                        ->getValue();
                                        if ($lastId) {
                                            $record['tm_id'] = $lastId[key($lastId)]['tm_id'] + 1;
                                        } else {
                                            $record['tm_id'] = $firstId;
                                        }
                                        $this->database->getReference($key)->push($record);
                                        $importSuccess[$key] ++;
                                    } else {
                                        $importFailed[$key] ++;
                                    }
                                } else {
                                    $importFailed[$key] ++;
                                }
                            }
                        }
                    }
                }
                if ($importSuccess[$key] > 0) {
                    $listImport[$key] = $key;
                    $listImport['count_success_'.$key] = $importSuccess[$key];
                    $listImport['count_failed_'.$key] = $importFailed[$key];
                    $jsonListImport = json_encode($listImport);
                    $error = false;
                }
            }
        }
        return response([
            'error' => $error,
            'listImport' => $jsonListImport,
        ], 200);
    }
    
    public function delete(Request $request) {
        $error = true;
        $listTableDelete = array();
        $data = array(
            'mst_translate_mean' => $request->mst_translate_mean,
            'mst_section' => $request->mst_section,
            'mst_topic' => $request->mst_topic,
        );
        
        if ($data['mst_translate_mean']) {
            $result = $this->database->getReference('mst_translate_mean')->getSnapshot()->exists();
            $listTableDelete['mst_translate_mean'] = self::NOT_DELETED;
            if($result){
                $this->database->getReference('mst_translate_mean')->remove();
                $listTableDelete['mst_translate_mean'] = self::DELETED;
                $error = false;
            }
        } 
        
        if ($data['mst_section']) {
            $result = $this->database->getReference('mst_section')->getSnapshot()->exists();
            $listTableDelete['mst_section'] = self::NOT_DELETED;
            if($result){
                $this->database->getReference('mst_section')->remove();
                $listTableDelete['mst_section'] = self::DELETED;
                $error = false;
            }
        }
        
        if ($data['mst_topic']) {
            $result = $this->database->getReference('mst_topic')->getSnapshot()->exists();
            $listTableDelete['mst_topic'] = self::NOT_DELETED;
            if($result){
                $this->database->getReference('mst_topic')->remove();
                $listTableDelete['mst_topic'] = self::DELETED;
                $error = false;
            }
        }
        
        $json = json_encode($listTableDelete);
        return response([
            'error' => $error,
            'data' => $json
        ], 200);
    }
}
