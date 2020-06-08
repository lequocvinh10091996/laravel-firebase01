<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use League\Csv\Writer;
use SplTempFileObject;
use League\Csv\CharsetConverter;
use ZipArchive;
use Illuminate\View\View;

class ExportController extends BaseController
{
    public function index() {
        
    }

    public function export(Request $request) {
        $error = true;
        $zipname = 'export_data_web_dictionary'.date("Y-m-d").'.zip';
        $zip = new ZipArchive();
        $zip->open($zipname, ZipArchive::CREATE);
        $data = array(
//            'mst_account' => $request->mst_account,
            'mst_translate_mean' => $request->mst_translate_mean,
            'mst_section' => $request->mst_section,
            'mst_topic' => $request->mst_topic,
        );
//        if ($data['mst_account']) {
//            $csvname = "mst_account_" . date("Y-m-d") . ".csv";
//            $csv = Writer::createFromFileObject(new SplTempFileObject());
//
//            $listAccount = $this->database->getReference('mst_account')->getValue();
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
//            $zip->addFromString($csvname, $csv->getContent());
//            $error = false;
//        }
        
        if ($data['mst_translate_mean']) {
            $csvname = "terminology_" . date("Y-m-d") . ".csv";
            $csv = Writer::createFromFileObject(new SplTempFileObject());

            $listTerminology = $this->database->getReference('mst_translate_mean')->getValue();
//            $listSection = $this->database->getReference('mst_section')->getValue();
            $csv->insertOne(['Section id', 'Japanese', 'Higarana', 'Vietnamese', 'English', 'Example', 'User']);
            
            if ($listTerminology) {
                foreach ($listTerminology as $key => $value) {
//                    if (isset($listSection[$value['sec_id']])) {
//                        $listTerminology[$key]['sec_vietnamese'] = $listSection[$value['sec_id']]['sec_vietnamese'];
//                        unset($listTerminology[$key]['sec_id']);
                        $listTerminologyExport[$key] = array(
                            'sec_id' => $listTerminology[$key]['sec_id'],
                            'tm_japanese_translate' => $listTerminology[$key]['tm_japanese_translate'],
                            'tm_japanese_higarana' => $listTerminology[$key]['tm_japanese_higarana'],
                            'tm_vietnamese_translate' => $listTerminology[$key]['tm_vietnamese_translate'],
                            'tm_english_translate' => $listTerminology[$key]['tm_english_translate'],
                            'tm_example' => $listTerminology[$key]['tm_example'],
                            'tm_insert_user' => $listTerminology[$key]['tm_insert_user']
                        );
//                    }
                }
            }

            $csv->insertAll($listTerminologyExport);
//            $csv->output($csvname);
            $zip->addFromString($csvname, $csv->getContent());
            $error = false;
        }
        
        if ($data['mst_section']) {
            $csvname = "section_" . date("Y-m-d") . ".csv";
            $csv = Writer::createFromFileObject(new SplTempFileObject());

            $listSection = $this->database->getReference('mst_section')->getValue();
//            $listTopic = $this->database->getReference('mst_topic')->getValue();
            $csv->insertOne(['Topic id', 'Section vietnamese', 'Section japanese', 'Section description']);

            if ($listSection) {
                foreach ($listSection as $key => $value) {
//                    if (isset($listTopic[$value['tp_id']])) {
//                        $listSection[$key]['tp_vietnamese'] = $listTopic[$value['tp_id']]['tp_vietnamese'];
                        $listSectionExport[$key] = array(
                            'tp_id' => $listSection[$key]['tp_id'],
                            'sec_vietnamese' => $listSection[$key]['sec_vietnamese'],
                            'sec_japanese' => $listSection[$key]['sec_japanese'],
                            'sec_description' => $listSection[$key]['sec_description'],
                        );
//                    }
                }
            }
            $csv->insertAll($listSectionExport);
            $zip->addFromString($csvname, $csv->getContent());
            $error = false;
        }
        
        if ($data['mst_topic']) {
            $csvname = "topic_" . date("Y-m-d") . ".csv";
            $csv = Writer::createFromFileObject(new SplTempFileObject());

            $listTopic = $this->database->getReference('mst_topic')->getValue();
            $csv->insertOne(['Topic vietnamese', 'Topic japanese', 'Topic description']);

            if ($listTopic) {
                foreach ($listTopic as $key => $value) {
                    $listTopicExport[$key] = array(
                        'tp_vietnamese' => $listTopic[$key]['tp_vietnamese'],
                        'tp_japanese' => $listTopic[$key]['tp_japanese'],
                        'tp_description' => $listTopic[$key]['tp_description'],
                    );
                }
            }
            $csv->insertAll($listTopicExport);
            $zip->addFromString($csvname, $csv->getContent());
            $error = false;
        }

        if ($error) {
            return back()->with('error', 'Export failed, check data to export !');
        } else {
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $zipname);
            header('Content-Length: ' . filesize($zipname));
            readfile($zipname);

            // remove the zip archive
            // you could also use the temp file method above for this.
            unlink($zipname);
        }
    }
}
