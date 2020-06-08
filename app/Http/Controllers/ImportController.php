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
//                'mst_account' => 'file',
                'mst_translate_mean' => 'file',
                'mst_section' => 'file',
                'mst_topic' => 'file',
            ]);
            $data = array(
//                'mst_account' => $request->mst_account,
                'mst_translate_mean' => $request->mst_translate_mean,
                'mst_section' => $request->mst_section,
                'mst_topic' => $request->mst_topic,
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
                    if ($key == 'mst_section') {
                        foreach ($csv as $record) {
                            if (isset($record['Topic id']) && isset($record['Section vietnamese']) && isset($record['Section japanese']) && isset($record['Section description'])) {
                                $toppic = $this->database->getReference('mst_topic/'.$record['Topic id'])->getValue();
                                if (!empty($record['Section vietnamese']) && !empty($record['Section japanese']) && !empty($toppic)) {
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
                                        $record['sec_flag'] = 1;
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
                                $section = $this->database->getReference('mst_section/'.$record['Section id'])->getValue();
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
                                        $record['tm_flag'] = 1;
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
                    } elseif ($key == 'mst_topic') {
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
                                        $record['tp_flag'] = 1;
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
}
