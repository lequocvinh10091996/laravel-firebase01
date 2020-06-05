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
use DateTime;

class ImportController extends BaseController
{
    protected $mst_section = [
        'tp_id' => 'Topic',
        'sec_vietnamese' => 'Section vietnamese',
        'sec_japanese'   => 'Section japanese',
        'sec_description' => 'Section description',
    ];


    public function index() {
        
    }

    public function import(Request $request) {
        $error = true;
        
        if($request->hasFile('mst_account') || $request->hasFile('mst_translate_mean') || $request->hasFile('mst_section') || $request->hasFile('mst_topic')){
            $this->validate($request, [
                'mst_account' => 'file',
                'mst_translate_mean' => 'file',
                'mst_section' => 'file',
                'mst_topic' => 'file',
            ]);
            $data = array(
                'mst_account' => $request->mst_account,
                'mst_translate_mean' => $request->mst_translate_mean,
                'mst_section' => $request->mst_section,
                'mst_topic' => $request->mst_topic,
            );
//            $date  = new DateTime();
            foreach ($data as $key => $name) {
                if (!empty($name)) {
                    $name->move('upload/temp', $name->getClientOriginalName());
//                    rename('upload/temp/'.$name->getClientOriginalName(), 'upload/temp/'.$date->format('Y').$date->format('m').$date->format('d').$date->format('h').$date->format('i').$date->format('s').$name->getClientOriginalName());
//                    $name->move('upload/temp', $name->getClientOriginalName());
                    if (file_exists('upload/temp/' . $name->getClientOriginalName())) {
                        rename('upload/temp/' . $name->getClientOriginalName(), 'upload/temp/' . md5($name->getClientOriginalName()));                  
                        $data[$key]->nameFile = md5($name->getClientOriginalName());
                    }
                }
            }

            foreach ($data as $key => $name) {
                if (isset($data[$key]->nameFile)) {
                    $csv = Reader::createFromPath('upload/temp/' . $data[$key]->nameFile)
                            ->setHeaderOffset(0);
                    $checkColumn = false;
                    foreach ($csv as $record => $value) {
                        print_r($record);
                        foreach (array_keys($value) as $index => $keyRecord){
                            if (in_array($keyRecord, $this->$key)) {
                                $checkColumn = true;
                            } else{
                                $checkColumn = false;
                            }
                        }
                        break;
                    }die;
                    
                    if ($checkColumn == true) {
                        foreach ($csv as $record) {
                            $this->database->getReference($key)->push($record);
                        }
                        print_r('success');die;
                    }
                    print_r('error');die;
                }
            }
            $error = false;
        }
        return response([
            'error' => $error,
//            'data' => $json,
        ], 200);
    }
}
